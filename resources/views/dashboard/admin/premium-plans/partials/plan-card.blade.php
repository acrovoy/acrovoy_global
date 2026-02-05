<div class="flex flex-col">

    <div class="relative border rounded-xl p-6 shadow-sm
        {{ $plan['popular'] ? 'border-blue-500 scale-[1.02]' : 'border-gray-200' }}
        transition">

        @if($plan['popular'])
            <span class="absolute -top-3 left-1/2 -translate-x-1/2
                bg-blue-500 text-white text-xs font-semibold px-3 py-1 rounded-full">
                Most Popular
            </span>
        @endif

        <h3 class="text-xl font-semibold mb-2">{{ $plan['name'] }}</h3>
        <p class="text-3xl font-bold mb-4">{{ $plan['price'] }}</p>

        <ul class="space-y-2 text-gray-700 text-sm">
            @foreach($plan['features'] as $feature)
                <li class="flex items-center gap-2">
                    <span class="text-green-500">✔</span>
                    {{ $feature }}
                </li>
            @endforeach
        </ul>
    </div>

    <div class="flex justify-between mt-2 gap-2">
        <a href="{{ route('admin.premium-plans.edit', $plan['id']) }}"
           class="flex-1 py-2 text-center rounded-md bg-yellow-500 text-white font-semibold hover:bg-yellow-600 transition">
            Edit
        </a>

        <form action="{{ route('admin.premium-plans.destroy', $plan['id']) }}" method="POST" class="flex-1">
            @csrf
            @method('DELETE')
            <button type="submit"
                    class="w-full py-2 rounded-md bg-red-500 text-white font-semibold hover:bg-red-600 transition"
                    onclick="return confirm('Are you sure you want to delete the plan &quot;{{ $plan['name'] }}&quot;?')">
                Delete
            </button>
        </form>
    </div>
</div>
