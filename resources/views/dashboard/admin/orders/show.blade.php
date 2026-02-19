@extends('dashboard.admin.layout')

@section('dashboard-content')

<a href="{{ route('admin.orders.index') }}"
    class="text-sm text-gray-500 hover:text-gray-700 flex items-center gap-1 mb-4">
    ← Back to orders
</a>

<div class="flex flex-col gap-4">

    {{-- Header --}}
    <div class="flex items-center justify-between">
        <div class="flex flex-col">
            <div class="flex items-baseline gap-4">
            <h2 class="text-2xl font-semibold text-gray-900"><span class="uppercase">{{ $order->type }}</span> Order #{{ $order->id }}  </h2>
            <span class="text-xs text-gray-400 uppercase">{{ $order->created_at->format('d M y | H:i') }}</span> 
            </div>
           
        </div>

        <span class="px-4 py-1 text-sm font-medium
            @if($order->status === 'pending') bg-yellow-50 text-yellow-700
            @elseif($order->status === 'paid') bg-blue-50 text-blue-700
            @elseif($order->status === 'shipped') bg-green-50 text-green-700
            @elseif($order->status === 'completed') bg-gray-50 text-gray-700
            @endif
        ">
            {{ ucfirst($order->status) }}
        </span>
    </div>

   

{{-- Customer + Contact & Shipping --}}
<div class="border rounded-lg p-6 bg-gray-50 shadow-sm">
    <h3 class="text-lg font-semibold text-gray-800 mb-6">Customer & Shipping and Contact Information</h3>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-8 text-sm text-gray-700">

        {{-- LEFT COLUMN — USER INFORMATION --}}
        <div class="space-y-3 pr-4 md:border-r md:border-gray-200">

            <p>
                <span class="text-xs text-gray-400 uppercase tracking-wide">
                    User (ID: {{ $order->user_id }})
                </span><br>
                {{ $order->user->name ?? '-' }} {{ $order->user->last_name ?? '' }}
                @if($order->user?->email)
                    (<a href="mailto:{{ $order->user->email }}" class="text-blue-600 hover:underline">
                        {{ $order->user->email }}
                    </a>)
                @endif
            </p>

            @php
                $lang = \App\Models\Language::where('code', $order->user->language ?? null)->first();
                $country = \App\Models\Country::where('code', $order->user->purchase_country ?? null)->first();
            @endphp

            <p>
                <span class="text-xs text-gray-400 uppercase tracking-wide">Language</span><br>
                {{ $lang->name ?? '-' }}
            </p>

            <p>
                <span class="text-xs text-gray-400 uppercase tracking-wide">Purchase Country</span><br>
                {{ $country->name ?? '-' }}
            </p>

            <p>
                <span class="text-xs text-gray-400 uppercase tracking-wide">Role</span><br>
                <span class="uppercase">{{ $order->user->role ?? '-' }}</span>
            </p>

            <p>
                <span class="text-xs text-gray-400 uppercase tracking-wide">Plan</span><br>
                {{ $order->user->buyer_premium_plan_id ?? 'FREE' }}
            </p>

            <p>
                <span class="text-xs text-gray-400 uppercase tracking-wide">Premium Period</span><br>
                {{ $order->user->buyer_premium_start ?? '-' }}
                —
                {{ $order->user->buyer_premium_end ?? '-' }}
            </p>

            <p>
                <span class="text-xs text-gray-400 uppercase tracking-wide">Currency</span><br>
                {{ $order->user->currency ?? '-' }}
            </p>

            <p>
                <span class="text-xs text-gray-400 uppercase tracking-wide">Order Date</span><br>
                {{ $order->created_at->format('d M y | H:i') }}
            </p>

        </div>

        {{-- RIGHT COLUMN — CONTACT & SHIPPING --}}
        <div class="space-y-3 pl-4">

            <p>
                <span class="text-xs text-gray-400 uppercase tracking-wide">Contact Name</span><br>
                {{ $order->first_name }} {{ $order->last_name }}
            </p>

            <p>
                <span class="text-xs text-gray-400 uppercase tracking-wide">Country</span><br>
                {{ $order->countryRelation?->name ?? '-' }}
            </p>

            <p>
                <span class="text-xs text-gray-400 uppercase tracking-wide">City</span><br>
                {{ $order->city ?? '-' }}
            </p>

            @if(!empty($order->regionRelation?->name))
            <p>
                <span class="text-xs text-gray-400 uppercase tracking-wide">Region</span><br>
                {{ $order->regionRelation?->name ?? '-' }}
            </p>
            @endif

            <p>
                <span class="text-xs text-gray-400 uppercase tracking-wide">Street</span><br>
                {{ $order->street ?? '-' }}
            </p>

            <p>
                <span class="text-xs text-gray-400 uppercase tracking-wide">Postal Code</span><br>
                {{ $order->postal_code ?? '-' }}
            </p>

            @if(!empty($order->phone))
            <p>
                <span class="text-xs text-gray-400 uppercase tracking-wide">Phone</span><br>
                {{ $order->phone ?? '-'}}
            </p>
            @endif

        </div>

    </div>
