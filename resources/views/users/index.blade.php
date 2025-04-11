@extends('layouts.main')

@section('content')
<!-- Page Heading -->
<h1 class="h3 mb-2 text-gray-800">User Management</h1>
<p class="mb-4">Manage all users in the system.</p>

<!-- DataTales Card -->
<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
        <h6 class="m-0 font-weight-bold text-primary">Users List</h6>
        <div>
            <button class="btn btn-primary btn-sm load-modal" data-url="{{ route('users.create') }}" data-title="Tambah User">
                <i class="fas fa-user-plus fa-sm"></i> Tambah User
            </button>
            <a href="{{ route('users.export.excel') }}" class="btn btn-success btn-sm">
                <i class="fas fa-file-excel fa-sm"></i> Export Excel
            </a>
            <a href="{{ route('users.export.pdf') }}" class="btn btn-danger btn-sm">
                <i class="fas fa-file-pdf fa-sm"></i> Export PDF
            </a>
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered" id="userTable" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Nama</th>
                        <th>Email</th>
                        <th>Actions</th>
                    </tr>
                </thead>
            
                <tbody>
                    @foreach($users as $index => $u)
                    <tr>
                    <td>{{ $index + 1 }}</td>
                        <td>{{ $u->name }}</td>
                        <td>{{ $u->email }}</td>
                        <td>
                            <a href="#" class="btn btn-info btn-circle btn-sm load-modal" data-url="{{ route('users.show', $u->id) }}" data-title="View User">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="#" class="btn btn-warning btn-circle btn-sm load-modal" data-url="{{ route('users.edit', $u->id) }}" data-title="Edit User">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="{{ route('users.destroy', $u->id) }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="button" class="btn btn-danger btn-circle btn-sm delete-confirm">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function () {
        $('#userTable').DataTable({
            responsive: true
        });
    });
</script>
@endpush
