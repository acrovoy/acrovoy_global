@extends('dashboard.admin.layout')

@section('dashboard-content')
<h2 class="text-2xl font-bold mb-4">Edit Premium Plan</h2>

<form action="{{ route('admin.premium-plans.update', $plan->id) }}" method="POST" class="space-y-6 bg-white p-6 rounded shadow">
    @csrf
    @method('PUT')

    {{-- Plan name --}}
    <div>
        <label for="name" class="block font-medium mb-1">Plan Name</label>
        <input type="text" name="name" id="name"
               class="w-full border rounded p-2"
               value="{{ $plan->name }}"
               required>
    </div>

    {{-- Plan price --}}
    <div>
        <label for="price" class="block font-medium mb-1">Price</label>
        <input type="text" name="price" id="price"
               class="w-full border rounded p-2"
               value="{{ $plan->price }}"
               required>
    </div>

    {{-- Plan target type --}}
<div>
    <label for="target_type" class="block font-medium mb-1">
        Plan Type
    </label>

    <select name="target_type" id="target_type"
            class="w-full border rounded p-2" required>
        <option value="supplier"
            {{ $plan->target_type === 'supplier' ? 'selected' : '' }}>
            Supplier (Seller) Plan
        </option>

        <option value="buyer"
            {{ $plan->target_type === 'buyer' ? 'selected' : '' }}>
            Buyer Plan
        </option>
    </select>

    <p class="text-xs text-gray-500 mt-1">
        Choose who this plan is intended for.
    </p>
</div>

    {{-- Popular checkbox --}}
    <div class="flex items-center gap-2">
        <input type="checkbox" name="popular" id="popular" class="h-4 w-4"
               {{ $plan->popular ? 'checked' : '' }}>
        <label for="popular" class="font-medium">Mark as Most Popular</label>
    </div>

    {{-- Features with values --}}
    <div>
        <h3 class="font-semibold mb-2">Select Features and Values</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

            @foreach($features as $feature)
                @php
                    $pfData = $planFeatures[$feature->id] ?? ['enabled' => false, 'value' => ''];
                @endphp
                <div class="flex items-center gap-2 border rounded p-2 hover:bg-gray-50">
                    <input type="checkbox" name="features[{{ $feature->id }}][enabled]"
                           value="1" class="h-4 w-4" {{ $pfData['enabled'] ? 'checked' : '' }}>
                    <span class="flex-1">{{ $feature->name }}</span>
                    <input type="text" name="features[{{ $feature->id }}][value]"
                           class="border rounded p-1 w-24"
                           placeholder="Value (optional)"
                           value="{{ $pfData['value'] }}">
                </div>
            @endforeach

        </div>
    </div>

    <div>
        <button type="submit"
                class="px-6 py-2 bg-blue-600 text-white font-semibold rounded hover:bg-blue-700">
            Update Plan
        </button>
        <a href="{{ route('admin.premium-plans.index') }}"
           class="ml-4 px-4 py-2 border rounded hover:bg-gray-100">
            Cancel
        </a>
    </div>
</form>
@endsection
