<div class="bg-white border rounded-lg">

            <div class="border-b px-5 py-3 font-semibold">
                Premium Plans
            </div>

            <div class="p-5 space-y-4">

                <div>

                    <div class="text-sm text-gray-500">
                        Supplier Plan
                    </div>

                    <div class="font-semibold">
                        {{ optional($user->premiumPlan)->name ?? '-' }}
                    </div>

                    @if($user->supplier_premium_start)

                        <div class="text-sm text-gray-500">
                            {{ $user->supplier_premium_start }}
                            →
                            {{ $user->supplier_premium_end }}
                        </div>

                    @endif

                </div>

                <hr>

                <div>

                    <div class="text-sm text-gray-500">
                        Buyer Plan
                    </div>

                    <div class="font-semibold">
                        {{ optional($user->buyerPremiumPlan)->name ?? '-' }}
                    </div>

                    @if($user->buyer_premium_start)

                        <div class="text-sm text-gray-500">
                            {{ $user->buyer_premium_start }}
                            →
                            {{ $user->buyer_premium_end }}
                        </div>

                    @endif

                </div>

            </div>

        </div>