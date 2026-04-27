@extends('dashboard.layout')

@section('dashboard-content')
<div class="flex flex-col gap-6">

    <div>
        <h2 class="text-2xl font-semibold text-gray-900">
            Roles & Permissions
        </h2>

        <p class="text-sm text-gray-500">
            Control what team members can access
        </p>
    </div>


    <div class="grid md:grid-cols-2 gap-4">


        @foreach($roles ?? [] as $role)

        <div class="bg-white border border-gray-200 rounded-xl shadow-sm p-5">

            <div class="text-lg font-semibold text-gray-900">
                {{ ucfirst($role['name']) }}
            </div>


            <ul class="text-sm text-gray-600 mt-2 space-y-1">

                @foreach($role['permissions'] as $permission)

                <li>
                    • {{ $permission }}
                </li>

                @endforeach

            </ul>

        </div>

        @endforeach


    </div>

</div>
@endsection