<x-alerts />

<form method="POST"
    action="{{ route('manufacturer.products.store') }}"
    enctype="multipart/form-data"
    class="" id="productForm">
    @csrf

    <input type="hidden" name="user_id" value="{{ auth()->id() }}">

    @php
    $languages = \App\Models\Language::where('is_active', true)->get();
    @endphp

    {{-- Шаги формы --}}
    <div class="form-step" data-step="1">

        @include('dashboard.supplier.sections.step1')

    </div>

    <div class="form-step hidden" data-step="2">

        @include('dashboard.supplier.sections.step2')

    </div>

    <div class="form-step hidden" data-step="3">

        @include('dashboard.supplier.sections.step3')


    </div>

    
    <div class="form-step hidden" data-step="4">

        @include('dashboard.supplier.sections.step4')


    </div>


    <div class="form-step hidden" data-step="5">

        @include('dashboard.supplier.sections.step5')


    </div>




    <div class="form-step hidden" data-step="6">

        @include('dashboard.supplier.sections.step6')







    </div>

    <div class="form-step hidden" data-step="7">

        @include('dashboard.supplier.sections.step7')







    </div>


    {{-- Навигация между шагами --}}
    <div class="flex mt-6">
        <button type="button" id="prevBtn" class="bg-gray-300 px-6 py-2 rounded hidden">Назад</button>
        <button type="button" id="nextBtn" class="ml-auto bg-blue-800 text-white px-6 py-2 rounded">Далее</button>
        <button type="submit" id="submitBtn" class="ml-auto bg-green-600 text-white px-6 py-2 rounded hidden">Сохранить</button>
    </div>
</form>

<style>
    .input {
        width: 100%;
        border: 1px solid #d1d5db;
        padding: 0.75rem 1rem;
        border-radius: 0.75rem;
        font-family: 'Figtree', sans-serif;
    }
</style>