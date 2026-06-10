@extends('dashboard.admin.layout')

@section('dashboard-content')
<div class="flex justify-between items-center mb-6">
    <h2 class="text-2xl font-bold">Manage FAQs</h2>
    <a href="{{ route('admin.faq.create') }}" 
       class="inline-flex items-center gap-2 mt-3 px-4 py-2
           text-sm font-medium text-gray-700
           bg-white border border-gray-200
           rounded-lg
           hover:bg-gray-50 hover:border-gray-300 hover:text-gray-900
           active:scale-[0.98]
           transition-all duration-150 shadow-sm">
       Add New FAQ +
    </a>
</div>

<x-alerts />

<div class="space-y-3">
    @foreach($faqs as $faq)
        <div class="bg-white rounded-lg shadow-sm p-5 flex justify-between items-start">
            <div>
                <p class="font-semibold">{{ $faq['question'] }}</p>
                <p class="text-gray-600 mt-1">{{ $faq['answer'] }}</p>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('admin.faq.edit', $faq['id']) }}" 
                   class="text-sm text-gray-700 hover:underline mr-3">Edit</a>
                <form action="{{ route('admin.faq.destroy', $faq['id']) }}" method="POST" onsubmit="return confirm('Are you sure?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="text-sm text-red-600 hover:underline">Delete</button>
                </form>
            </div>
        </div>
    @endforeach
</div>
@endsection