</div>




{{-- Supplier + Contact Information --}}
@foreach($order->items as $item)
    @php
        if ($order->type === 'product') {
            // обычный продукт
            $supplier = $item->product->supplier;
        } elseif ($order->type === 'rfq') {
            // RFQ — берем supplier из принятого оффера
            $acceptedOffer = $order->rfqOffer?->where('status', 'accepted')->first();
            $supplier = $acceptedOffer?->supplier;
           
        } else {
            $supplier = null;
        }

        $supplierUser = $supplier?->user;
        $supplierCountry = \App\Models\Country::find($supplier?->country_id);
    @endphp

    <div class="border rounded-lg p-6 bg-gray-50 shadow-sm mt-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-6">
            Supplier Information | 
    <span class="text-emerald-600">
        {{ $item->product->name ?? '-' }}
    </span>
        </h3>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 text-sm text-gray-700">

            {{-- LEFT COLUMN — SUPPLIER BASIC INFO --}}
            <div class="space-y-3 pr-4 md:border-r md:border-gray-200">

                <p>
                    <span class="text-xs text-gray-400 uppercase tracking-wide">
                        Supplier (ID: {{ $supplier?->id ?? '-' }})
                    </span><br>
                    <a href="{{ url('/supplier/' . $supplier->slug) }}"
       class="text-gray-600 hover:underline font-medium" target="_blank" rel="noopener">
        {{ $supplier->name }}
    </a>
                    @if($supplierUser?->email)
                        (<a href="mailto:{{ $supplierUser->email }}" class="text-blue-600 hover:underline">
                            {{ $supplierUser->email }}
                        </a>)
                    @endif
                </p>

                <p>
                    <span class="text-xs text-gray-400 uppercase tracking-wide">Verified</span><br>
                    {{ $supplier?->is_verified ? 'Yes' : 'No' }}
                </p>

                <p>
                    <span class="text-xs text-gray-400 uppercase tracking-wide">Trusted</span><br>
                    {{ $supplier?->is_trusted ? 'Yes' : 'No' }}
                </p>

                <p>
                    <span class="text-xs text-gray-400 uppercase tracking-wide">Premium</span><br>
                    {{ $supplier?->is_premium ? 'Yes' : 'No' }}
                </p>

                <p>
                    <span class="text-xs text-gray-400 uppercase tracking-wide">Reputation</span><br>
                    {{ $supplier?->reputation ?? '-' }}
                </p>

                <p>
                    <span class="text-xs text-gray-400 uppercase tracking-wide">Created At</span><br>
                    {{ $supplier?->created_at?->format('d M y | H:i') ?? '-' }}
                </p>

                {{-- Supplier User Info --}}
                @if($supplierUser)
                    <hr class="my-2 border-gray-300">
                    <h4 class="text-sm font-semibold text-gray-700">Supplier User Info</h4>

                    <p>
                        <span class="text-xs text-gray-400 uppercase tracking-wide">Name (ID: {{ $supplierUser->id }})</span><br>
                        {{ $supplierUser->name ?? '-' }} {{ $supplierUser->last_name ?? '' }}
                        @if($supplierUser->email)
                            (<a href="mailto:{{ $supplierUser->email }}" class="text-blue-600 hover:underline">
                                {{ $supplierUser->email }}
                            </a>)
                        @endif
                    </p>

                    <p>
                        <span class="text-xs text-gray-400 uppercase tracking-wide">Role</span><br>
                        {{ $supplierUser->role ?? '-' }}
                    </p>

                    <p>
                        <span class="text-xs text-gray-400 uppercase tracking-wide">Currency</span><br>
                        {{ $supplierUser->currency ?? '-' }}
                    </p>

                    <p>
                        <span class="text-xs text-gray-400 uppercase tracking-wide">Country</span><br>
                        @php
                            $supplierUserCountry = \App\Models\Country::where('code', $supplierUser->purchase_country)->first();
                        @endphp
                        {{ $supplierUserCountry?->name ?? '-' }}
                    </p>
                @endif
            </div>

            {{-- RIGHT COLUMN — CONTACT, ADDRESS & IMAGES --}}
            <div class="space-y-3 pl-4">

                <p>
                    <span class="text-xs text-gray-400 uppercase tracking-wide">Phone</span><br>
                    {{ $supplier?->phone ?? '-' }}
                </p>

                <p>
                    <span class="text-xs text-gray-400 uppercase tracking-wide">Country</span><br>
                    {{ $supplierCountry?->name ?? '-' }}
                </p>

                <p>
                    <span class="text-xs text-gray-400 uppercase tracking-wide">Address</span><br>
                    {{ $supplier?->address ?? '-' }}
                </p>

                @if($supplier?->logo)
                <p>
                    <span class="text-xs text-gray-400 uppercase tracking-wide">Logo</span><br>
                    <img src="{{ asset('storage/' . $supplier->logo) }}" alt="{{ $supplier->name }}" class="w-20 h-20 object-contain rounded border">
                </p>
                @endif

                @if($supplier?->catalog_image)
                <p>
                    <span class="text-xs text-gray-400 uppercase tracking-wide">Catalog Image</span><br>
                    <img src="{{ asset('storage/' . $supplier->catalog_image) }}" alt="{{ $supplier->name }}" class="w-24 h-24 object-contain rounded border">
                </p>
                @endif

            </div>

        </div>
    </div>
