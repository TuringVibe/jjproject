@extends('layouts.master')

@push('head')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="{{ asset('lib/DataTables/datatables.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/content.css') }}">
    <style>
        .user-img-cont {
            display: inline-flex;
            flex-wrap: wrap;
            gap: 5px;
        }
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

        .label-list {
            display: inline-block;
            padding: .3rem;
            border-radius: 5px;
            color: white;
        }

        .label-list:not(:first-child) {
            margin-left: 5px;
        }

        table#list.dataTable tbody tr {
            cursor: pointer;
        }

    </style>
@endpush

@section('content')
    <div class="content container-fluid">
        @include('components.content-header',['with_btn' => false])
        <div class="card">
            <h4 class="card-header">Task</h4>
            <div class="card-body">
                <div class="form-row">
                    <div class="col-auto form-group">
                        <label for="filter-project">Project</label>
                        <select id="filter-project" class="select2">
                            <option value="">-- All Project --</option>
                            @foreach ($projects as $project)
                                <option value="{{ $project['id'] }}">{{ $project['name'] }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-auto form-group">
                        <label for="filter-project-label">Project Label</label>
                        <select id="filter-project-label" class="select2">
                            <option value="">-- All Project Label --</option>
                            @foreach ($project_labels as $label)
                                <option value="{{ $label['id'] }}">{{ $label['name'] }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-auto form-group">
                        <label for="filter-duedate">Due Date</label>
                        <input type="date" id="filter-duedate" class="form-control">
                    </div>
                    @can('viewAny', App\Models\Task::class)
                        <div class="col-auto form-group">
                            <label for="filter-user">User</label>
                            <select id="filter-user" class="select2">
                                <option value="">-- All User --</option>
                                @foreach ($users as $user)
                                    <option value="{{ $user['id'] }}">{{ $user['firstname'] . ' ' . $user['lastname'] }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    @endcan
                    <div class="col-auto form-group">
                        <label for="filter-priority">Priority</label>
                        <select id="filter-priority" class="form-control">
                            <option value="">-- All Priority --</option>
                            <option value="low">Low</option>
                            <option value="medium">Medium</option>
                            <option value="high">High</option>
                        </select>
                    </div>
                    <div class="col-auto form-group">
                        <label for="filter-status">Status</label>
                        <div class="my-2">
                            <div class="form-check form-check-inline">
                                <input id="status-todo" name="status[]" value="todo" type="checkbox" class="form-check-input" checked>
                                <label for="status-todo" class="form-check-label">To do</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input id="status-inprogress" name="status[]" value="inprogress" type="checkbox" class="form-check-input" checked>
                                <label for="status-inprogress" class="form-check-label">In Progress</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input id="status-done" name="status[]" value="done" type="checkbox" class="form-check-input">
                                <label for="status-done" class="form-check-label">Done</label>
                            </div>
                        </div>
                    </div>
                    <div class="col-auto form-group">
                        <label for="filter-name">Name</label>
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
                            <th>Project</th>
                            <th>Project Labels</th>
                            <th>Users</th>
                            <th>Due Date</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="{{ asset('lib/DataTables/datatables.min.js') }}"></script>
    <script src="{{ asset('lib/moment-with-locales.min.js') }}"></script>
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
                        url: '{{ route('project-labels.delete') }}',
                        data: {
                            id: $(elem).data('id')
                        },
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        }
                    }).done((res) => {
                        if (res == true) {
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
        url: '{{ route('tasks.data') }}',
        dataSrc: '',
        data: function(d) {
            d.project_id = $('#filter-project').val();
            d.project_label_id = $('#filter-project-label').val();
            d.due_date = $('#filter-duedate').val();
            d.user_id = $('#filter-user').val();
            d.status = [];
            $('[name="status[]"]:checked').each(function() {
                d.status.push(this.value)
            });
            d.priority = $('#filter-priority').val();
            d.name = $('#filter-name').val();
        }
        },
        columns: [
            {data: 'name'},
            {data: 'project.name'},
            {
                data: null,
                render: (data, type, row, meta) => {
                    var html = '';
                    for(label of row.project.labels) {
                        html += '<span class="label-list" style="background-color:'+label.color+'">'+label.name+'</span>';
                    }
                    return html;
                }
            },
            {
                data: null,
                render: (data, type, row, meta) => {
                    console.log(data,type,row,meta)
                    let html = '<div class="user-img-cont">';
                    for(user of row.users) {
                        const img_url = '/storage/'+user.img_path;
                        const firstnameInitial = user.firstname.charAt(0);
                        const lastnameInitial = user.lastname !== null ? user.lastname.charAt(0) : '';
                        const nameInitial = user.img_path !== null ? '' : firstnameInitial+lastnameInitial;
                        const imgPath = user.img_path !== null ? 'style="background-image: url(\''+img_url+'\')"' : '';
                        html += '<span class="user-img" '+imgPath+'>'+nameInitial+'</span>';
                    }
                    html += '</div>';
                    return html;
                }
            },
            {
                data: 'due_date',
                render: (data) => {
                    if(data) return moment(data).format('LL');
                    else return '-';
                }
            },
            {data: 'status'}
        ]
    });

    $('#list').on('mutated', (e) => {
        table.ajax.reload();
    });

    $("#list tbody").on('click','tr',function(e) {
        if($(e.target).is("tr,td")) {
            var data = table.row(this).data();
            window.location.href = '/projects/board?id='+data.project_id+'&task_id='+data.id;
        }
    });
@endpush
