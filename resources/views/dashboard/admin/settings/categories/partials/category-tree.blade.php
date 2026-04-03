@php
    $hasChildren = $category->children && $category->children->count();

    // Базовый класс для цвета и жирности
    $categoryClass = 'text-xs hover:underline break-inside-avoid';

    if ($category->level == 0) {
        // Главные категории
        $categoryClass .= ' font-semibold text-orange-500';
    } elseif ($category->is_leaf) {
        // Конечные категории — НЕжирные и черные
        $categoryClass .= ' font-normal text-black';
    } else {
        // Подкатегории (не конечные)
        $categoryClass .= ' font-semibold text-blue-800';
    }
@endphp

<div class="flex flex-col mb-1">
    {{-- Название категории кликабельное --}}
    <a href="{{ route('admin.settings.categories.edit', $category) }}"
       class="{{ $categoryClass }}"
       style="margin-left: {{ $category->level * 12 }}px;">
        {{ $category->name ?? $category->slug }}
    </a>

    {{-- Вложенные категории --}}
    @if($hasChildren)
        @foreach($category->children as $child)
            @include('dashboard.admin.settings.categories.partials.category-tree', ['category' => $child])
        @endforeach
    @endif
</div>