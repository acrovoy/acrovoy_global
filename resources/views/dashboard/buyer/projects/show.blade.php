@extends('dashboard.layout')

@section('dashboard-content')

<div class="space-y-6">

    {{-- Header --}}
<div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
    <div>
        <!-- Ссылка назад -->
        <a href="{{ route('buyer.projects.index') }}"
           class="text-sm text-gray-500 hover:text-gray-700 flex items-center gap-1">
            ← Back to projects
        </a>

        {{-- Title --}}
        <h1 class="text-2xl font-semibold mt-2 text-gray-800">{{ $project->title }}</h1>

        

        <p class="text-sm text-gray-500 mt-1">
            Manage your project details and positions
        </p>
    </div>

    {{-- Actions --}}
    <div class="flex gap-2">
        <a href="{{ route('buyer.projects.edit', $project) }}" class="px-4 py-2 bg-gray-900 text-white rounded-lg hover:bg-gray-800 transition text-sm">
            Edit Project
        </a>
        <form action="{{ route('buyer.projects.destroy', $project) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this project?');">
            @csrf
            @method('DELETE')
            <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-500 transition text-sm">
                Delete Project
            </button>
        </form>
    </div>
</div>


{{-- Status badge под заголовком --}}
        @php
            $statusClasses = [
                'new' => 'bg-blue-100 text-blue-800',
                'in_progress' => 'bg-yellow-100 text-yellow-800',
                'completed' => 'bg-green-100 text-green-800',
                'cancelled' => 'bg-red-100 text-red-800',
            ];
        @endphp
        <span class="inline-block  px-3 py-1 rounded-full text-sm font-medium {{ $statusClasses[$project->status] ?? 'bg-gray-100 text-gray-700' }}">
            {{ ucfirst(str_replace('_', ' ', $project->status)) }}
        </span>


    {{-- Project Info --}}
<div class="bg-white border border-gray-200 rounded-xl p-6 shadow-sm space-y-2">
    <p><strong>Category:</strong> {{ $project->category->name ?? '-' }}</p>
    
    
    
    <div class="flex flex-col gap-4">

    {{-- Description --}}
    <div class="flex flex-col md:flex-row md:items-start gap-2">
        <span class="font-medium text-gray-700 "><strong>Description:</strong></span>
        <span class="text-gray-800">{{ $project->description ?? '-' }}</span>
    </div>

</div>
</div>

    {{-- Items --}}
    <div class="flex items-center justify-between mb-4">
    
    <h2 class="text-lg font-semibold text-gray-800">Items</h2>
    
    <div class="flex items-center gap-3">
    <a href="{{ url('/catalog') }}"
           class="px-3 py-1.5 text-sm border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-100 transition">
            + Add from catalog
        </a>   
    

<a href=""
       class="px-3 py-1.5 bg-gray-900 text-white rounded-lg hover:bg-gray-800 transition text-sm">
        + Add Item
    </a>
        
    </div>

    
</div>

        <div class="overflow-y-auto max-h-[600px] space-y-4 p-2 border border-gray-200 rounded-lg bg-gray-50">
    @forelse ($project->items as $item)
        <div x-data="{ open: false }" class="border border-gray-200 rounded-xl p-4 hover:shadow-md transition relative bg-white">
            <div class="flex items-start gap-4">
                {{-- Главная картинка --}}
                @if($item->media->count())
                    @php
                        $mainImage = $item->media->firstWhere('is_main', 1) ?? $item->media->first();
                    @endphp
                    <img src="{{ asset('storage/' . $mainImage->image_path) }}" 
                         alt="Product Image" 
                         class="w-20 h-20 object-cover rounded-md border border-gray-200">
                @endif

                <div class="flex-1 flex flex-col">
                    {{-- Название продукта --}}
                    <div class="font-medium text-gray-800">{{ $item->product_name ?? '-' }}</div>

                    {{-- Краткая информация --}}
                    <div class="mt-1 text-xs text-gray-500 space-y-1">
                        <div><strong>Quantity:</strong> {{ $item->quantity }}</div>
                        <div><strong>Lead Time:</strong> {{ $item->lead_time_days }} days</div>

                        @if($item->materials->count())
                            <div>
                                <strong>Materials:</strong>
                                {{ $item->materials->pluck('name')->join(', ') }}
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Действия --}}
                <div class="flex flex-col gap-1 text-sm">
                    <a href="" class="text-indigo-600 hover:underline">Edit</a>
                    <form action="" method="POST" class="inline-block" onsubmit="return confirm('Delete this item?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-red-600 hover:underline">Delete</button>
                    </form>
                </div>

                {{-- Стрелка в правом нижнем углу --}}
                <button 
                    @click="open = !open" 
                    class="absolute bottom-2 right-2 text-gray-400 hover:text-gray-700 focus:outline-none">
                    <svg xmlns="http://www.w3.org/2000/svg" 
                         class="h-5 w-5 transform transition-transform duration-300"
                         :class="{'rotate-180': open, 'rotate-0': !open}" 
                         fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </button>
            </div>

            {{-- Полная информация --}}
            <div x-show="open" x-transition class="mt-2 text-xs text-gray-600 space-y-1">
                @if($item->media->count())
                    <div class="flex flex-wrap gap-2">
                        @foreach($item->media->where('is_main', 0) as $media)
                            <img src="{{ asset('storage/' . $media->image_path) }}" 
                                 alt="Product Image" 
                                 class="w-16 h-16 object-cover rounded-md border border-gray-200">
                        @endforeach
                    </div>
                @endif

                {{-- Полные спецификации --}}
                @if($item->specifications->count())
                    <div>
                        <strong>All Specifications:</strong>
                        @foreach($item->specifications as $spec)
                            <div>{{ $spec->parameter }}: {{ $spec->value }}</div>
                        @endforeach
                    </div>
                @endif

                {{-- Полное описание --}}
                @if($item->descriptions->count())
                    <div>
                        <strong>Description:</strong>
                        @foreach($item->descriptions as $desc)
                            <div>{{ $desc->description }}</div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    @empty
        <div class="text-center text-gray-500 py-6">
            No items yet.
        </div>
    @endforelse
</div>










    </div>

</div>

@endsection
