@extends('project.layout')

@section('project-content')

@php
    $isClosed = $project->status->isClosed();
    $activeTab = 'participants';

@endphp

{{-- BACK --}}
<a href="{{ route('buyer.projects.show', $project) }}"
   class="text-sm text-gray-500 hover:text-gray-900 transition">

    ← Back to Project
</a>

<x-alerts />

{{-- PROJECT PARTICIPANTS WORKSPACE --}}

<div class="bg-white border border-gray-200 rounded-lg shadow-sm p-5">

    {{-- HEADER --}}
    <div>

        <div class="text-sm text-gray-500">
            Project Participants
        </div>

        <div class="text-lg font-semibold text-gray-900">
            Manage suppliers invited to this Project
        </div>

        <div class="text-xs text-gray-500 mt-1">
            Invite suppliers and track their participation status
        </div>

    </div>

    @if($isClosed)

    <div class="mt-4 mb-4 p-3 rounded-lg border border-red-200 bg-red-50">

        <div class="text-sm font-medium text-red-700">
            Project Closed
        </div>

        <div class="text-xs text-red-600 mt-1">
            Participants, visibility settings and invitations are locked.
        </div>

    </div>

    @endif

    <div class="bg-white p-4 mb-3">

        <div class="font-semibold mb-3">
            Visibility
        </div>

        @if(!$isClosed)

        <form method="POST"
              action="{{ route('buyer.projects.visibility.update', $project) }}">

            @csrf
            @method('PATCH')

            <select
                name="visibility_type"
                onchange="this.form.submit()"
                class="w-full border border-gray-300 rounded px-3 py-2 text-sm">

                <option value="private"
                    {{ $project->visibility_type->value === 'private' ? 'selected' : '' }}>
                    🔒 Private (only invited suppliers)
                </option>

                <option value="category"
                    {{ $project->visibility_type->value === 'category' ? 'selected' : '' }}>
                    🧭 Category suppliers
                </option>

                <option value="platform"
                    {{ $project->visibility_type->value === 'platform' ? 'selected' : '' }}>
                    🌐 All platform suppliers
                </option>

                <option value="open"
                    {{ $project->visibility_type->value === 'open' ? 'selected' : '' }}>
                    🚀 Open Project
                </option>

            </select>

        </form>

        @else

        <div class="w-full border border-gray-200 bg-gray-100 rounded px-3 py-2 text-sm text-gray-600">
            {{ $project->visibility_type->label() }}
        </div>

        @endif

    </div>

    @if(!$isClosed)

        @include('project.components.participants-invite-panel', [
            'project' => $project,
            'suppliers' => $allparticipants,
            'visibility' => $project->visibility_type->value,
            'allparticipants' => $allparticipants,
        ])

    @else

    <div class="mt-4 p-4 border border-gray-200 rounded-lg bg-gray-50">

        <div class="text-sm font-medium text-gray-700">
            Project Closed
        </div>

        <div class="text-xs text-gray-500 mt-1">
            New suppliers can no longer be invited.
        </div>

    </div>

    @endif

    {{-- PARTICIPANTS LIST --}}
    <div class="space-y-3 mt-4">

        @forelse($participants as $participant)

        <div class="group flex items-center justify-between p-4 border border-gray-100 rounded-lg bg-white hover:border-gray-200 transition">

            {{-- LEFT --}}
            <div class="flex items-center gap-3">

                <div class="w-10 h-10 rounded-full bg-gray-100 flex items-center justify-center text-xs text-gray-500">
                    {{ strtoupper(substr($participant->participant?->name ?? 'S', 0, 1)) }}
                </div>

                <div>

                    <div class="text-sm font-medium text-gray-900">

                        @php($supplier = $participant->participant)

                        @if($supplier instanceof \App\Models\User)

                            {{ trim($supplier->name.' '.$supplier->last_name) }}

                            <span class="text-xs text-gray-400">
                                ({{ $supplier->email }})
                            </span>

                        @else

                            {{ $supplier?->name ?? 'Unknown supplier' }}

                        @endif

                    </div>

                    <div class="text-xs text-gray-500">
                        Invited {{ optional($participant->invited_at)->format('d M Y H:i') ?? '—' }}
                    </div>

                </div>

            </div>

            {{-- RIGHT --}}
            <div class="flex items-center gap-3">

                <span class="{{ $participant->status->badge() }}">
                    {{ $participant->status->label() }}
                </span>

                @if(!$isClosed)

                <form
                    method="POST"
                    action="{{ route('buyer.projects.participants.remove', [$project, $participant]) }}">

                    @csrf
                    @method('PATCH')

                    <button
                        type="submit"
                        class="text-xs text-gray-400 hover:text-red-600">

                        Remove

                    </button>

                </form>

                @endif

            </div>

        </div>

        @empty

        <div class="p-6 border border-dashed border-gray-200 rounded-lg text-center">

            <div class="text-sm text-gray-500">
                No suppliers invited yet
            </div>

        </div>

        @endforelse

    </div>

</div>

@endsection