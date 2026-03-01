@extends('dashboard.layout')

@section('dashboard-content')

<div class="max-w-7xl mx-auto space-y-4">

{{-- Header --}}
    <div class="flex justify-between items-center">
        <div>
            <h2 class="text-2xl font-semibold text-gray-900">Company Profile</h2>
            <p class="text-sm text-gray-500">
                Manage manufacturer identity and marketplace listing settings
            </p>
        </div>

        <div class="flex items-center gap-3">
            

           
        </div>
    </div>

@if(session('success'))
<div class="bg-green-50 border border-green-200 text-green-800
            px-4 py-3 rounded-xl text-sm">

    {{ session('success') }}

</div>
@endif


   





{{-- ================= COMPANY PROFILE (STRUCTURED COMPACT CARD) ================= --}}
<div class="bg-white border rounded-2xl shadow-sm p-8 space-y-8">


    {{-- ================= HEADER ================= --}}
    <div class="flex justify-between items-center">
        <div class="text-sm text-gray-400 uppercase tracking-wider">
            Company Profile Overview
        </div>

        <a href="{{ route('manufacturer.company.profile') }}"
           class="px-4 py-2 text-sm rounded-lg border hover:bg-gray-50 transition">
            Edit Profile
        </a>
    </div>


    {{-- =====================================================
        IDENTITY & DESCRIPTION
    ===================================================== --}}
    <div class="space-y-6">

        <div class="grid lg:grid-cols-3 gap-8">

            {{-- Logo --}}
            <div>
                <div class="text-xs text-gray-400 uppercase mb-2">Logo</div>

                <div class="w-24 h-24 rounded-xl overflow-hidden border bg-gray-50">
                    
                    <img
                        src="{{ $company->logo()?->cdn_url ?? asset('images/no-logo.png') }}"
                        class="w-full h-full object-cover"
                    >
                </div>
            </div>

            {{-- Core Info --}}
            <div class="lg:col-span-2 grid md:grid-cols-2 gap-x-8 gap-y-5">

                <div>
                    <div class="text-xs text-gray-400 uppercase">Company Name</div>
                    <div class="font-semibold text-gray-900">
                        {{ $company->name }}
                    </div>
                </div>

                <div>
                    <div class="text-xs text-gray-400 uppercase">Registration Country</div>
                    <div class="text-gray-900">
                        {{ $company->country?->name ?? '—' }}
                    </div>
                </div>

                <div class="md:col-span-2">
                    <div class="text-xs text-gray-400 uppercase">Short Description</div>
                    <div class="text-sm text-gray-900">
                        {{ $company->short_description ?? '—' }}
                    </div>
                </div>

                <div class="md:col-span-2">
                    <div class="text-xs text-gray-400 uppercase">Company Description</div>
                    <div class="text-sm text-gray-700 leading-relaxed">
                        {!! $company->description ?? '—' !!}
                    </div>
                </div>

            </div>

        </div>
    </div>


    {{-- =====================================================
        GENERAL INFORMATION
    ===================================================== --}}
    <div class="border-t pt-6 space-y-6">

        <div class="text-xs text-gray-400 uppercase tracking-wider">
            General Information
        </div>

        <div class="grid lg:grid-cols-2 gap-10">

            <div class="space-y-4">

                <div>
                    <div class="text-xs text-gray-400 uppercase mb-1">About Us</div>
                    <div class="text-sm text-gray-700 leading-relaxed">
                        {!! $company->about_us_description ?? '—' !!}
                    </div>
                </div>

                <div>
                    <div class="text-xs text-gray-400 uppercase mb-2">
                        Export Markets
                    </div>

                    <div class="flex flex-wrap gap-2">
                        @forelse($company->exportMarkets as $market)
                            <span class="px-3 py-1 text-xs bg-gray-100 border rounded-full">
                                {{ $market->translation?->name ?? $market->slug }}
                            </span>
                        @empty
                            <span class="text-gray-400 text-sm">—</span>
                        @endforelse
                    </div>
                </div>

            </div>


            {{-- Metrics --}}
            <div class="grid grid-cols-2 gap-5">

                <div>
                    <div class="text-xs text-gray-400 uppercase">Founded</div>
                    <div class="font-semibold text-gray-900">
                        {{ $company->founded_year ?? '—' }}
                    </div>
                </div>

                <div>
                    <div class="text-xs text-gray-400 uppercase">Employees</div>
                    <div class="font-semibold text-gray-900">
                        {{ $company->total_employees ?? '—' }}
                    </div>
                </div>

                <div>
                    <div class="text-xs text-gray-400 uppercase">Export Revenue</div>
                    <div class="font-semibold text-gray-900">
                        {{ $company->annual_export_revenue ? '$'.number_format($company->annual_export_revenue) : '—' }}
                    </div>
                </div>

                <div>
                    <div class="text-xs text-gray-400 uppercase">Registration Capital</div>
                    <div class="font-semibold text-gray-900">
                        {{ $company->registration_capital ? '$'.number_format($company->registration_capital) : '—' }}
                    </div>
                </div>

            </div>

        </div>
    </div>


    {{-- =====================================================
        MANUFACTURING PROFILE
    ===================================================== --}}
    <div class="border-t pt-6 space-y-6">

        <div class="text-xs text-gray-400 uppercase tracking-wider">
            Manufacturing Profile
        </div>

        <div class="space-y-5">

            <div>
                <div class="text-xs text-gray-400 uppercase mb-1">
                    Manufacturing Overview
                </div>

                <div class="text-sm text-gray-700 leading-relaxed">
                    {!! $company->manufacturing_description ?? '—' !!}
                </div>
            </div>


            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-5">

                <div>
                    <div class="text-xs text-gray-400 uppercase">Factory Area</div>
                    <div class="font-semibold text-gray-900">
                        {{ $company->factory_area ? $company->factory_area.' m²' : '—' }}
                    </div>
                </div>

                <div>
                    <div class="text-xs text-gray-400 uppercase">Lines</div>
                    <div class="font-semibold text-gray-900">
                        {{ $company->production_lines ?? '—' }}
                    </div>
                </div>

                <div>
                    <div class="text-xs text-gray-400 uppercase">MOQ</div>
                    <div class="font-semibold text-gray-900">
                        {{ $company->moq ?? '—' }}
                    </div>
                </div>

                <div>
                    <div class="text-xs text-gray-400 uppercase">Capacity</div>
                    <div class="font-semibold text-gray-900">
                        {{ $company->monthly_capacity ?? '—' }}
                    </div>
                </div>

                <div>
                    <div class="text-xs text-gray-400 uppercase">Lead Time</div>
                    <div class="font-semibold text-gray-900">
                        {{ $company->lead_time_days ? $company->lead_time_days.' days' : '—' }}
                    </div>
                </div>

            </div>

        </div>
    </div>

    {{-- =====================================================
        CONTACT INFORMATION
    ===================================================== --}}
    <div class="border-t pt-6 space-y-6">

        <div class="text-xs text-gray-400 uppercase tracking-wider">
            Contact Information
        </div>

        <div class="grid md:grid-cols-2 gap-8">

            <div>
                <div class="text-xs text-gray-400 uppercase">Email</div>
                <div class="font-semibold text-gray-900">
                    {{ $company->email ?? '—' }}
                </div>
            </div>

            <div>
                <div class="text-xs text-gray-400 uppercase">Phone</div>
                <div class="font-semibold text-gray-900">
                    {{ $company->phone ?? '—' }}
                </div>
            </div>

            <div class="md:col-span-2">
                <div class="text-xs text-gray-400 uppercase">Address</div>
                <div class="text-gray-900">
                    {{ $company->address ?? '—' }}
                </div>
            </div>

        </div>
    </div>



