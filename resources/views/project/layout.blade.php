@extends('dashboard.layout')

@section('dashboard-sidebar')

@include('project.partials.buyer.aside-panel', ['project' => $project])


@endsection




@section('dashboard-content')

@yield('project-content')

<x-conversation.drawer
    subjectType="App\Domain\Project\Models\Project"
    :subjectId="$project->id"
    :messagesUrl="url('/dashboard/supplier/messenger/conversations')"
/>

@endsection


