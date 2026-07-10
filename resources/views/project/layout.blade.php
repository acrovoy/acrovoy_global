@extends('dashboard.layout')

@section('dashboard-sidebar')

@include('project.partials.buyer.aside-panel', ['project' => $project])


@endsection




@section('dashboard-content')

@yield('project-content')

@endsection


