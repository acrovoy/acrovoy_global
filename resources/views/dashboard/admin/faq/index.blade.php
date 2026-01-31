@extends('dashboard.admin.layout')

@section('dashboard-content')
<div class="flex justify-between items-center mb-6">
    <h2 class="text-2xl font-bold">Manage FAQs</h2>
    <a href="{{ route('admin.faq.create') }}" 
       class="px-4 py-2 bg-brown-600 text-black rounded-md text-sm font-semibold">
       Add New FAQ +
    </a>
</div>

@if(session('success'))
    <div class="bg-green-100 text-green-800 p-3 rounded mb-4">
        {{ session('success') }}
    </div>
@endif

<div class="space-y-3">
    @foreach($faqs as $faq)
        <div class="bg-white rounded-lg shadow-sm p-5 flex justify-between items-start">
            <div>
                <p class="font-semibold">{{ $faq['question'] }}</p>
                <p class="text-gray-600 mt-1">{{ $faq['answer'] }}</p>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('admin.faq.edit', $faq['id']) }}" 
                   class="px-3 py-1 bg-blue-500 text-white rounded-md text-sm">Edit</a>
                <form action="{{ route('admin.faq.destroy', $faq['id']) }}" method="POST" onsubmit="return confirm('Are you sure?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="px-3 py-1 bg-red-500 text-white rounded-md text-sm">Delete</button>
                </form>
            </div>
        </div>
    @endforeach
</div>
@endsection
