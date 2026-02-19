@extends('dashboard.admin.layout')

@section('dashboard-content')
<div class="flex flex-col gap-6">

    {{-- ================= OPEN DISPUTES ================= --}}
    <div class="flex justify-between items-center">
        <div>
            <h2 class="text-2xl font-semibold text-gray-900">
                Orders with Opened Dispute
            </h2>
            <p class="text-sm text-gray-500">
                Orders that currently have active dispute cases
            </p>
        </div>

        <form method="GET" class="flex gap-2 items-center">
            <select name="sort"
                    onchange="this.form.submit()"
                    class="px-3 py-2 text-sm border border-gray-300 rounded-lg text-gray-700 bg-white">
                <option value="">Newest</option>
                <option value="oldest" @selected($sort === 'oldest')>Oldest</option>
                <option value="status" @selected($sort === 'status')>Status</option>
            </select>
        </form>
    </div>

    <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 border-b">
                <tr>
                    <th class="px-5 py-3 text-left font-medium text-gray-600">ID</th>
                    <th class="px-5 py-3 text-left font-medium text-gray-600">Customer</th>
                    <th class="px-5 py-3 text-left font-medium text-gray-600">Supplier</th>
                    <th class="px-5 py-3 text-left font-medium text-gray-600">Dispute Status</th>
                    <th class="px-5 py-3 text-left font-medium text-gray-600">Created</th>
                    <th class="px-5 py-3 text-right font-medium text-gray-600">Total</th>
                    
                </tr>
            </thead>

            <tbody class="divide-y">
                @foreach($ordersWithOpenDisputes as $order)
                    <tr onclick="window.location='{{ route('admin.orders.show', $order->id) }}'"
    class="hover:bg-gray-50 transition cursor-pointer">
                        <td class="px-5 py-3 font-semibold text-gray-900">
                            <div>{{ $order->id }}</div>
                            <div class="text-xs text-gray-400">{{ $order->type }}</div>
                        </td>

                        <td class="px-5 py-3 text-gray-700">
                            {{ $order->user->name ?? 'User' }} {{ $order->user->last_name ?? '' }}
                        </td>

                        <td class="px-5 py-3 text-gray-700">

                        
                            @php
                                
                                    // обычный продукт
                                    $user_name = $order->items->first()?->product?->supplier?->user?->name ?? '';
                                    $user_last_name = $order->items->first()?->product?->supplier?->user?->last_name ?? '';
                                    $supplier = $order->items->first()?->product?->supplier?->name;
                                
                                    // RFQ — берем supplier из принятого оффера

                                    $acceptedOffer = $order->rfqOffer?->where('status', 'accepted')->first();
                                    $rfq_supplier = $acceptedOffer?->supplier;
                                    
                                
                                

                                
                            @endphp
                            <div>
                            @if ($order->type === 'product') 
                            {{ $order->items->first()?->product?->supplier?->user?->name ?? '' }} 
                            {{ $order->items->first()?->product?->supplier?->user?->last_name ?? '' }}
                            @elseif($order->type === 'rfq')
                            {{ $rfq_supplier->user->name ?? '-' }} {{ $rfq_supplier->user->last_name ?? '-' }}
                            
                            @endif
                            
                            
                            </div>
                            <span class="text-emerald-600 text-xs">
                            @if ($order->type === 'product') 
                            {{ $supplier ?? '-' }}
                            @elseif($order->type === 'rfq')
                            {{ $rfq_supplier->name ?? '-' }}
                            @endif
                        
                        </span>
                        </td>

                        @php
                            $openStatuses = ['pending', 'supplier_offer', 'rejected', 'admin_review'];
                            $dispute = $order->disputes->firstWhere(fn($d) => in_array($d->status, $openStatuses));
                        @endphp

                        <td class="px-5 py-3">
                            @if($dispute)
                                <span class="inline-flex px-2 py-1 rounded-full text-xs font-medium
                                    @if($dispute->status === 'admin_review')
                                        bg-red-50 text-red-700
                                    @else
                                        bg-yellow-50 text-yellow-700
                                    @endif">
                                    {{ ucfirst(str_replace('_',' ', $dispute->status)) }}
                                </span>
                            @else
                                <span class="text-gray-400 text-xs">—</span>
                            @endif
                        </td>

                        <td class="px-5 py-3 text-right font-semibold text-gray-900">
                            ${{ number_format($order->items->sum(fn($i) => $i->quantity * $i->price), 2) }}
                        </td>

                        <td class="px-5 py-3 text-gray-600 text-xs">
                            <div>{{ $order->created_at->format('d M y') }}</div>
                            <div>{{ $order->created_at->format('H:i') }}</div>
                        </td>

                       
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>


    {{-- ================= ALL ORDERS ================= --}}
    <div class="flex justify-between items-center">
        <div>
            <h2 class="text-2xl font-semibold text-gray-900">
                Orders
            </h2>
            <p class="text-sm text-gray-500">
                Manage and monitor all platform orders
            </p>
        </div>

        <form method="GET" class="flex gap-2 items-center">
            <select name="sort"
                    onchange="this.form.submit()"
                    class="px-3 py-2 text-sm border border-gray-300 rounded-lg text-gray-700 bg-white">
                <option value="">Newest</option>
                <option value="oldest" @selected($sort === 'oldest')>Oldest</option>
                <option value="status" @selected($sort === 'status')>Status</option>
            </select>

            <select name="status"
                    onchange="this.form.submit()"
                    class="px-3 py-2 text-sm border border-gray-300 rounded-lg text-gray-700 bg-white">
                <option value="">All</option>
                <option value="pending" @selected($status === 'pending')>Pending</option>
                <option value="confirmed" @selected($status === 'confirmed')>Confirmed</option>
                <option value="cancelled" @selected($status === 'cancelled')>Cancelled</option>
                <option value="production" @selected($status === 'production')>Production</option>
                <option value="shipped" @selected($status === 'shipped')>Shipped</option>
                <option value="completed" @selected($status === 'completed')>Completed</option>
            </select>

            <input type="text"
                   name="user"
                   value="{{ $userFilter ?? '' }}"
                   placeholder="Show by user..."
                   class="px-3 py-2 text-sm border border-gray-300 rounded-lg text-gray-700"
                   onkeyup="if(event.key === 'Enter') this.form.submit()">
        </form>
    </div>

    <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 border-b">
                <tr>
                    <th class="px-5 py-3 text-left font-medium text-gray-600">ID</th>
                    <th class="px-5 py-3 text-left font-medium text-gray-600">Customer</th>
                    <th class="px-5 py-3 text-left font-medium text-gray-600">Supplier</th>
                    <th class="px-5 py-3 text-left font-medium text-gray-600">Status</th>
                    <th class="px-5 py-3 text-left font-medium text-gray-600">Total Price</th>
                    <th class="px-5 py-3 text-right font-medium text-gray-600">Created</th>
                    
                </tr>
            </thead>

            <tbody class="divide-y">
                @foreach($orders as $order)
                    <tr onclick="window.location='{{ route('admin.orders.show', $order->id) }}'"
                        class="hover:bg-gray-50 transition cursor-pointer">
                        <td class="px-5 py-3 font-semibold text-gray-900">
                            <div>{{ $order->id }}</div>
                            <div class="text-xs text-gray-400">{{ $order->type }}</div>
                        </td>

                        

                        <td class="px-5 py-3 text-gray-700">
                            {{ $order->user->name ?? 'User' }} {{ $order->user->last_name ?? '' }}
                        </td>

                        <td class="px-5 py-3 text-gray-700">

                        
                            @php
                                
                                    // обычный продукт
                                    $user_name = $order->items->first()?->product?->supplier?->user?->name ?? '';
                                    $user_last_name = $order->items->first()?->product?->supplier?->user?->last_name ?? '';
                                    $supplier = $order->items->first()?->product?->supplier?->name;
                                
                                    // RFQ — берем supplier из принятого оффера

                                    $acceptedOffer = $order->rfqOffer?->where('status', 'accepted')->first();
                                    $rfq_supplier = $acceptedOffer?->supplier;
                                    
                                
                                

                                
                            @endphp
                            <div>
                            @if ($order->type === 'product') 
                            {{ $order->items->first()?->product?->supplier?->user?->name ?? '' }} 
                            {{ $order->items->first()?->product?->supplier?->user?->last_name ?? '' }}
                            @elseif($order->type === 'rfq')
                            {{ $rfq_supplier->user->name ?? '-' }} {{ $rfq_supplier->user->last_name ?? '-' }}
                            
                            @endif
                            
                            
                            </div>
                            <span class="text-emerald-600 text-xs">
                            @if ($order->type === 'product') 
                            {{ $supplier ?? '-' }}
                            @elseif($order->type === 'rfq')
                            {{ $rfq_supplier->name ?? '-' }}
                            @endif
                        
                        </span>
                        </td>

                        <td class="px-5 py-3">
                            @php
                                $statusColors = [
                                    'pending'    => 'bg-yellow-50 text-yellow-700',
                                    'confirmed'  => 'bg-green-50 text-green-700',
                                    'cancelled'  => 'bg-red-50 text-red-700',
                                    'production' => 'bg-purple-50 text-purple-700',
                                    'shipped'    => 'bg-blue-50 text-blue-700',
                                    'completed'  => 'bg-gray-50 text-gray-700',
                                ];

                                $statusClass = $statusColors[$order->status] ?? 'bg-gray-100 text-gray-700';
                            @endphp

                            <span class="inline-flex px-2 py-1 text-xs font-medium {{ $statusClass }}">
                                {{ ucfirst($order->status) }}
                            </span>
                        </td>


                        <td class="px-5 py-3 text-right font-semibold text-gray-900">
                            ${{ number_format($order->items->sum(fn($i) => $i->quantity * $i->price), 2) }}
                        </td>

                        <td class="px-5 py-3 text-gray-600 text-xs">
                            <div>{{ $order->created_at->format('d M y') }}</div>
                            <div>{{ $order->created_at->format('H:i') }}</div>
                        </td>

                        

                        
                    </tr>
                @endforeach
            </tbody>
        </table>

        {{-- Пагинация для всех заказов --}}
        <div class="mt-4 px-5 py-3">
            {{ $orders->links('pagination::tailwind') }}
        </div>

    </div>

</div>
@endsection
