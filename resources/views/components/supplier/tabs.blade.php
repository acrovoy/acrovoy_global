@php
    $currentTab = request('tab', 'home');
@endphp

<div class="sticky top-0 bg-gradient-to-r from-[#fdfcf9] via-[#fdfcf9] to-[#fdfcf9] rounded-lg border-b mb-4 shadow-sm">
    <div class="flex justify-center overflow-x-auto scrollbar-hide px-6 space-x-8 text-sm">

        @foreach($tabs as $id => $label)
            <a href="{{ request()->fullUrlWithQuery(['tab' => $id]) }}"
               class="py-1 border-b-2 whitespace-nowrap transition-all duration-200
               {{ $currentTab === $id 
                    ? 'border-gray-500 text-gray-600' 
                    : 'border-transparent text-gray-600 hover:text-gray-900 hover:border-gray-600' }}">

                {{ $label }}
            </a>
        @endforeach

    </div>
</div>