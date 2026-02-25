@php
$ext = strtolower(pathinfo($certificate->file_path, PATHINFO_EXTENSION));

$isImage = in_array($ext, ['jpg','jpeg','png','webp']);
$isPdf = $ext === 'pdf';
@endphp

<div class="border rounded-xl p-3 w-32 flex flex-col items-center gap-2 bg-white shadow-sm hover:shadow-md transition">

    <a href="{{ asset('storage/'.$certificate->file_path) }}"
       target="_blank"
       class="block text-center w-full">

        {{-- IMAGE --}}
        @if($isImage)

            <img src="{{ asset('storage/'.$certificate->file_path) }}"
                 class="w-full h-20 object-contain rounded">

        {{-- PDF --}}
        @elseif($isPdf)

            <div class="w-full h-20 flex items-center justify-center bg-gray-100 rounded">
                <img src="{{ asset('images/pdf-icon.png') }}"
                     class="w-10 h-10 object-contain">
            </div>

        {{-- OTHER FILE TYPES --}}
        @else

            <div class="w-full h-20 flex items-center justify-center bg-gray-100 rounded text-xs text-gray-500">
                {{ strtoupper($ext) }}
            </div>

        @endif

        <div class="text-xs truncate mt-2">
            {{ $certificate->name }}
        </div>

    </a>

</div>