@extends('admin.layouts.app')
@section('title', 'Manage Users')
@section('page_title', 'User List')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <form method="GET" class="d-flex gap-2">
        <input type="text" name="search" class="form-control" placeholder="Search name, email..." value="{{ request('search') }}" style="max-width:250px">
        <select name="role" class="form-select" style="max-width:150px" onchange="this.form.submit()">
            <option value="">All Roles</option>
            <option value="customer" {{ request('role') == 'customer' ? 'selected' : '' }}>Customers</option>
            <option value="admin" {{ request('role') == 'admin' ? 'selected' : '' }}>Admin</option>
        </select>
        <button type="submit" class="btn btn-dark">Filter</button>
    </form>
</div>

<div class="card shadow-sm border-0">
    <div class="table-responsive">
        <table class="table table-hover mb-0 align-middle">
            <thead class="table-light">
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Status</th>
                    <th>Registration Date</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($users ?? [] as $user)
                <tr>
                    <td class="fw-bold">{{ $user->id }}</td>
                    <td>
                        <div class="d-flex align-items-center">
                            <div class="bg-secondary text-white rounded-circle d-flex align-items-center justify-content-center me-2" style="width: 32px; height: 32px;">
                                {{ strtoupper(substr($user->name, 0, 1)) }}
                            </div>
                            <div>
                                <p class="mb-0 fw-semibold">{{ $user->name }}</p>
                                <small class="text-muted">{{ $user->phone }}</small>
                            </div>
                        </div>
                    </td>
                    <td>{{ $user->email }}</td>
                    <td>
                        @if($user->isAdmin())
                            <span class="badge bg-primary">Admin</span>
                        @else
                            <span class="badge bg-secondary">Customers</span>
                        @endif
                    </td>
                    <td>
                        @if($user->is_active)
                            <span class="badge bg-success">Active</span>
                        @else
                            <span class="badge bg-danger">Locked</span>
                        @endif
                    </td>
                    <td>{{ $user->created_at ? $user->created_at->format('d/m/Y') : '' }}</td>
                    <td class="text-end">
                        <div class="btn-group" role="group">
                            <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#editUserModal{{ $user->id }}">Edit</button>
                        </div>

                        <!-- Edit Modal -->
                        <div class="modal fade text-start" id="editUserModal{{ $user->id }}" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <form action="{{ route('admin.customers.update', $user->id) ?? '#' }}" method="POST">
                                        @csrf @method('PUT')
                                        <div class="modal-header">
                                            <h5 class="modal-title">Update User</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="mb-3">
                                                <label class="form-label">Name</label>
                                                <input type="text" name="name" class="form-control" value="{{ $user->name }}" required>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Email</label>
                                                <input type="email" name="email" class="form-control" value="{{ $user->email }}" required>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Role</label>
                                                <select name="role" class="form-select">
                                                    <option value="customer" {{ !$user->isAdmin() ? 'selected' : '' }}>Customers (Customer)</option>
                                                    <option value="admin" {{ $user->isAdmin() ? 'selected' : '' }}>Admin (Admin)</option>
                                                </select>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Account Status</label>
                                                <select name="is_active" class="form-select">
                                                    <option value="1" {{ $user->is_active ? 'selected' : '' }}>Active</option>
                                                    <option value="0" {{ !$user->is_active ? 'selected' : '' }}>Lock Account</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                            <button type="submit" class="btn btn-primary">Save Changes</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center py-4 text-muted">No users found.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if(isset($users) && method_exists($users, 'links'))
    <div class="card-footer bg-white pt-3 border-0">
        {{ $users->links() }}
    </div>
    @endif
</div>
@endsection
