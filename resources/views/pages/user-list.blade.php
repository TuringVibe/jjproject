@extends('layouts.master')

@push('head')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="{{ asset('css/content.css') }}">
@endpush

@section('content')
    <div class="content container-fluid">
        @include('components.content-header',[
                'with_btn' => true,
                'btn_label' => 'Create Users',
                'action' => 'openModal()'
            ]
        )
        <div class="card">
            <h4 class="card-header">Users</h4>
            <div class="card-body">
                <div class="form-row">
                    <div class="col-auto form-group">
                        <label for="filter-role">Role</label>
                        <select id="filter-role" class="form-control">
                            <option value="">All role</option>
                            <option value="user">User</option>
                            <option value="admin">Admin</option>
                        </select>
                    </div>
                    <div class="col-auto form-group">
                        <label for="name">Name</label>
                        <input type="text" id="filter-name" class="form-control">
                    </div>
                    <div class="col-auto form-group align-self-end">
                        <button id="filter-button" class="btn btn-default" type="button" onclick="document.querySelector('#list').dispatchEvent(new CustomEvent('mutated'))">Filter</button>
                    </div>
                </div>
                <table id="list" class="table table-striped table-bordered">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Projects</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
    @include('components.popup-user')
@endsection

@push('scripts')
    <script src="https://cdn.datatables.net/1.10.24/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.24/js/dataTables.bootstrap4.min.js"></script>
    <script>
        function deleteData(elem) {
            Swal.fire({
                title: 'Are you sure ?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        method: 'POST',
                        url: '{{route("users.delete")}}',
                        data: { id: $(elem).data('id') },
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        }
                    }).done((res) => {
                        if(res == true) {
                            Swal.fire(
                                'Deleted!',
                                'Your data has been deleted.',
                                'success'
                            )
                            document.querySelector('#list').dispatchEvent(new CustomEvent('mutated'));
                        } else {
                            Swal.fire(
                                'Failed!',
                                'The data has\'nt been deleted succesfully.',
                                'error'
                            )
                        }
                    }).fail((jqXHR) => {
                        Swal.fire(
                            'Failed!',
                            'The data has\'nt been deleted succesfully.',
                            'error'
                        )
                    });
                }
            });
        }

        function openModal() {
            $('#popup-user').modal('show');
        }

    </script>
@endpush

@push('ready-scripts')
    var table = $('#list').DataTable({
        paging: false,
        searching: false,
        processing: true,
        ajax: {
            url: '{{ route('users.data') }}',
            dataSrc: '',
            data: function(d) {
                d.name = $('#filter-name').val();
                d.role = $('#filter-role').val();
            }
        },
        columns: [
            {
                data: null,
                render: (data, type, row, meta) => {
                    return row.firstname+' '+row.lastname;
                }
            },
            {data: 'email'},
            {data: 'role'},
            {data: 'projects_count'},
            {
                data: null,
                width: "90px",
                render: (data, type, row, meta) => {
                    return '<button class="table-action-icon" type="button" data-toggle="modal" data-target="#popup-user" data-action="edit"'+
                        'data-id="'+row.id+'"><i class="fas fa-pen"></i></button>'+
                        '<button class="table-action-icon" data-id="'+row.id+'" type="button" onclick="deleteData(this)"><i class="fas fa-trash"></i></button>';
                }
            }
        ]
    });

    $('#list').on('mutated', (e) => {
        table.ajax.reload();
    });
@endpush
