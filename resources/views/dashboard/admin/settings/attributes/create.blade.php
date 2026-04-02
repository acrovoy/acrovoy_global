@extends('dashboard.admin.settings.layout')

@section('settings-content')

<div class="flex flex-col max-w-3xl">

<x-alerts />

{{-- Header --}}

<div>

<h2 class="text-2xl font-semibold text-gray-900">
Добавить Attribute
</h2>

<p class="text-sm text-gray-500 mt-1">
Создание новой характеристики товара
</p>

</div>


{{-- Form Card --}}

<div class="bg-white border border-gray-200 rounded-xl shadow-sm">

<form action="{{ route('admin.settings.attributes.store') }}"
method="POST"
class="p-6 flex flex-col gap-6">

@csrf


@php

use App\Models\Language;

$languages = Language::where('is_active', true)
->orderBy('sort_order')
->get();

@endphp


{{-- Translations --}}

<div class="border rounded-xl p-4 space-y-4">

<h3 class="font-medium text-gray-700 text-sm">
Translations
</h3>


@foreach($languages as $lang)

<div>

<label class="block text-sm text-gray-600 mb-1">
Name ({{ strtoupper($lang->code) }})
</label>

<input
type="text"
name="translations[{{ $lang->code }}]"
value="{{ old('translations.' . $lang->code) }}"
class="w-full border border-gray-300 rounded px-3 py-2"
required
>

</div>

@endforeach

</div>



{{-- Code --}}

<div>

<label class="block font-medium mb-1">
Code
</label>

<input
type="text"
name="code"
value="{{ old('code') }}"
class="w-full border border-gray-300 rounded px-3 py-2"
required
>

@error('code')
<p class="text-red-600 mt-1">{{ $message }}</p>
@enderror

</div>



{{-- Type --}}

<div>

<label class="block font-medium mb-1">
Type
</label>

<select
name="type"
class="w-full border border-gray-300 rounded px-3 py-2"
required
>

<option value="">Select type</option>

<option value="text">Text</option>
<option value="number">Number</option>
<option value="select">Select</option>
<option value="multiselect">Multiselect</option>
<option value="boolean">Boolean</option>

</select>

@error('type')
<p class="text-red-600 mt-1">{{ $message }}</p>
@enderror

</div>



{{-- Unit --}}

<div>

<label class="block font-medium mb-1">
Unit (optional)
</label>

<input
type="text"
name="unit"
value="{{ old('unit') }}"
class="w-full border border-gray-300 rounded px-3 py-2"
placeholder="cm / kg / mm"
/>

</div>



{{-- Flags --}}

<div class="flex gap-6">

<label class="flex items-center gap-2">

<input
type="checkbox"
name="is_required"
value="1"
{{ old('is_required') ? 'checked' : '' }}
>

Required

</label>


<label class="flex items-center gap-2">

<input
type="checkbox"
name="is_filterable"
value="1"
{{ old('is_filterable') ? 'checked' : '' }}
>

Filterable

</label>

</div>



{{-- Sort Order --}}

<div>

<label class="block font-medium mb-1">
Sort order
</label>

<input
type="number"
name="sort_order"
value="{{ old('sort_order', 0) }}"
class="w-full border border-gray-300 rounded px-3 py-2"
>

</div>



{{-- Actions --}}

<div class="flex justify-end gap-3 pt-4 border-t">

<a
href="{{ route('admin.settings.attributes.index') }}"
class="px-4 py-2 text-sm border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-100 transition"
>

Отмена

</a>


<button
type="submit"
class="px-5 py-2 text-sm bg-gray-900 text-white rounded-lg hover:bg-gray-800 transition"
>

Сохранить

</button>

</div>


</form>

</div>

</div>

@endsection