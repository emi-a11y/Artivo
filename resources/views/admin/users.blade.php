@extends('layouts.admin')
@section('title', 'Users')
@section('heading', 'Users')

@section('content')
    <div class="card">
        <div class="card-body">
            @include('admin.partials.tabs', ['tabs' => $tabs, 'param' => 'role', 'label' => 'User role'])
            <div class="table-responsive">
                <table class="table admin-table align-middle mb-0">
                    <thead>
                        <tr><th scope="col">Name</th><th scope="col">Email</th><th scope="col">Role</th><th scope="col">Joined</th><th scope="col">Status</th><th scope="col" class="text-end">Actions</th></tr>
                    </thead>
                    <tbody>
                        @foreach ($users as $u)
                            <tr>
                                <td>{{ $u['name'] }}</td>
                                <td>{{ $u['email'] }}</td>
                                <td>{{ $u['role'] }}</td>
                                <td>{{ $u['joined'] }}</td>
                                <td>@include('admin.partials.status', ['status' => $u['status']])</td>
                                <td class="text-end">
                                    <button type="button" class="btn btn-sm btn-outline-dark" data-proto="Prototype: viewing {{ $u['name'] }} is simulated.">View</button>
                                    <button type="button" class="btn btn-sm btn-outline-dark" data-proto="Prototype: {{ $u['name'] }} was not blocked. Nothing is saved.">Block</button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @include('admin.partials.pagination', ['pages' => $pages])
        </div>
    </div>
@endsection
