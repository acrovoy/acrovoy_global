@extends('dashboard.layout')

@section('dashboard-content')
<h2 class="text-2xl font-bold">Welcome, {{ auth()->user()->supplier->name ?? 'Manufacturer' }}</h2>
<p class="text-gray-600 mb-6">
    Maintain your reputation. Stimulate your growth as a supplier..
</p>


@php
    $supplier = auth()->user()->supplier;

    $score = $supplier->reputation ?? 0;

    // Уровни репутации
    if ($score <= 50) {
        $level = 'Bronze';
        $color = 'bg-yellow-600';
        $textColor = 'text-yellow-700';
        $icon = '';
        $nextLevelScore = 51;
    } elseif ($score <= 120) {
        $level = 'Silver';
        $color = 'bg-gray-400';
        $textColor = 'text-gray-600';
        $icon = '';
        $nextLevelScore = 121;
    } elseif ($score <= 200) {
        $level = 'Gold';
        $color = 'bg-yellow-500';
        $textColor = 'text-yellow-600';
        $icon = '';
        $nextLevelScore = 201;
    } else {
        $level = 'Platinum';
        $color = 'bg-blue-500';
        $textColor = 'text-blue-600';
        $icon = '';
        $nextLevelScore = $score; // максимум
    }

    $progress = ($score / $nextLevelScore) * 100;
    if ($progress > 100) $progress = 100;

    $rating = $supplier->reviews()->avg('rating') ?? 0;
    $rating = round($rating, 1);
@endphp

<div class="bg-white shadow-2xl rounded-2xl p-6 mb-6">
    {{-- Верхний блок: уровень и рейтинг --}}
    <div class="flex items-center justify-between mb-4">
        <div class="flex items-center space-x-2">
            <span class="text-2xl">{{ $icon }}</span>
            <span class="px-4 py-1 rounded-full font-semibold {{ $color }} text-white text-sm tracking-wide">{{ $level }} Supplier</span>
        </div>

        <div class="flex items-center space-x-1">
            @for ($i = 1; $i <= 5; $i++)
                @if ($i <= floor($rating))
                    <svg class="w-5 h-5 fill-current text-yellow-500" viewBox="0 0 20 20">
                        <path d="M10 15l-5.878 3.09L5.36 11.545 1 7.91l6.061-.545L10 2l2.939 5.365L19 7.91l-4.36 3.635 1.238 6.545z"/>
                    </svg>
                @elseif ($i - $rating < 1)
                    <svg class="w-5 h-5 fill-current text-yellow-300" viewBox="0 0 20 20">
                        <path d="M10 15l-5.878 3.09L5.36 11.545 1 7.91l6.061-.545L10 2l2.939 5.365L19 7.91l-4.36 3.635 1.238 6.545z"/>
                    </svg>
                @else
                    <svg class="w-5 h-5 fill-current text-gray-300" viewBox="0 0 20 20">
                        <path d="M10 15l-5.878 3.09L5.36 11.545 1 7.91l6.061-.545L10 2l2.939 5.365L19 7.91l-4.36 3.635 1.238 6.545z"/>
                    </svg>
                @endif
            @endfor
            <span class="ml-2 font-semibold {{ $textColor }}">{{ $rating }}</span>
        </div>
    </div>

    {{-- Прогресс-бар --}}
    <div class="w-full bg-gray-200 rounded-full h-6 mb-2 relative overflow-hidden">
        <div 
        id="supplier-progress-bar" 
        class="h-6 {{ $color }} rounded-full" 
        style="width: 0%; transition: width 1s ease-out;">
    </div>
        <span class="absolute right-3 top-0 text-sm font-semibold text-gray-700 leading-6">
            {{ $score }}/{{ $nextLevelScore }}
        </span>
    </div>
    <p class="text-gray-500 text-sm mb-4">Points needed for next level: {{ max($nextLevelScore - $score, 0) }}</p>

    {{-- История последних изменений --}}
    <h4 class="font-semibold text-lg mb-2">Recent Reputation Changes</h4>
    <ul class="space-y-1 max-h-48 overflow-y-auto">
        @foreach($supplier->reputationLogs()->latest()->take(5)->get() as $log)
            <li class="flex justify-between py-2 px-3 rounded-lg border {{ $log->score_change > 0 ? 'border-green-200 bg-green-50 text-green-700' : 'border-red-200 bg-red-50 text-red-700' }}">
                <span class="font-medium">{{ $log->reason }}</span>
                <span class="font-bold">{{ $log->score_change > 0 ? '+' : '' }}{{ $log->score_change }}</span>
            </li>
        @endforeach
    </ul>
</div>

{{-- Анимация прогресс-бара --}}
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const bar = document.getElementById('supplier-progress-bar');
    if(bar) {
        // Запускаем анимацию после небольшого таймаута
        setTimeout(() => {
            bar.style.width = '{{ $progress }}%';
        }, 50);
    }
});
</script>
@endpush
@endsection
