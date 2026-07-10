@extends('dashboard.layout')

@section('dashboard-content')

<div class="mb-6">

    {{-- HEADER --}}
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-semibold mt-1 text-gray-800">
                My Projects
            </h1>

            <p class="text-sm text-gray-500">
                Manage your projects, organize products and prepare procurement requests.
            </p>
        </div>

        <a href="{{ route('buyer.projects.create') }}"
           class="inline-flex items-center gap-2 mt-3 px-4 py-2
                  text-sm font-medium text-gray-700
                  bg-white border border-gray-200
                  rounded-lg
                  hover:bg-gray-50 hover:border-gray-300 hover:text-gray-900
                  active:scale-[0.98]
                  transition-all duration-150 shadow-sm">

            <span class="text-lg leading-none">+</span>
            Create Project
        </a>
    </div>

</div>

<x-alerts />

@if($projects->isEmpty())

<div class="text-center py-10 text-gray-500">
    No projects created yet.
</div>

@else

<div class="bg-white border rounded-xl shadow-sm overflow-hidden">

    <table class="w-full text-sm border-collapse">

        {{-- HEADER --}}
        <thead class="bg-gray-50 border-b">
            <tr>
                <th class="px-4 py-2 text-left font-medium">ID</th>
                <th class="px-4 py-2 text-left font-medium">Project</th>
                <th class="px-4 py-2 text-left font-medium">Status</th>
                
            </tr>
        </thead>

        {{-- BODY --}}
        <tbody class="divide-y divide-gray-100">

            @foreach($projects as $project)

            <tr
    class="hover:bg-gray-50 transition cursor-pointer"
    onclick="window.location='{{ route('buyer.projects.show', $project) }}'">

                {{-- ID --}}
                <td class="px-4 py-2 font-mono text-gray-800">
                    {{ $project->public_id }}
                </td>

                {{-- TITLE --}}
                <td class="px-4 py-2 text-gray-800">
                    {{ $project->title }}
                </td>

                {{-- STATUS --}}
                <td class="px-4 py-2">

                    <span class="inline-flex items-center px-2 py-1 text-xs font-medium rounded-lg {{ $project->status->badgeClasses() }}">
    {{ $project->status->label() }}
</span>

                </td>

                

            </tr>

            @endforeach

        </tbody>

    </table>

</div>

@endif



@endsection