@extends('layouts.master')

@push('head')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="{{ asset('lib/DataTables/datatables.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/content.css') }}">
    <style>
        .user-img {
            display: inline-flex;
            justify-content: center;
            vertical-align: middle;
            align-items: center;
            width: 4rem;
            height: 4rem;
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
            margin-right: 5px;
        }
        .detail .row:not(:last-child) {
            margin-bottom: 1.5rem;
        }
        .status{
            display: inline-block;
            padding: .1rem .2rem;
            border-radius: 5px;
            background-color: var(--com-bg-color-default);
            color: white;
        }
    </style>
@endpush

@section('content')
    <div class="content container-fluid">
        @include('components.content-header',[
            'with_btn' => true,
            'btn_label' => 'Project Tasks',
            'action' => 'goToKanbanBoard()'
        ])
        <div class="row">
            <div class="col-sm-5">
                <div class="row mb-3">
                    <div class="col">
                        <div class="card">
                            <h4 class="card-header">Tasks Overview</h4>
                            <div class="card-body">
                                @if($tasks_statistic['total'] == 0)
                                    <span>There is no task</span>
                                @else
                                    <canvas id="chart-tasks-overview"></canvas>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-4">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title">To Do</h5>
                                <p class="h5">{{$tasks_statistic['todo']}}</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title">In Progress</h5>
                                <p class="h5">{{$tasks_statistic['inprogress']}}</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title">Done</h5>
                                <p class="h5">{{$tasks_statistic['done']}}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-7">
                <div class="row mb-3">
                    <div class="col">
                        <div class="card">
                            <div class="card-body detail">
                                <h2 class="card-title">{{$detail['name']}}</h2>
                                <div class="row justify-content-between">
                                    <div class="col">
                                        <span class="status">{{$detail['status']}}</span>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-auto">
                                        <h5>Start Date</h5>
                                        <p class="card-text">{{$detail['startdate']}}</p>
                                    </div>
                                    <div class="col-auto">
                                        <h5>End Date</h5>
                                        <p class="card-text">{{$detail['enddate']}}</p>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col">
                                        <h5>Description</h5>
                                        <p class="card-text">{{$detail['description']}}</p>
                                    </div>
                                </div>
                                @if(!empty($detail['users']))
                                <div class="row">
                                    <div class="col">
                                        <h5>Users</h5>
                                        @foreach($detail['users'] as $user)
                                            @php
                                                $img_url = asset('storage/'.$user['img_path']);
                                                $firstname_initial = $user['firstname'][0];
                                                $lastname_initial = $user['lastname'] !== null ? $user['lastname'][0] : '';
                                                $name_initial = $user['img_path'] !== null ? '' : $firstname_initial.$lastname_initial;
                                            @endphp
                                            <span class="user-img" @isset($user['img_path']) style="background-image: url('{{$img_url}}')"@endisset>{{$name_initial}}</span>
                                        @endforeach
                                    </div>
                                </div>
                                @endif
                                @if(!empty($detail['labels']))
                                <div class="row">
                                    <div class="col">
                                        <h5>Labels</h5>
                                        @foreach($detail['labels'] as $label)
                                            <span class="label-list" style="background-color:{{$label['color']}}">{{$label['name']}}</span>
                                        @endforeach
                                    </div>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-6">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title">Days Left</h5>
                                <p class="h5">{{$detail['days_left']}}</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title">Budget</h5>
                                <p class="h5">USD {{ number_format($detail['budget'],0,'.',',')}}</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-6">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title">Total Tasks</h5>
                                <p class="h5">{{$tasks_statistic['total']}}</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title">Total Comments</h5>
                                <p class="h5">{{$detail['comments_count']}}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mb-3">
            <div class="col">
                <div class="card">
                    <h4 class="card-header">Project Files</h4>
                    <div class="card-body">
                        <form id="file-upload-form" class="mb-3" enctype="multipart/form-data">
                            <div class="form-row align-items-center">
                                <div class="col-auto">
                                    <div class="custom-file">
                                        <input type="file" class="custom-file-input" id="file" name="file" aria-describedby="file-help-block validate-file">
                                        <label class="custom-file-label" for="file">Choose image file...</label>
                                        <div id="validate-file" class="invalid-feedback"></div>
                                    </div>
                                </div>
                                <div class="col-auto">
                                    <button type="submit" class="btn btn-default">Upload</button>
                                </div>
                            </div>
                        </form>
                        <table id="files" class="table table-striped table-bordered">
                            <thead>
                                <tr>
                                    <th>Filename</th>
                                    <th>Ext</th>
                                    <th>Size</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <div><h4>Project Milestones</h4></div>
                        @can('create',App\Models\Milestone::class)
                        <button class="btn btn-negative" type="button" data-toggle="modal" data-target="#popup-milestone">Create Milestone</button>
                        @endcan
                    </div>
                    <div class="card-body">
                        <table id="milestones" class="table table-striped table-bordered">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Status</th>
                                    <th>Description</th>
                                    <th>Cost</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @include('components.popup-milestone')
