 {{-- PRODUCTS --}}
@php
$activeTab = 'requirements';
@endphp
@foreach($project->rfqs as $rfq)
 <div class="mt-1">

    {{-- ITEM --}}
    <a href="{{ route('buyer.projects.requirements', ['project' => $project, 'rfq' => $rfq->id]) }}"
       class="group flex items-center gap-3 px-3 py-2.5 rounded-lg border border-gray-200 bg-gradient-to-b from-white via-gray-50 to-gray-100 shadow-sm
              transition-all duration-200
              hover:bg-black/5 hover:border-gray-300 hover:shadow
              {{ $activeTab === 'requirements' ? 'bg-black/5 border-gray-300' : '' }}">

        {{-- INDEX --}}
        <div class="text-[11px] text-gray-400 w-4 text-right">
            1.
        </div>

        {{-- IMAGE --}}
        <img src="{{ $rfq->image ?? asset('images/no-photo.png') }}"
             class="w-10 h-10 rounded-md object-cover border border-gray-200">

        {{-- CONTENT --}}
        <div class="flex-1 min-w-0">
            <div class="text-sm text-gray-800 truncate">
                {{ $rfq->title }}
            </div>
        </div>

        {{-- ARROW --}}
        <div class="text-gray-300 group-hover:text-gray-500 transition">
            →
        </div>

    </a>

    

</div>

@endforeach