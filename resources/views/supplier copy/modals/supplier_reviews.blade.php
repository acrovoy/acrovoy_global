<div 
    x-show="openReviews"
    x-transition
    class="fixed inset-0 z-50 flex items-center justify-center"
    style="display: none;"
>

    <!-- Overlay -->
    <div 
        class="absolute inset-0 bg-slate-900/40 backdrop-blur-sm"
        @click="openReviews = false"
    ></div>

    <!-- Modal -->
    <div class="relative w-full max-w-3xl rounded-2xl overflow-hidden shadow-2xl bg-white z-10">

        <!-- HEADER -->
        <div class="px-8 py-6 bg-gradient-to-r from-slate-50 via-[#f4f1eb] to-[#ebe5dc] border-b border-gray-200">

            <div class="flex items-center justify-between">

                <div>
                    <h2 class="text-xl font-semibold text-gray-900">
                        Supplier Reviews
                    </h2>

                    <!-- Average Rating -->
                    <div class="flex items-center gap-2 mt-2">

                        @php
                            $avg = round($supplier->supplier_reviews_avg_rating ?? 0, 1);
                            $count = $supplier->supplier_reviews_count ?? $supplier->supplierReviews->count();
                            $supplierRating = round($supplier->supplierReviews->avg('rating'), 1);
                        @endphp

                        <div class="text-2xl font-semibold text-gray-900">
                            {{ $supplierRating }}
                        </div>

                        <div class="flex items-center">
                            @for ($i = 1; $i <= 5; $i++)
                                @if ($i <= floor($supplierRating))
                                        <svg class="w-4 h-4 fill-current text-yellow-500" viewBox="0 0 20 20"><path d="M10 15l-5.878 3.09L5.36 11.545 1 7.91l6.061-.545L10 2l2.939 5.365L19 7.91l-4.36 3.635 1.238 6.545z"/></svg>
                                    @elseif ($i - $supplierRating < 1)
                                        <svg class="w-4 h-4 fill-current text-yellow-300" viewBox="0 0 20 20"><path d="M10 15l-5.878 3.09L5.36 11.545 1 7.91l6.061-.545L10 2l2.939 5.365L19 7.91l-4.36 3.635 1.238 6.545z"/></svg>
                                    @else
                                        <svg class="w-4 h-4 fill-current text-gray-300" viewBox="0 0 20 20"><path d="M10 15l-5.878 3.09L5.36 11.545 1 7.91l6.061-.545L10 2l2.939 5.365L19 7.91l-4.36 3.635 1.238 6.545z"/></svg></svg>

                                    @endif
                            @endfor
                        </div>

                        <div class="text-sm text-gray-500">
                            {{ $count }} reviews
                        </div>

                    </div>
                </div>

                <!-- Close button -->
                <button 
                    @click="openReviews = false"
                    class="text-gray-500 hover:text-gray-900 transition"
                >
                    ✕
                </button>

            </div>
        </div>


        <!-- BODY -->
        <div class="max-h-[70vh] overflow-y-auto px-8 py-6 bg-white">

            @forelse($supplier->supplierReviews as $review)

                @php
                    $user = $review->order->user ?? null;
                @endphp

                <div class="py-6 border-b border-gray-100 last:border-none">

                    <div class="flex gap-4">

                        <!-- Avatar -->
                        <div class="flex-shrink-0">
                            @if($user && $user->photo)
                                <img 
                                    src="{{ asset('storage/' . $user->photo) }}" 
                                    class="w-12 h-12 rounded-full object-cover"
                                >
                            @else
                                <div class="w-12 h-12 rounded-full bg-gray-200 flex items-center justify-center text-gray-600 font-semibold">
                                    {{ strtoupper(substr($user->name ?? 'U', 0, 1)) }}
                                </div>
                            @endif
                        </div>

                        <!-- Content -->
                        <div class="flex-1">

                            <div class="flex items-center justify-between">

                                <div>
                                    <div class="text-sm font-semibold text-gray-900">
                                        {{ $user->name ?? 'Anonymous' }} {{ $user->last_name ?? '' }}
                                    </div>

                                    <div class="text-xs text-gray-400 mt-0.5">
                                        {{ $review->created_at->format('d M Y') }}
                                    </div>
                                </div>

                                <!-- Rating -->
                                <div class="flex items-center gap-1">
                                    @for ($i = 1; $i <= 5; $i++)
                                        @if ($i <= $review->rating)
                                            <svg class="w-4 h-4 text-amber-500" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M10 15l-5.878 3.09L5.36 11.545 1 7.91l6.061-.545L10 2l2.939 5.365L19 7.91l-4.36 3.635 1.238 6.545z"/>
                                            </svg>
                                        @else
                                            <svg class="w-4 h-4 text-gray-300" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M10 15l-5.878 3.09L5.36 11.545 1 7.91l6.061-.545L10 2l2.939 5.365L19 7.91l-4.36 3.635 1.238 6.545z"/>
                                            </svg>
                                        @endif
                                    @endfor
                                </div>

                            </div>

                            <!-- Comment -->
                            <div class="mt-3 text-sm text-gray-700 leading-relaxed">
                                {{ $review->comment }}
                            </div>

                        </div>

                    </div>

                </div>

            @empty
                <div class="text-center py-12 text-gray-400 text-sm">
                    No reviews yet.
                </div>
            @endforelse

        </div>

    </div>
</div>