@endsection

@push('scripts')
    <script src="{{asset('lib/DataTables/datatables.min.js')}}"></script>
    <script src="{{asset('lib/chart.js-3.0.1/chart.min.js')}}"></script>
    <script>
        function downloadData(elem) {
            window.location.href = @json(route('project-files.download'))+"?id="+$(elem).data('id');
        }

        function deleteFile(elem) {
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
                        url: '{{route("project-files.delete")}}',
                        data: { id: $(elem).data('id') },
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        }
                    }).done((res) => {
                        if(res == true) {
                            Swal.fire(
                                'Success!',
                                @json(__('response.delete_succeed')),
                                'success'
                            )
                            document.querySelector('#files').dispatchEvent(new CustomEvent('mutated'));
                        } else {
                            Swal.fire(
                                'Failed!',
                                @json(__('response.delete_failed')),
                                'error'
                            )
                        }
                    }).fail((jqXHR) => {
                        var response = jqXHR.responseJSON;
                        var title = 'Failed!';
                        var message = response.message ?? @json(__('response.delete_failed'));
                        switch(jqXHR.status) {
                            case 403: title = 'Not Authorized'; break;
                            case 500: title = 'Server Error'; break;
                        }
                        Swal.fire(
                            title,
                            message,
                            'error'
                        )
                    });
                }
            });
        }

        function deleteMilestone(elem) {
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
                        url: '{{route("project-milestones.delete")}}',
                        data: { id: $(elem).data('id') },
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        }
                    }).done((res) => {
                        if(res.status) {
                            Swal.fire(
                                'Success!',
                                res.message,
                                'success'
                            )
                            document.querySelector('#milestones').dispatchEvent(new CustomEvent('mutated'));
                        } else {
                            Swal.fire(
                                'Failed!',
                                res.message,
                                'error'
                            )
                        }
                    }).fail((jqXHR) => {
                        var response = jqXHR.responseJSON;
                        var title = 'Failed!';
                        var message = response.message ?? @json(__('response.delete_failed'));
                        switch(jqXHR.status) {
                            case 403: title = 'Not Authorized'; break;
                            case 500: title = 'Server Error'; break;
                        }
                        Swal.fire(
                            title,
                            message,
                            'error'
                        )
                    });
                }
            });
        }
    </script>
    <script>
        if($('#chart-tasks-overview').length) {
            var ctx = $('#chart-tasks-overview');
            var data = {
                labels: [
                    'To Do',
                    'In Progress',
                    'Done'
                ],
                datasets: [{
                    label: 'Tasks Overview',
                    data: [@json($tasks_statistic['todo']),@json($tasks_statistic['inprogress']),@json($tasks_statistic['done'])],
                    backgroundColor: [
                        'rgb(120,109,247)',
                        'rgb(255, 230, 0)',
                        'rgb(0, 255, 0)'
                    ],
                    hoverOffset: 3
                }]
            };
            var chartTasksOverview = new Chart(ctx, {
                type: 'pie',
                data: data
            });
        }
    </script>
    <script>
        function goToKanbanBoard() {
            window.location.href = '/projects/board?id='+getQueryVariable('id');
        }

        function uploadFile(e) {
            var formData = new FormData(e.target);
            formData.append('project_id', getQueryVariable('id'));
            $.ajax({
                method: 'POST',
                url: @json(route('project-files.save')),
                data: formData,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                processData: false,
                contentType: false
            }).done((res) => {
                if(res) {
                    document.querySelector('#files').dispatchEvent(new CustomEvent('mutated'));
                    $('[name=file]').removeClass('is-invalid');
                }
            }).fail((jqXHR, textStatus, errorResponse) => {
                if(jqXHR.status == 422) {
                    var errors = jqXHR.responseJSON.errors;
                    for(error in errors) {
                        $('#validate-'+error).text(errors[error][0]);
                        $('[name='+error+']').addClass('is-invalid');
                    }
                } else {
                    var response = jqXHR.responseJSON;
                    var title = 'Failed!';
                    var message = response.message;

                    switch(jqXHR.status) {
                        case 403: title = 'Not Authorized!'; break;
                        case 500: title = 'Server Error'; break;
                        case 413:
                            title = 'File is too big';
                            message = 'File size is bigger than server allowance';
                        break;
                    }
                    Swal.fire(
                        title,
                        message,
                        'error'
                    )
                }
            });
            e.preventDefault();
        }
    </script>
