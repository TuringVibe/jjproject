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
                'btn_label' => 'Create Wallet',
                'action' => 'openModal()'
            ]
        )
        <div class="card">
            <h4 class="card-header">Wallet</h4>
            <div class="card-body">
                <div class="form-row">
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
                            <th>Default Currency</th>
                            <th>Total USD</th>
                            <th>Total CNY</th>
                            <th>Total IDR</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
    @include('components.popup-wallet')
@endsection

@push('scripts')
    <script src="{{asset('lib/DataTables/datatables.min.js')}}"></script>
    <script>
        function deleteData(elem) {
            Swal.fire({
                title: 'Are you sure ?',
                text: "You won't be able to revert this! Its mutations will still be there",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        method: 'POST',
                        url: '{{route("wallets.delete")}}',
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
            $('#popup-wallet').modal('show');
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
            url: '{{ route('wallets.data') }}',
            dataSrc: '',
            data: function(d) {
                d.name = $('#filter-name').val();
            }
        },
        columns: [
            {data: 'name'},
            {
                data: 'default_currency',
                render: (data) => {
                    return '<span class="text-uppercase">'+data+'</span>';
                }
            },
            {
                data: 'total_usd',
                render: (data, type, row, meta) => {
                    return '&#36; ' + new Intl.NumberFormat().format(new Number(data).toFixed(1));
                }
            },
            {
                data: 'total_cny',
                render: (data, type, row, meta) => {
                    return '&yen; ' + new Intl.NumberFormat().format(new Number(data).toFixed(1));
                }
            },
            {
                data: 'total_idr',
                render: (data, type, row, meta) => {
                    return 'Rp ' + new Intl.NumberFormat().format(new Number(data).toFixed(1));
                }
            },
            {
                data: null,
                width: "3.75rem",
                render: (data, type, row, meta) => {
                    return '<button class="table-action-icon" type="button" data-toggle="modal" data-target="#popup-wallet" data-action="edit" '+
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
