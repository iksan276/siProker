<div class="table-responsive">
    <table class="table table-bordered" width="100%" cellspacing="0">
        <tr>
            <th width="30%">ID</th>
            <td>{{ $user->id }}</td>
        </tr>
        <tr>
            <th>Name</th>
            <td>{{ $user->name }}</td>
        </tr>
        <tr>
            <th>Email</th>
            <td>{{ $user->email }}</td>
        </tr>
        <tr>
            <th>Level</th>
            <td>
            @if($user->level == 1)
                <span class="badge badge-primary">Admin</span>
            @endif
            @if($user->level == 2)
                <span class="badge badge-secondary">User</span>
            @endif
            </td>
        </tr>
        <tr>
            <th>Created At</th>
            <td>{{ $user->created_at }}</td>
        </tr>
        <tr>
            <th>Ubahd At</th>
            <td>{{ $user->updated_at }}</td>
        </tr>
    </table>
</div>
</div>
<div class="modal-footer">
<button class="btn btn-secondary" type="button" data-dismiss="modal">Tutup</button>
</div>
