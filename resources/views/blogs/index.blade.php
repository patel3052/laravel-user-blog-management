@extends('layouts.app')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2>Blogs</h2>
        <a href="{{ route('blogs.create') }}" class="btn btn-primary">Add Blog</a>
    </div>

    <div class="card mb-3">
        <div class="card-body">
            <form class="row g-3" id="filters">
                @if ($isAdmin)
                    <div class="col-md-4">
                        <label class="form-label">User</label>
                        <select id="filter-user" class="form-select">
                            <option value="">All</option>
                            @foreach ($users as $u)
                                <option value="{{ $u->id }}">{{ $u->name }}</option>
                            @endforeach
                        </select>
                    </div>
                @endif

                <div class="col-md-8">
                    <label class="form-label">Tags</label>
                    <select id="filter-tags" class="form-select" multiple>
                        @foreach ($tags as $t)
                            <option value="{{ $t->id }}">{{ $t->name }}</option>
                        @endforeach
                    </select>
                </div>
            </form>
        </div>
    </div>
    <div class="table-responsive mb-5">
        <table class="table table-bordered" id="blogs-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Title</th>
                    <th>User</th>
                    <th>Tags</th>
                    <th>Images</th>
                    <th>Action</th>
                </tr>
            </thead>
        </table>
    </div>

    <script>
        $(function() {
            let table = $('#blogs-table').DataTable({
                processing: true,
                serverSide: true,
                responsive: true,
                ajax: {
                    url: '{{ route('blogs.data') }}',
                    data: function(d) {
                        d.user_id = $('#filter-user').val();
                        d.tags = $('#filter-tags').val();
                    }
                },
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'title',
                        name: 'title'
                    },
                    {
                        data: 'user_name',
                        name: 'user.name'
                    },
                    {
                        data: 'tags_list',
                        name: 'tags.name',
                        orderable: false
                    }, {
                        data: 'images',
                        name: 'images',
                        orderable: false,
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

            $('#filter-user, #filter-tags').on('change', function() {
                table.ajax.reload();
            });
        });
    </script>

    <script>
        $(document).ready(function() {
            $('#filter-tags').select2({
                placeholder: "Select tags",
                allowClear: true,
                width: '100%'
            });
        });

        $(document).on('click', '.copy-link', function() {
            let link = $(this).data('link');
            navigator.clipboard.writeText(link).then(() => {
                showToast('Blog link copied to clipboard!', 'success');
            }).catch(err => {
                showToast('Failed to copy link', 'error');
                console.error('Failed to copy link: ', err);
            });
        });

        $(document).on('click', '.image-hover-wrapper', function(e) {
            if (window.innerWidth <= 768) {
                $(this).find('.image-hover-popup').toggle();
            }
        });
    </script>

@endsection