</div>





 

{{-- ================= CERTIFICATES BLOCK ================= --}}

<div class="bg-white border rounded-2xl shadow-sm p-6 space-y-6 mt-6">

    <div class="flex justify-between items-center">
        <h3 class="text-sm text-gray-400 uppercase tracking-wider">
            Certificates
        </h3>

        <button
            type="button"
            onclick="openCertificateModal()"
            class="px-4 py-2 text-sm rounded-lg border hover:bg-gray-50 transition">

            + Add Certificate
        </button>
    </div>

    @php
        $certificates = $company->certificatesMedia()->get();
    @endphp

    <div id="certificatesContainer" class="grid md:grid-cols-2 gap-4">

        @foreach($certificates as $certificate)

            @php
                $meta = is_array($certificate->metadata)
                    ? $certificate->metadata
                    : json_decode($certificate->metadata ?? '{}', true);

                $certificateName = $meta['certificate_name'] ?? '';
                $certificateNumber = $meta['certificate_number'] ?? '';
                $validFrom = $meta['valid_from'] ?? '';
                $validUntil = $meta['valid_until'] ?? '';
            @endphp

            <div class="certificate-card border rounded-xl p-4 bg-gray-50 space-y-2">

                <div class="flex justify-between items-start">

                    <div class="space-y-1">

                        <a href="{{ $certificate->cdn_url }}"
                           target="_blank"
                           class="text-sm font-semibold hover:underline block">

                            {{ $certificateName ?: $certificate->original_file_name }}
                        </a>

                        <div class="text-xs text-gray-500 space-y-1">

                            @if($certificateNumber)
                                <div>Number: {{ $certificateNumber }}</div>
                            @endif

                            @if($validFrom)
                                <div>Valid From: {{ $validFrom }}</div>
                            @endif

                            @if($validUntil)
                                <div>Valid Until: {{ $validUntil }}</div>
                            @endif

                            <div class="text-gray-400">
                                Status: {{ ucfirst($certificate->processing_status) }}
                            </div>

                        </div>

                    </div>

                    <button class="delete-certificate-btn text-red-500 text-sm hover:underline"
                            data-id="{{ $certificate->id }}">
                        Delete
                    </button>

                </div>

            </div>

        @endforeach

    </div>

