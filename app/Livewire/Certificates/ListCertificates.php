<?php

namespace App\Livewire\Certificates;

use App\Models\Certificate;
use App\Models\User;
use App\Services\CertificateService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

class ListCertificates extends Component
{
    use AuthorizesRequests;
    use WithPagination;

    #[Layout('layouts.app')]
    #[Title('Meus certificados')]
    public function render(CertificateService $certificateService)
    {
        $this->authorize('viewAny', Certificate::class);

        /** @var User $user */
        $user = Auth::user();

        return view('livewire.certificates.list-certificates', [
            'certificates' => $certificateService->listForUser($user, 15),
        ]);
    }
}