@endforeach





{{-- Product sector --}}
    @include('dashboard.admin.orders.partials.product-order', ['order' => $order])

    

    {{-- Status Timeline --}}
    <div class="border rounded-lg p-4 bg-white shadow-sm pb-6">
        <h4 class="text-xs font-semibold text-gray-600 uppercase tracking-wide mb-2">
            Order Status Timeline
        </h4>
    <div class=" border-t pt-6">
    <div class="relative pl-6">
        {{-- Vertical line --}}
        <div class="absolute left-2 top-0 w-px h-full bg-gray-200"></div>

        <div class="space-y-3">
            @forelse($orderData['status_history'] ?? [] as $history)
                @php
                    $statusValue = $history->status;
                    $isCurrent = $statusValue === $order->status; // активный статус
                    $displayStatus = str_replace('_', ' ', ucfirst($statusValue));
                    $displayDate = $history->created_at ?? '';
                @endphp

                <div class="relative flex items-start space-x-2">
                    {{-- Timeline Dot --}}
                    <div class="flex-shrink-0 mt-1 w-2.5 h-2.5 rounded-full
                        {{ $isCurrent ? 'bg-emerald-500' : 'bg-gray-400' }}">
                    </div>

                    {{-- Status + Date on one line --}}
                    <div class="text-sm text-gray-700 flex items-center space-x-3">
                        <span class="font-medium text-gray-800">{{ $displayStatus }}</span>
                        <span class="text-xs text-gray-400">{{ \Carbon\Carbon::parse($displayDate)->format('d M y | H:i') }}</span>
                    </div>
                </div>

                {{-- Comment (if any) --}}
                @if(!empty($history->comment))
                    <div class="ml-8 mt-1 text-xs text-gray-600 bg-gray-50 border border-gray-100 rounded px-2 py-1">
                        {{ $history->comment }}
                    </div>
                @endif

            @empty
                <div class="text-sm text-gray-500 ml-6">No status history</div>
            @endforelse
        </div>
    </div>