</div>

{{-- ================= CERTIFICATE MODAL ================= --}}

<div id="certificateFormModal"
     class="hidden fixed inset-0 z-50 bg-black/50 flex items-center justify-center">

    <div class="bg-white w-full max-w-lg rounded-2xl shadow-xl p-6 space-y-4">

        <h3 class="text-lg font-semibold">Add Certificate</h3>

        <input id="certificateName" type="text"
               placeholder="Certificate Name"
               class="w-full border rounded-lg p-3 text-sm">

        <input id="certificateNumber" type="text"
               placeholder="Certificate Number"
               class="w-full border rounded-lg p-3 text-sm">

        <div class="grid grid-cols-2 gap-3">

            <input id="validFrom" type="date"
                   class="border rounded-lg p-3 text-sm">

            <input id="validUntil" type="date"
                   class="border rounded-lg p-3 text-sm">

        </div>

        <input id="certificateFile" type="file"
               accept=".jpg,.jpeg,.png,.webp,.pdf"
               class="w-full border rounded-lg p-3 text-sm">

        <div class="flex justify-end gap-3">

            <button onclick="closeCertificateModal()"
                    class="px-4 py-2 text-sm border rounded-lg">
                Cancel
            </button>

            <button onclick="submitCertificate()"
                    class="px-4 py-2 bg-black text-white rounded-lg">
                Upload
            </button>

        </div>

    </div>
</div>

<script>

/* ================= MODAL CONTROL ================= */

function openCertificateModal() {
    document.getElementById('certificateFormModal').classList.remove('hidden');
}

function closeCertificateModal() {
    document.getElementById('certificateFormModal').classList.add('hidden');

    document.getElementById('certificateName').value = '';
    document.getElementById('certificateNumber').value = '';
    document.getElementById('validFrom').value = '';
    document.getElementById('validUntil').value = '';
    document.getElementById('certificateFile').value = '';
}

/* ================= SUBMIT CERTIFICATE ================= */

function submitCertificate() {

    const file = document.getElementById('certificateFile').files[0];

    if (!file) {
        alert("Select certificate file");
        return;
    }

    const metadata = {
        certificate_name: document.getElementById('certificateName').value,
        certificate_number: document.getElementById('certificateNumber').value,
        valid_from: document.getElementById('validFrom').value,
        valid_until: document.getElementById('validUntil').value
    };

    const formData = new FormData();

    formData.append('certificate', file);
    formData.append('metadata', JSON.stringify(metadata));

    fetch("{{ route('manufacturer.certificates.upload') }}", {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': "{{ csrf_token() }}",
            'Accept': 'application/json'
        },
        body: formData
    })
    .then(res => res.json())
    .then(response => {

        if (response.success) {

            closeCertificateModal();
            location.reload();
        } else {
            alert(response.message || 'Upload failed');
        }

    })
    .catch(() => alert("Upload failed"));
}

