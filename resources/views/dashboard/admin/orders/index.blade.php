@extends('dashboard.admin.layout')

@section('dashboard-content')
<div class="flex justify-between items-center mb-6">
    <h1 class="text-2xl font-bold">Orders with Opened Dispute</h1>

    <form method="GET" class="flex gap-2 items-center">
        {{-- Сортировка --}}
        <select name="sort" onchange="this.form.submit()" class="border rounded-md px-2 py-1 text-sm">
            <option value="">Newest</option>
            <option value="oldest" @selected($sort === 'oldest')>Oldest</option>
            <option value="status" @selected($sort === 'status')>Status</option>
        </select>

       
    </form>

    
</div>

<table class="w-full border rounded text-sm mb-6">
    <thead class="bg-gray-100">
        <tr>
            <th class="px-4 py-2">ID</th>
            <th class="px-4 py-2">Customer</th>
            <th class="px-4 py-2">User</th>
            <th class="px-4 py-2">Dispute Status</th>
            <th class="px-4 py-2">Created</th>
            <th class="px-4 py-2">Total</th>
            <th class="px-4 py-2 text-right">Actions</th>
        </tr>
    </thead>
    <tbody>
        @foreach($ordersWithOpenDisputes as $order)
<tr class="border-t align-top">
    <td class="px-4 py-2">{{ $order->id }}</td>
    <td class="px-4 py-2">{{ $order->first_name }} {{ $order->last_name }}</td>
    <td class="px-4 py-2">{{ $order->user->name ?? 'User' }}</td>

    @php
        $openStatuses = ['pending', 'supplier_offer', 'rejected', 'admin_review'];
        $dispute = $order->disputes->firstWhere(fn($d) => in_array($d->status, $openStatuses));
    @endphp

    <td class="px-4 py-2 
        @if($dispute && $dispute->status === 'admin_review') bg-red-100 text-red-800 font-semibold @endif">
        {{ $dispute ? ucfirst(str_replace('_',' ', $dispute->status)) : '-' }}
    </td>

    <td class="px-4 py-2">{{ $order->created_at->format('Y-m-d H:i') }}</td>
    <td class="px-4 py-2 text-right font-semibold">
        ${{ number_format($order->items->sum(fn($i) => $i->quantity * $i->price), 2) }}
    </td>

    <td class="px-4 py-2 text-right space-x-2">
        <a href="{{ route('admin.orders.show', $order->id) }}" 
           class="text-blue-600 hover:underline">View</a>
    </td>
</tr>
@endforeach
    </tbody>
</table>



<div class="flex justify-between items-center mb-6">
    <h1 class="text-2xl font-bold">Orders with Transport Request</h1>

    <form method="GET" class="flex gap-2 items-center">
        {{-- Сортировка --}}
        <select name="sort" onchange="this.form.submit()" class="border rounded-md px-2 py-1 text-sm">
            <option value="">Newest</option>
            <option value="oldest" @selected($sort === 'oldest')>Oldest</option>
            <option value="status" @selected($sort === 'status')>Status</option>
        </select>

       
    </form>

    
</div>

<table class="w-full border rounded text-sm mb-6">
    <thead class="bg-gray-100">
        <tr>
            <th class="px-4 py-2">ID</th>
            <th class="px-4 py-2">Customer</th>
            <th class="px-4 py-2">Supplier</th>
            
            <th class="px-4 py-2">Created</th>
            <th class="px-4 py-2">Total</th>
            <th class="px-4 py-2 text-right">Actions</th>
        </tr>
    </thead>
    <tbody>

   
        @foreach($ordersWithTransportRequest as $order)
<tr class="border-t align-top">
    <td class="px-4 py-2">{{ $order->id }}</td>

    <td class="px-4 py-2">
        {{ $order->user->name ?? 'User' }} {{ $order->user->last_name ?? '' }}
    </td>

    <td class="px-4 py-2">
        {{ $order->first_name }} {{ $order->last_name }}
    </td>

    <td class="px-4 py-2">
        {{ $order->created_at->format('Y-m-d H:i') }}
    </td>

    <td class="px-4 py-2 text-right font-semibold">
        ${{ number_format($order->items->sum(fn($i) => $i->quantity * $i->price), 2) }}
    </td>

    <td class="px-4 py-2 text-right space-x-2">
        <a href="{{ route('admin.orders.show', $order->id) }}" 
           class="text-blue-600 hover:underline">View</a>
    </td>
</tr>
@endforeach
    </tbody>
</table>




<div class="flex justify-between items-center mb-6">
    <h1 class="text-2xl font-bold">Orders</h1>

    <form method="GET" class="flex gap-2 items-center">
        {{-- Сортировка --}}
        <select name="sort" onchange="this.form.submit()" class="border rounded-md px-2 py-1 text-sm">
            <option value="">Newest</option>
            <option value="oldest" @selected($sort === 'oldest')>Oldest</option>
            <option value="status" @selected($sort === 'status')>Status</option>
        </select>

        {{-- Фильтр по статусу --}}
        <select name="status" onchange="this.form.submit()" class="border rounded-md px-2 py-1 text-sm">
            <option value="">All</option>
            <option value="pending" @selected($status === 'pending')>Pending</option>
            <option value="confirmed" @selected($status === 'confirmed')>Confirmed</option>
            <option value="cancelled" @selected($status === 'cancelled')>Cancelled</option>
            <option value="production" @selected($status === 'production')>Production</option>
            <option value="shipped" @selected($status === 'shipped')>Shipped</option>
            <option value="completed" @selected($status === 'completed')>Completed</option>
        </select>

        {{-- Поиск по пользователю --}}
        <input type="text" name="user" value="{{ $userFilter ?? '' }}" placeholder="Show by user..." 
               class="border rounded-md px-2 py-1 text-sm" 
               onkeyup="if(event.key === 'Enter') this.form.submit()">
    </form>

    
</div>

<table class="w-full border rounded text-sm">
    <thead class="bg-gray-100">
        <tr>
            <th class="px-4 py-2">ID</th>
            <th class="px-4 py-2">Customer</th>
            <th class="px-4 py-2">User</th>
            <th class="px-4 py-2">Status</th>
            <th class="px-4 py-2">Created</th>
            <th class="px-4 py-2">Total</th>
            <th class="px-4 py-2 text-right">Actions</th>
        </tr>
    </thead>
    <tbody>
        @foreach($orders as $order)
<tr class="border-t align-top">
    <td class="px-4 py-2">{{ $order->id }}</td>

    <td class="px-4 py-2">
        {{ $order->first_name }} {{ $order->last_name }}
    </td>

    <td class="px-4 py-2">
        {{ $order->user->name ?? 'User' }} {{ $order->user->last_name ?? '' }}
    </td>

    <td class="px-4 py-2">
        {{ ucfirst($order->status) }}
    </td>

    <td class="px-4 py-2">
        {{ $order->created_at->format('Y-m-d H:i') }}
    </td>

    <td class="px-4 py-2 text-right font-semibold">
        ${{ number_format($order->items->sum(fn($i) => $i->quantity * $i->price), 2) }}
    </td>

    <td class="px-4 py-2 text-right space-x-2">
        <a href="{{ route('admin.orders.show', $order->id) }}" 
           class="text-blue-600 hover:underline">View</a>
    </td>
</tr>
@endforeach
    </tbody>
</table>



@endsection
