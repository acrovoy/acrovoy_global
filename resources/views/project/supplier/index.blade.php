@extends('dashboard.layout')

@section('dashboard-content')

<div class="mb-6">

    {{-- HEADER --}}
    <div class="flex justify-between items-center">

        <div>

            <h1 class="text-2xl font-semibold mt-1 text-gray-800">
                Project Invitations
            </h1>

            <p class="text-sm text-gray-500">
                Projects where your company has been invited to participate.
            </p>

        </div>

    </div>

</div>

<x-alerts />

@if($projects->isEmpty())

<div class="text-center py-10 text-gray-500">
    No projects available.
</div>

@else

<div class="bg-white border rounded-xl shadow-sm overflow-hidden">

    <table class="w-full text-sm border-collapse">

        <thead class="bg-gray-50 border-b">

            <tr>

                <th class="px-4 py-2 text-left font-medium">
                    ID
                </th>

                <th class="px-4 py-2 text-left font-medium">
                    Project
                </th>

                <th class="px-4 py-2 text-left font-medium">
                    Status
                </th>

                <th class="px-4 py-2 text-left font-medium">
                    Invitation
                </th>

            </tr>

        </thead>

        <tbody class="divide-y divide-gray-100">

        @foreach($projects as $project)

            @php
                $participant = $project->participants
                    ->first(function ($item) {
                        return $item->participant_type === app(\App\Services\Company\ActiveContextService::class)->type()
                            && $item->participant_id === app(\App\Services\Company\ActiveContextService::class)->id();
                    });
            @endphp

            <tr
                class="hover:bg-gray-50 transition cursor-pointer"
                onclick="window.location='{{ route('supplier.projects.show', $project) }}'">

                <td class="px-4 py-2 font-mono text-gray-800">
                    {{ $project->public_id }}
                </td>

                <td class="px-4 py-2">
                    {{ $project->title }}
                </td>

                <td class="px-4 py-2">

                    <span class="inline-flex items-center px-2 py-1 text-xs font-medium rounded-lg {{ $project->status->badgeIndexClasses() }}">
                        {{ $project->status->label() }}
                    </span>

                </td>

                <td class="px-4 py-2">

                    @if($participant)

                        <span class="{{ $participant->status->badge() }}">
                            {{ $participant->status->label() }}
                        </span>

                    @endif

                </td>

            </tr>

        @endforeach

        </tbody>

    </table>

</div>

<div class="mt-6">
    {{ $projects->links() }}
</div>

@endif

@endsection