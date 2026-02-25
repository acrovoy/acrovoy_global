<?php

namespace App\View\Components\Supplier;

use Illuminate\View\Component;

class CertificateCard extends Component
{
    public $certificate;

    public function __construct($certificate)
    {
        $this->certificate = $certificate;
    }

    public function render()
    {
        return view('components.supplier.certificate-card');
    }

    public function extension(): string
    {
        return strtolower(pathinfo($this->certificate->file_path, PATHINFO_EXTENSION));
    }

    public function isImage(): bool
    {
        return in_array($this->extension(), [
            'jpg', 'jpeg', 'png', 'webp'
        ]);
    }

    public function isPdf(): bool
    {
        return $this->extension() === 'pdf';
    }
}