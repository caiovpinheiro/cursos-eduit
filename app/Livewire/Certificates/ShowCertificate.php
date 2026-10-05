<?php

namespace App\Livewire\Certificates;

use App\Models\Certificate;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Attributes\Layout;
use Livewire\Component;

class ShowCertificate extends Component
{
    use AuthorizesRequests;

    public Certificate $certificate;

    public function mount(int $id): void
    {
        $this->certificate = Certificate::with('course')->findOrFail($id);
        $this->authorize('view', $this->certificate);
    }

    #[Layout('layouts.app')]
    public function render()
    {
        $courseTitle = $this->certificate->course?->title ?? 'Certificado';

        return view('livewire.certificates.show-certificate')
            ->title('Certificado · '.$courseTitle);
    }
}
