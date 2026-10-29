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
                                <td>{{ $u->name }}</td>
                                <td>{{ $u->email }}</td>
                                <td>{{ $u->role }}</td>
                                <td class="text-nowrap">{{ $u->created_at->format('M Y') }}</td>
                                <td>@include('admin.partials.status', ['status' => $u->status])</td>
                                <td class="text-end">
                                    @if ($u->isAdmin())
                                        <span class="text-muted small">Admin</span>
                                    @else
                                        <form action="{{ route('admin.users.status', $u->id) }}" method="post" class="d-inline">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="btn btn-sm btn-outline-dark">{{ $u->status === 'Blocked' ? 'Unblock' : 'Block' }}</button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @include('partials.paginator', ['paginator' => $users, 'navClass' => 'mt-3', 'class' => 'admin-pagination justify-content-end'])
        </div>
    </div>
@endsection