@endpush

@push('ready-scripts')
    $('#file-upload-form').on('submit', uploadFile);
    var tableFiles = $('#files').DataTable({
        order: [],
        paging: false,
        searching: false,
        processing: true,
        ajax: {
            url: '{{ route('project-files.data') }}',
            dataSrc: '',
            data: (d) => {
                d.project_id = getQueryVariable('id');
            }
        },
        columns: [
            {data: 'filename'},
            {data: 'ext'},
            {
                data: 'size',
                render: (data, type, row, meta) => {
                    return new Intl.NumberFormat().format(new Number(data/1000).toFixed(1))+" KB";
                }
            },
            {
                data: null,
                width: "3.75rem",
                render: (data, type, row, meta) => {
                    var htmlDownload = '<button class="table-action-icon" type="button" data-id="'+row.id+'" onclick="downloadData(this)"><i class="fas fa-download"></i></button>';
                    var htmlDelete = '<button class="table-action-icon" data-id="'+row.id+'" type="button" onclick="deleteFile(this)"><i class="fas fa-trash"></i></button>';
                    if(!row.can_delete) htmlDelete = '';
                    return htmlDownload+htmlDelete;
                }
            }
        ]
    });

    $('#files').on('mutated', (e) => {
        tableFiles.ajax.reload();
    });

    var tableMilestones = $('#milestones').DataTable({
        order: [],
        paging: false,
        searching: false,
        processing: true,
        ajax: {
            url: '{{ route('project-milestones.data') }}',
            dataSrc: '',
            data: (d) => {
                d.project_id = getQueryVariable('id');
                d.name = $('#filter-name').val();
                d.status = $('#filter-status').val();
            }
        },
        columns: [
            {data: 'name'},
            {data: 'status'},
            {data: 'description'},
            {
                data: 'cost',
                render: (data,type,row,meta) => {
                    return "USD "+new Intl.NumberFormat().format(new Number(data));
                }
            },
            {
                visible: @json(request()->user()->role == "admin"),
                data: null,
                width: "3.75rem",
                render: (data, type, row, meta) => {
                    return '<button class="table-action-icon" type="button" data-toggle="modal" data-target="#popup-milestone" data-action="edit"'+
                        'data-id="'+row.id+'"><i class="fas fa-pen"></i></button>'+
                        '<button class="table-action-icon" data-id="'+row.id+'" type="button" onclick="deleteMilestone(this)"><i class="fas fa-trash"></i></button>';
                }
            }
        ]
    });

    $('#milestones').on('mutated', (e) => {
        tableMilestones.ajax.reload();
    });
@endpush