</div>

</div>
    
{{-- SHIPPING STATUS TIMELINE --}}
@foreach($order->items as $item)
    @foreach($item->shipments as $shipment)

        <div class="border rounded-lg p-4 bg-white shadow-sm mt-6">
            <h4 class="text-xs font-semibold text-gray-600 uppercase tracking-wide mb-4">
                Shipping Status Timeline 
                <span class="text-gray-400">
                    (Shipment ID: {{ $shipment->id }} Cargo: {{ $item->product->name ?? $item->title ?? '-' }})
                </span>
            </h4>

            <div class="border-t pt-6">
                <div class="relative pl-6">

                    {{-- Vertical line --}}
                    <div class="absolute left-2 top-0 w-px h-full bg-gray-200"></div>

                    <div class="space-y-3">
                        @forelse($shipment->statuses as $status)
                            @php
                                $displayStatus = ucfirst(
                                    str_replace('_', ' ', $status->status->value)
                                );
                            @endphp

                            <div class="relative flex items-start space-x-2">

                                {{-- Dot --}}
                                <div class="flex-shrink-0 mt-1 w-2.5 h-2.5 rounded-full bg-gray-200"></div>

                                <div class="text-sm text-gray-700 flex items-center space-x-3">
                                    <span class="font-medium text-gray-800">
                                        {{ $displayStatus }}
                                    </span>

                                    <span class="text-xs text-gray-400">
                                        {{ \Carbon\Carbon::parse($status->created_at)->format('d M y | H:i') }}
                                    </span>
                                </div>
                            </div>

                            {{-- Comment --}}
                            @if(!empty($status->comment))
                                <div class="ml-8 mt-1 text-xs text-gray-600 bg-gray-50 border border-gray-100 rounded px-2 py-1">
                                    {{ $status->comment }}
                                </div>
                            @endif

                        @empty
                            <div class="text-sm text-gray-500 ml-6">
                                No shipping status history
                            </div>
                        @endforelse
                    </div>

                </div>
            </div>

        </div>

    @endforeach
@endforeach



    {{-- Disputes --}}
    <div class="border rounded-lg p-4 bg-gray-50">
        <h3 class="font-semibold mb-3">Disputes</h3>
        @if(count($orderData['disputes']) > 0)
            <ul class="space-y-3">
                @foreach($orderData['disputes'] as $dispute)
                    <li class="border rounded-lg p-4 bg-white">
                        <p><strong>Customer:</strong> {{ $dispute->user->name ?? '—' }}</p>
                        <p><strong>Reason:</strong> {{ $dispute->reason }}</p>
                        <p><strong>Requested action:</strong> {{ ucfirst($dispute->action) }}</p>
                        <p><strong>Status:</strong> {{ ucfirst(str_replace('_', ' ', $dispute->status)) }}</p>
                        @if($dispute->buyer_comment)
                            <p class="mt-1 bg-red-100 p-2 rounded">Comment: {{ $dispute->buyer_comment }}</p>
                        @endif
                        @if($dispute->admin_comment)
                            <p class="mt-1 bg-purple-100 p-2 rounded">Admin decision: {{ $dispute->admin_comment }}</p>
                        @endif
                        @if($dispute->attachment)
                            <p><a href="{{ asset('storage/' . $dispute->attachment) }}" target="_blank" class="text-blue-600 hover:underline">View attachment</a></p>
                        @endif
                    </li>
                @endforeach
            </ul>


            

        <h2 class="text-xl font-bold mt-6 mb-4">Dispute History</h2>

