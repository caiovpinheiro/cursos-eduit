<?php

namespace App\Services;

use App\Models\Certificate;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class CertificateService
{
    public function listForUser(User $user, int $perPage = 15): LengthAwarePaginator
    {
        $query = Certificate::with(['course']);

        if ($user->isStudent()) {
            $query->where('user_id', $user->id);
        }

        return $query->orderBy('issue_date', 'desc')->paginate($perPage);
    }
}
