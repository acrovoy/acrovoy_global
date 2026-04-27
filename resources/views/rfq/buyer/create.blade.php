@extends('dashboard.layout')

@section('dashboard-content')

<div class="bg-white border rounded-xl p-6">

    <h1 class="text-lg font-semibold mb-4">Create RFQ</h1>

<x-alerts />

<form method="POST" action="{{ route('buyer.rfqs.store') }}">
@csrf

<div class="space-y-4">

<div>
<label class="text-sm text-gray-600">Title</label>
<input type="text"
name="title"
class="w-full border rounded-lg px-3 py-2 text-sm">
</div>

<div>
<label class="text-sm text-gray-600">Description</label>
<textarea name="description"
class="w-full border rounded-lg px-3 py-2 text-sm"></textarea>
</div>

<div>
<label class="text-sm text-gray-600">Type</label>

<select name="type"
class="w-full border rounded-lg px-3 py-2 text-sm">

<option value="product">Product</option>
<option value="service">Service</option>
<option value="project">Project</option>

</select>
</div>

</div>

<div class="mt-6 flex justify-end">
<button class="px-4 py-2 bg-gray-900 text-white rounded-lg text-sm">
Create
</button>
</div>

</form>

</div>

@endsection