/* ================= DELETE CERTIFICATE ================= */

document.addEventListener('click', function(e){

    if(e.target?.classList.contains('delete-certificate-btn')){

        deleteCertificate(e.target.dataset.id);
    }

});

function deleteCertificate(id) {

    const url = "{{ route('manufacturer.certificates.delete', ['certificate' => 'CERT_ID']) }}"
        .replace('CERT_ID', id);

    fetch(url, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': "{{ csrf_token() }}",
            'Accept': 'application/json'
        }
    })
    .then(res => res.json())
    .then(data => {

        if (data.success) {

            document.querySelector(
                `.delete-certificate-btn[data-id="${id}"]`
            )?.closest('.certificate-card')?.remove();
        }

    })
    .catch(() => alert('Delete failed'));
}

</script>


{{-- ================= FACTORY PHOTO BLOCK ================= --}}

<div x-data="FactoryUploader()" class="bg-white border rounded-2xl shadow-sm p-8 pt-4 space-y-8">

    <div class="flex justify-between items-center">
        <h3 class="text-sm text-gray-400 uppercase tracking-wider">
            Factory Photos
        </h3>

        <button onclick="openModal('factoryPhotosModal')"
                class="px-4 py-2 text-sm rounded-lg border hover:bg-gray-50 transition">
            + Add Photo
        </button>
    </div>

    <div class="grid md:grid-cols-4 gap-5">

        @forelse($company->factoryPhotos as $photo)

    <div class="relative group">

        <img src="{{ $photo->cdn_url }}"
             class="aspect-square w-full object-cover rounded-xl border">

        <button
            type="button"
            @click="deletePhoto({{ $photo->id }})"
            class="absolute top-2 right-2 opacity-0 group-hover:opacity-100 transition bg-red-600 text-white text-xs px-2 py-1 rounded-lg"
        >
            Delete
        </button>

    </div>

@empty

            <div class="text-sm text-gray-400 col-span-4">
                No factory photos uploaded.
            </div>

        @endforelse

    </div>

</div>
{{-- ================= Factory photo MODAL ================= --}}

<div id="factoryPhotosModal"
     class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/50">

    <div class="bg-white w-full max-w-3xl rounded-2xl shadow-xl overflow-hidden">

        <div class="flex justify-between items-center px-6 py-4 border-b">

            <h3 class="text-lg font-semibold">
                Manage photos
            </h3>

            <button type="button"
                    onclick="closeModal('factoryPhotosModal')"
                    class="text-gray-400 hover:text-gray-600 text-2xl leading-none">

                &times;

            </button>

        </div>


        <div class="p-6 max-h-[70vh] overflow-y-auto">
            @include('dashboard.manufacturer.partials.manage-factoryphotos')
        </div>

    </div>
</div>







 {{-- ================= CATALOG ================= --}}

<div class="bg-white border rounded-2xl shadow-sm p-6 space-y-5 mt-6">

<div>
    <h3 class="text-sm text-gray-400 uppercase tracking-wider">
        Catalog Presentation
    </h3>
    <div class="flex justify-center">
            @include('dashboard.manufacturer.partials.preview-card', [
                'company' => $company
            ])
        </div>
    
</div>
    

</div>
<script>

    function FactoryUploader() {
    return {
        async deletePhoto(id) {

            if (!confirm('Delete this photo?')) return;

            try {

                const response = await fetch(`/factory/photos/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    }
                });

                const data = await response.json();

                console.log('Delete response:', data);

                if (response.ok) {
                    location.reload();
                } else {
                    alert('Delete failed');
                }

            } catch (e) {
                console.error(e);
                alert('Request error');
            }
        }
    }
}

</script>

<script>
function openModal(id){
    document.getElementById(id)?.classList.remove('hidden');
}

function closeModal(id){
    document.getElementById(id)?.classList.add('hidden');
}
</script>



@endsection