@extends('dashboard.layout')

@section('dashboard-content')


<div>
    <h2 class="text-2xl font-semibold text-gray-900">Edit Product</h2>
    <p class="text-sm text-gray-500 mb-4">
        Manage all your products, edit details, prices, and inventory.
    </p>
</div>



<div class="bg-gray-50 border border-gray-200 rounded-xl shadow-sm">
    <div class="p-6">

        <x-alerts />

        <form method="POST"
            action="{{ route('products.update', $product->id) }}"
            enctype="multipart/form-data"
            class="" id="productForm">
            @csrf
            @method('PUT')

            <input type="hidden" name="user_id" value="{{ auth()->id() }}">

            @php
            $languages = \App\Models\Language::where('is_active', true)->get();
            @endphp

            <div class="form-step" data-step="1">

                @include('product.sections.step1')

            </div>

            <div class="form-step hidden" data-step="2">

                @include('product.sections.step2')

            </div>

            <div class="form-step hidden" data-step="3">

                @include('product.sections.step3')

            </div>


            <div class="form-step hidden" data-step="4">
                @include('product.sections.step4')
            </div>

            <div class="form-step hidden" data-step="5">

                @include('product.sections.step5')

            </div>

            <div class="form-step hidden" data-step="6">

                @include('product.sections.step6')

            </div>

            {{-- Навигация между шагами --}}
            <div class="flex justify-between mt-6">
                <button type="button" id="prevBtn" class="bg-gray-300 px-6 py-2 rounded hidden">Назад</button>
                <button type="button" id="nextBtn" class="bg-blue-800 text-white px-6 py-2 rounded">Далее</button>
                <button type="submit" id="submitBtn" class="bg-green-600 text-white px-6 py-2 rounded hidden">Сохранить</button>
            </div>
        </form>
    </div>
</div>

<style>
    .input {
        width: 100%;
        border: 1px solid #d1d5db;
        padding: 0.75rem 1rem;
        border-radius: 0.75rem;
        font-family: 'Figtree', sans-serif;
    }
</style>

@vite(['resources/js/product-edit.js', 'resources/js/product-form-steps.js'])

@endsection

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@simonwep/pickr/dist/themes/classic.min.css" />

<script src="https://cdn.jsdelivr.net/npm/@simonwep/pickr"></script>