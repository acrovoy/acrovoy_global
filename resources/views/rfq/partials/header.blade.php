<div class="mb-5">

    {{-- BACK --}}
    <a href="
        @if($isBuyer ?? false)
           {{ route('buyer.rfqs.index') }} 
        @else
            {{ route('supplier.rfqs.index') }}
        @endif
    "
    class="text-sm text-gray-500 hover:text-gray-900 transition">
    
        ← Back to RFQs
    </a>

    {{-- HEADER BAR --}}
    <div class="mt-3 border border-gray-200 rounded-lg px-5 py-4 ">

        <div class="flex items-start justify-between gap-6">

            {{-- LEFT --}}
            <div class="min-w-0 flex-1">

               

                

                {{-- META --}}
                <div class="flex flex-wrap gap-5 text-xs text-gray-500 mt-2">

                    

                    

                </div>

                {{-- DESCRIPTION --}}
                @if($rfq->description)

                    <details class="mt-3 group">

                        <summary class="text-xs text-gray-400 hover:text-gray-700 cursor-pointer select-none flex items-center gap-1 w-fit">

                            View description

                            <svg class="w-3.5 h-3.5 transition-transform group-open:rotate-180"
                                 fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round"
                                      stroke-linejoin="round"
                                      stroke-width="2"
                                      d="M19 9l-7 7-7-7" />
                            </svg>

                        </summary>

                        <div class="text-sm text-gray-600 leading-relaxed mt-2 max-w-2xl">
                            {!! nl2br(e($rfq->description)) !!}
                        </div>

                    </details>

                @endif

            </div>

            {{-- RIGHT ACTIONS --}}
            <div class="flex items-center gap-2 shrink-0">

                {{-- BUYER ONLY --}}
                @if($isBuyer ?? false)

                    @if($rfq->status->canPublish())
                        <form method="POST" action="">
                            @csrf
                            <button class="px-3 py-1.5 text-sm bg-gray-900 text-white rounded-md hover:bg-gray-800 transition">
                                Publish
                            </button>
                        </form>
                    @endif

                    @if($rfq->status->canClose())
                        <form method="POST" action="">
                            @csrf
                            <button class="px-3 py-1.5 text-sm border border-gray-300 text-gray-700 rounded-md hover:bg-gray-50 transition">
                                Close
                            </button>
                        </form>
                    @endif

                @endif

            </div>

        </div>

    </div>

</div>