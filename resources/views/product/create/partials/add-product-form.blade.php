<x-alerts />

    @php
    $languages = \App\Models\Language::where('is_active', true)->get();
    @endphp



    <div>
        @include('product.create.sections.step1')
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