@if($orderData['disputes']->isEmpty())
    <p>No disputes for this order.</p>
@else
    <table class="w-full border rounded text-sm">
        <thead class="bg-gray-100">
            <tr>
                <th class="px-4 py-2">ID</th>
                <th class="px-4 py-2">Reason</th>
                <th class="px-4 py-2">Status</th>
                <th class="px-4 py-2">Who / Comment</th>
                <th class="px-4 py-2">Attachment</th>
                <th class="px-4 py-2">Created</th>
            </tr>
        </thead>
        <tbody>
            @foreach($orderData['disputes'] as $dispute)
            <tr class="border-t align-top">
                <td class="px-4 py-2">{{ $dispute->id }}</td>
                <td class="px-4 py-2">{{ $dispute->reason }}</td>
                <td class="px-4 py-2 
                    @if($dispute->status === 'admin_review') bg-red-100 text-red-800 font-semibold @endif
                ">{{ ucfirst(str_replace('_',' ',$dispute->status)) }}</td>
                <td class="px-4 py-2 space-y-1">
                    
                        <div class="bg-red-100 text-blue-800 p-2 rounded-lg">
            <strong>Buyer:</strong> {{ $dispute->buyer_comment }}
        </div>
                        <div class="bg-blue-100 text-blue-800 p-2 rounded-lg">
            <strong>Supplier:</strong> {{ $dispute->supplier_comment }}
        </div>
                        <div class="bg-purple-100 text-purple-800 p-2 rounded-lg">
            <strong>Admin:</strong> {{ $dispute->admin_comment }}
        </div>
                   
                </td>
                <td class="px-4 py-2">
                    @if($dispute->attachment)
                        <a href="{{ asset('storage/' . $dispute->attachment) }}" target="_blank" class="text-blue-600 hover:underline">View</a>
                    @else
                        -
                    @endif
                </td>


                @if(auth()->user()->role === 'admin')
    <form method="POST"
          action="{{ route('admin.orders.disputes.adminComment', $dispute->id) }}"
          class="mt-4 space-y-2">
        @csrf

        <label class="block text-sm font-medium text-gray-700">
            Admin comment
        </label>

        <textarea
            name="admin_comment"
            rows="3"
            required
            class="w-full border rounded-lg p-2 text-sm focus:ring focus:ring-purple-200"
            placeholder="Write admin decision or clarification...">{{ old('admin_comment', $dispute->admin_comment) }}</textarea>

        <div class="flex justify-end mb-4">
           
            <button
                type="submit"
                class="bg-purple-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-purple-700">
                Send message
            </button>
        </div>
    </form>



    <form action="{{ route('admin.disputes.update', $dispute->id) }}" method="POST" class="mt-3 space-y-2">
        @csrf
        @method('PATCH')

       
        {{-- Status buttons --}}
        <div class="flex gap-2">
            <button
                name="status"
                value="pending"
                class="px-3 py-1 text-sm rounded bg-yellow-100 text-yellow-800 hover:bg-yellow-200">
                Pending
            </button>

            <button
                name="status"
                value="resolved"
                class="px-3 py-1 text-sm rounded bg-green-100 text-green-800 hover:bg-green-200">
                Resolved
            </button>
        </div>
    </form>


@endif




                <td class="px-4 py-2">{{ $dispute->created_at->format('Y-m-d H:i') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
@endif




        @else
            <p class="text-gray-500 italic">No disputes for this order.</p>
        @endif
    </div>

    {{-- Back --}}
    
    <div class="flex justify-start">
        <a href="{{ route('admin.orders.index') }}"
           class="text-sm text-gray-500 hover:text-gray-700 flex items-center gap-1 mb-4">
            ← Back to orders
        </a>
    </div>

</div>
@endsection
