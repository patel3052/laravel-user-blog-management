@extends('layouts.app')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2>Users</h2>
        <a href="{{ route('users.create') }}" class="btn btn-primary">Add User</a>
    </div>
    <div class="table-responsive mb-5">
        <table class="table table-bordered" id="users-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Mobile</th>
                    <th>Gender</th>
                    <th>Status</th>
                    <th>Profile</th>
                    <th>Total Blogs</th>
                    <th>Action</th>
                </tr>
            </thead>
        </table>
    </div>

    <script>
        $(function() {
            $('#users-table').DataTable({
                processing: true,
                serverSide: true,
                responsive: true,
                ajax: '{{ route('users.data') }}',
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'name',
                        name: 'name'
                    },
                    {
                        data: 'email',
                        name: 'email'
                    },
                    {
                        data: 'mobile',
                        name: 'mobile'
                    },
                    {
                        data: 'gender',
                        name: 'gender'
                    },
                    {
                        data: 'status',
                        name: 'status'
                    },
                    {
                        data: 'profile_image',
                        name: 'profile_image',
                        orderable: false,
                        searchable: false
                    }, {
                        data: 'blogs_count',
                        name: 'blogs_count',
                        searchable: false
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    }
                ]
            });
        });
    </script>
@endsection
