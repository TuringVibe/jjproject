@extends('layouts.master')

@push('head')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="{{ asset('lib/DataTables/datatables.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/content.css') }}">
    <style>
        .label-list {
            display: inline-block;
            padding: .3rem;
            border-radius: 5px;
            color: white;
        }
    </style>
@endpush

@section('content')
    <div class="content container-fluid">
        @include('components.content-header',[
                'with_btn' => true,
                'btn_label' => 'Create Mutation',
                'action' => 'openModal()'
            ]
        )
        <div id="scheduled-mutations" class="row mb-3">
            <div class="col">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h4>Scheduled Mutations</h4>
                        <button type="button" class="btn btn-negative" data-toggle="modal" data-target="#popup-finance-mutation-schedule">Add Schedule</button>
                    </div>
                    <div class="card-body">
                        <div class="form-row">
                            <div class="col-auto form-group">
                                <label for="filter-name">Name</label>
                                <input type="text" id="filter-next-name" class="form-control">
                            </div>
                            <div class="col-auto form-group align-self-end">
                                <button id="filter-next-button" class="btn btn-default" type="button">Filter</button>
                            </div>
                        </div>
                        <table id="list-next" class="table table-striped table-bordered">
                            <thead>
                                <tr>
                                    <th>Next Date</th>
                                    <th>Name</th>
                                    <th>Mode</th>
                                    <th>Currency</th>
                                    <th>Nominal</th>
                                    <th>Repeat</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div id="finance-mutations" class="row">
            <div class="col">
                <div class="card">
                    <h4 class="card-header">Finance Mutations</h4>
                    <div class="card-body">
                        <div class="form-row">
                            <div class="col-auto form-group">
                                <label for="filter-from-date">From</label>
                                <input type="date" id="filter-from-date" class="form-control">
                            </div>
                            <div class="col-auto form-group">
                                <label for="filter-to-date">To</label>
                                <input type="date" id="filter-to-date" class="form-control">
                            </div>
                            <div class="col-auto form-group">
                                <label for="filter-wallet">Wallet</label>
                                <select id="filter-wallet" class="form-control">
                                    <option value="">--All wallets --</option>
                                    <option value="0">No wallet</option>
                                    @foreach ($wallets as $wallet)
                                        <option value="{{$wallet['id']}}">{{$wallet['name']}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-auto form-group">
                                <label for="filter-mode">Mode</label>
                                <select id="filter-mode" class="form-control">
                                    <option value="">--All modes --</option>
                                    <option value="debit">Debit</option>
                                    <option value="credit">Credit</option>
                                </select>
                            </div>
                            <div class="col-auto form-group">
                                <label for="filter-label">Label</label>
                                <select id="filter-label" class="form-control">
                                    <option value="">-- All labels --</option>
                                    <option value="0">No label</option>
                                    @foreach ($labels as $label)
                                        <option value="{{$label['id']}}">{{$label['name']}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-auto form-group">
                                <label for="filter-project">Project</label>
                                <select id="filter-project" class="form-control">
                                    <option value="">-- All projects --</option>
                                    <option value="0">No project</option>
                                    @foreach ($projects as $project)
                                        <option value="{{$project['id']}}">{{$project['name']}}</option>
                                    @endforeach
                                </select>
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
                                    <th rowspan="2">Date</th>
                                    <th rowspan="2">Name</th>
                                    <th rowspan="2">Wallet</th>
                                    <th colspan="3">Debit</th>
                                    <th colspan="3">Kredit</th>
                                    <th rowspan="2">Label</th>
                                    <th rowspan="2">Project</th>
                                    <th rowspan="2">Action</th>
                                </tr>
                                <tr>
                                    <th>USD</th>
                                    <th>CNY</th>
                                    <th>IDR</th>
                                    <th>USD</th>
                                    <th>CNY</th>
                                    <th>IDR</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @include('components.popup-finance-mutation-schedule')
    @include('components.popup-finance-mutation')
@endsection

@push('scripts')
    <script src="{{ asset('lib/moment-with-locales.min.js') }}"></script>
    <script src="{{asset('lib/DataTables/datatables.min.js')}}"></script>
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
                        url: '{{route("finance-mutations.delete")}}',
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

        function deleteSchedule(elem) {
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
                        url: '{{route("finance-mutations.scheduled.delete")}}',
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
                            document.querySelector('#list-next').dispatchEvent(new CustomEvent('mutated'));
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
            $('#popup-finance-mutation').modal('show');
        }

    </script>
@endpush

@push('ready-scripts')
    var walletId = getQueryVariable('wallet_id');
    var labelId = getQueryVariable('label_id');

    $('#filter-next-button').on('click',(e) => {
        document.querySelector('#list-next').dispatchEvent(new CustomEvent('mutated'));
    });
    $('#filter-next-name').on('keyup',(e) => {
        if(e.key === "Enter") $('#filter-next-button').click();
    });

    var table_next = $('#list-next').DataTable({
        order: [],
        paging: false,
        searching: false,
        processing: true,
        ajax: {
            url: '{{ route('finance-mutations.scheduled.data') }}',
            dataSrc: '',
            data: function(d) {
                d.name = $('#filter-next-name').val();
            }
        },
        columns: [
            {
                data: 'next_mutation_date',
                render: (data) => {
                    return moment(data).format('LL');
                }
            },
            {data: 'name'},
            {
                data: 'mode',
                render: (data,type,row,meta) => {
                    if(data != 'transfer') return data;
                    return data + ', from ' + (row.from_wallet?.name ?? '-') + ' to ' + (row.to_wallet?.name ?? '-');
                }
            },
            {data: 'currency'},
            {
                data: 'nominal',
                render: (data, type, row, meta) => {
                    var currencies = {
                        'usd': '&#36;',
                        'cny': '&yen;',
                        'idr': 'Rp'
                    };
                    return currencies[row.currency]+" "+new Intl.NumberFormat().format(new Number(data).toFixed(1));
                }
            },
            {data: 'repeat'},
            {
                data: null,
                width: "3.75rem",
                render: (data, type, row, meta) => {
                    return '<button class="table-action-icon" type="button" data-toggle="modal" data-target="#popup-finance-mutation-schedule" data-action="edit"'+
                        'data-id="'+row.id+'"><i class="fas fa-pen"></i></button>'+
                        '<button class="table-action-icon" data-id="'+row.id+'" type="button" onclick="deleteSchedule(this)"><i class="fas fa-trash"></i></button>';
                }
            }
        ]
    });

    $('#list-next').on('mutated', (e) => {
        table_next.ajax.reload();
    });

    $('#filter-button').on('click',(e) => {
        document.querySelector('#list').dispatchEvent(new CustomEvent('mutated'));
    });
    $('#filter-name').on('keyup',(e) => {
        if(e.key === "Enter") $('#filter-button').click();
    });

    $('#list').on('preXhr.dt', function (e, settings, data) {
        if(walletId !== false) {
            document.querySelector('#filter-wallet').value = walletId;
            data.wallet_id = walletId;
        }
        if(labelId !== false) {
            document.querySelector('#filter-label').value = labelId;
            data.label_id = labelId;
        }
    });

    var table = $('#list').DataTable({
        order: [],
        paging: false,
        searching: false,
        processing: true,
        ajax: {
            url: '{{ route('finance-mutations.data') }}',
            dataSrc: '',
            data: function(d) {
                d.from_date = $('#filter-from-date').val();
                d.to_date = $('#filter-to-date').val();
                d.wallet_id = $('#filter-wallet').val();
                d.mode = $('#filter-mode').val();
                d.label_id = $('#filter-label').val();
                d.project_id = $('#filter-project').val();
                d.name = $('#filter-name').val();
            }
        },
        columns: [
            {
                data: 'mutation_date',
                render: (data) => {
                    return moment(data).format('LL');
                }
            },
            {data: 'name'},
            {
                data: null,
                render: (data,type,row,meta) => {
                    return row.wallet?.name ?? '-';
                }
            },
            {
                data: 'usd',
                render: (data,type,row,meta) => {
                    if(row.mode == 'debit') return "&#36; "+Intl.NumberFormat("en-US",{maximumFractionDigits: 2}).format(data);
                    else return "";
                }
            },
            {
                data: 'cny',
                render: (data,type,row,meta) => {
                    if(row.mode == 'debit') return "&yen; "+Intl.NumberFormat("en-US",{maximumFractionDigits: 2}).format(data);
                    else return "";
                }
            },
            {
                data: 'idr',
                render: (data,type,row,meta) => {
                    if(row.mode == 'debit') return "Rp "+Intl.NumberFormat("en-US",{maximumFractionDigits: 2}).format(data);
                    else return "";
                }
            },
            {
                data: 'usd',
                render: (data,type,row,meta) => {
                    if(row.mode == 'credit') return "&#36; "+Intl.NumberFormat("en-US",{maximumFractionDigits: 2}).format((data*-1));
                    else return "";
                }
            },
            {
                data: 'cny',
                render: (data,type,row,meta) => {
                    if(row.mode == 'credit') return "&yen; "+Intl.NumberFormat("en-US",{maximumFractionDigits: 2}).format((data*-1));
                    else return "";
                }
            },
            {
                data: 'idr',
                render: (data,type,row,meta) => {
                    if(row.mode == 'credit') return "Rp "+Intl.NumberFormat("en-US",{maximumFractionDigits: 2}).format((data*-1));
                    else return "";
                }
            },
            {
                data: 'labels',
                render: (data, type, row, meta) => {
                    var html = '';
                    for(label of data) {
                        html += '<span class="label-list" style="background-color:'+label.color+'">'+label.name+'</span>';
                    }
                    if(html == '') html = '-';
                    return html;
                }
            },
            {
                data: 'project',
                render: (data) => {
                    if(data != null) return data.name;
                    return "-";
                }
            },
            {
                data: null,
                width: "3.75rem",
                render: (data, type, row, meta) => {
                    return '<button class="table-action-icon" type="button" data-toggle="modal" data-target="#popup-finance-mutation" data-action="edit"'+
                        'data-id="'+row.id+'"><i class="fas fa-pen"></i></button>'+
                        '<button class="table-action-icon" data-id="'+row.id+'" type="button" onclick="deleteData(this)"><i class="fas fa-trash"></i></button>';
                }
            }
        ]
    });

    $('#list').on('mutated', (e) => {
        table.ajax.reload();
    });

    $('#list').on('init.dt', (e) => {
        if(walletId !== false || labelId !== false) {
            const financeMutationContainerElem = document.getElementById('finance-mutations');
            financeMutationContainerElem.scrollIntoView(true);
        }
    });
@endpush
