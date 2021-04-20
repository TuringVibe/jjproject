@extends('layouts.master')

@push('head')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="{{ asset('css/content.css') }}">
    <style>
        .user-img {
            display: inline-flex;
            justify-content: center;
            vertical-align: middle;
            align-items: center;
            width: 3rem;
            height: 3rem;
            border-radius: 50%;
            border: 2px solid white;
            text-transform: uppercase;
            color: white;
            font-weight: bold;
            background-color: var(--com-bg-color-default);
            background-size: cover;
            background-position: center;
        }
        .user-img:not(:first-child) {
            margin-left: -10px;
        }
        .label-list {
            display: inline-block;
            padding: .3rem;
            border-radius: 5px;
            color: white;
        }
        .label-list:not(:first-child) {
            margin-left: 5px;
        }
        table#list.dataTable tbody tr{
            cursor: pointer;
        }
    </style>
@endpush

@section('content')
    <div class="content container-fluid">
        @include('components.content-header',[
                'with_btn' => request()->user()->can('create',App\Models\Project::class),
                'btn_label' => 'Create Project',
                'action' => 'openModal()'
            ]
        )
        <div class="card">
            <h4 class="card-header">Project</h4>
            <div class="card-body">
                <div class="form-row">
                    <div class="col-auto form-group">
                        <label for="filter-status">Status</label>
                        <select id="filter-status" class="form-control">
                            <option value="">All Status</option>
                            <option value="notstarted">Not Started</option>
                            <option value="ongoing">On Going</option>
                            <option value="complete">Complete</option>
                            <option value="onhold">On Hold</option>
                            <option value="canceled">Canceled</option>
                        </select>
                    </div>
                    @can('viewAny',App\Models\Project::class)
                    <div class="col-auto form-group">
                        <label for="filter-user">User</label>
                        <select id="filter-user" class="form-control">
                            <option value="">All Users</option>
                            @foreach ($users as $user)
                                <option value="{{$user['id']}}">{{$user['firstname'].' '.$user['lastname']}}</option>
                            @endforeach
                        </select>
                    </div>
                    @endcan
                    <div class="col-auto form-group">
                        <label for="filter-label">Label</label>
                        <select id="filter-label" class="form-control">
                            <option value="">All Labels</option>
                            @foreach ($labels as $label)
                                <option value="{{$label['id']}}">{{$label['name']}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-auto form-group">
                        <label for="name">Name</label>
                        <input type="text" id="filter-name" class="form-control">
                    </div>
                    <div class="col-auto form-group align-self-end">
                        <button id="filter-button" class="btn btn-default" type="button">Filter</button>
                    </div>
                </div>
                <table id="list" class="table table-striped table-bordered">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Total Tasks</th>
                            <th>Users</th>
                            <th>Labels</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
    @include('components.popup-project')
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
                        url: '{{route("projects.delete")}}',
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
            $('#popup-project').modal('show');
        }

    </script>
@endpush

@push('ready-scripts')
    $('#filter-button').on('click',(e) => {
        document.querySelector('#list').dispatchEvent(new CustomEvent('mutated'));
    });
    $('#filter-name').on('keyup',(e) => {
        if(e.key === "Enter") $('#filter-button').click();
    });
    var table = $('#list').DataTable({
        order: [],
        paging: false,
        searching: false,
        processing: true,
        ajax: {
            url: '{{ route('projects.data') }}',
            dataSrc: '',
            data: function(d) {
                d.status = $('#filter-status').val();
                d.user_id = $('#filter-user').val();
                d.project_label_id = $('#filter-label').val();
                d.name = $('#filter-name').val();
            }
        },
        columns: [
            {data: 'name'},
            {
                data: 'tasks_count',
                width: "100px",
            },
            {
                data: null,
                render: (data, type, row, meta) => {
                    var html = '';
                    for(user of row.users) {
                        var img_url = '/storage/'+user.img_path;
                        var firstnameInitial = user.firstname.charAt(0);
                        var lastnameInitial = user.lastname !== null ? user.lastname.charAt(0) : '';
                        var nameInitial = user.img_path !== null ? '' : firstnameInitial+lastnameInitial;
                        var imgPath = user.img_path !== null ? 'style="background-image: url(\''+img_url+'\')"' : '';
                        html += '<span class="user-img" '+imgPath+'>'+nameInitial+'</span>';
                    }
                    return html;
                }
            },
            {
                data: null,
                render: (data, type, row, meta) => {
                    var html = '';
                    for(label of row.labels) {
                        html += '<span class="label-list" style="background-color:'+label.color+'">'+label.name+'</span>';
                    }
                    return html;
                }
            },
            {
                data: 'status',
                width: "100px",
            },
            {
                visible: @json(auth()->user()->role == "admin"),
                data: null,
                width: "90px",
                render: (data, type, row, meta) => {
                    htmlUpdate = '<button class="table-action-icon" type="button" data-toggle="modal" data-target="#popup-project" data-action="edit" data-id="'+row.id+'"><i class="fas fa-pen"></i></button>';
                    htmlDelete = '<button class="table-action-icon" type="button" data-id="'+row.id+'" onclick="deleteData(this)"><i class="fas fa-trash"></i></button>';
                    if(!row.can_update) htmlUpdate = '';
                    if(!row.can_delete) htmlDelete = '';
                    return htmlUpdate+htmlDelete;
                }
            }
        ]
    });

    $('#list').on('mutated', (e) => {
        table.ajax.reload();
    });

    $("#list tbody").on('click','tr',function(e) {
        if($(e.target).is("tr,td")) {
            var data = table.row(this).data();
            window.location.href = '/projects/detail?id='+data.id;
        }
    });
@endpush
