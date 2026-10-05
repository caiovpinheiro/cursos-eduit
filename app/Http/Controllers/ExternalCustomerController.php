<?php

namespace App\Http\Controllers;

use App\Models\ExternalCustomer;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ExternalCustomerController extends Controller
{
    /**
     * Bulk insert CPFs from external database
     * This route will be called by a CRON job daily to sync new CPFs
     * Only adds CPFs that don't already exist (prevents duplicates)
     */
    public function bulkSync(Request $request): JsonResponse
    {
        $request->validate([
            'cpfs' => 'required|array',
            'cpfs.*' => 'required|string|distinct',
        ]);

        $cpfs = $request->input('cpfs', []);
        $added = 0;
        $skipped = 0;
        $errors = [];

        DB::beginTransaction();
        try {
            foreach ($cpfs as $cpf) {
                // Check if CPF already exists
                if (ExternalCustomer::where('cpf', $cpf)->exists()) {
                    $skipped++;
                    continue;
                }

                try {
                    ExternalCustomer::create(['cpf' => $cpf]);
                    $added++;
                } catch (\Exception $e) {
                    $errors[] = [
                        'cpf' => $cpf,
                        'error' => $e->getMessage(),
                    ];
                    Log::warning('Failed to add CPF to external_customers', [
                        'cpf' => $cpf,
                        'error' => $e->getMessage(),
                    ]);
                }
            }

            DB::commit();

            return response()->json([
                'message' => 'CPFs synchronized successfully',
                'added' => $added,
                'skipped' => $skipped,
                'total_received' => count($cpfs),
                'errors' => $errors,
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to sync CPFs', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'message' => 'Failed to synchronize CPFs',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get all external customers (for admin purposes)
     */
    public function index(Request $request): JsonResponse
    {
        $perPage = $request->get('per_page', 15);
        $customers = ExternalCustomer::orderBy('id', 'desc')
            ->paginate($perPage);

        return response()->json([
            'data' => $customers->items(),
            'current_page' => $customers->currentPage(),
            'last_page' => $customers->lastPage(),
            'per_page' => $customers->perPage(),
            'total' => $customers->total(),
        ]);
    }

    /**
     * Check if a CPF exists in external customers
     */
    public function check(Request $request): JsonResponse
    {
        $request->validate([
            'cpf' => 'required|string',
        ]);

        $exists = ExternalCustomer::where('cpf', $request->cpf)->exists();

        return response()->json([
            'cpf' => $request->cpf,
            'exists' => $exists,
        ]);
    }
}

