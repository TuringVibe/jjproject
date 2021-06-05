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
        table thead th {
            text-align: center;
        }
        table#list tbody tr {
            cursor: pointer;
        }
    </style>
@endpush

@section('content')
    <div class="content container-fluid">
        @include('components.content-header',[
                'with_btn' => true,
                'btn_label' => 'Create Finance Asset',
                'action' => 'openModal()'
            ]
        )
        <div class="card">
            <h4 class="card-header">Finance Asset</h4>
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
                            <th rowspan="3">Name</th>
                            <th rowspan="3">Qty</th>
                            <th rowspan="3">Unit</th>
                            <th rowspan="3">Buy Date</th>
                            <th colspan="6">Buy Price</th>
                            <th rowspan="3">Action</th>
                        </tr>
                        <tr>
                            <th colspan="2">USD</th>
                            <th colspan="2">CNY</th>
                            <th colspan="2">IDR</th>
                        </tr>
                        <tr>
                            <th>1 unit</th>
                            <th>total</th>
                            <th>1 unit</th>
                            <th>total</th>
                            <th>1 unit</th>
                            <th>total</th>
                        </tr>
                    </thead>
                    <tfoot>
                        <tr>
                            <th colspan="4" class="text-center align-middle">Grand total</th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
    @include('components.popup-finance-asset')
    @include('components.popup-asset-price-change')
@endsection

@push('scripts')
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
                        url: '{{route("finance-assets.delete")}}',
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
            $('#popup-finance-asset').modal('show');
        }

        function openAssetPriceChangeModal(id) {
            $('#popup-asset-price-change').data('financeAssetId',id);
            $('#popup-asset-price-change').modal('show');
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
    $('.single-date-picker').daterangepicker({
        autoUpdateInput: false,
        drops: 'auto',
        singleDatePicker: true,
        showDropdowns: true,
        applyClass: "btn-default",
        cancelClass: "btn-secondary",
        timePicker: true,
        timePicker24Hour: true,
        timePickerSeconds: true,
        locale: {
            format: 'YYYY-MM-DD HH:mm:ss',
            cancelLabel: 'Clear'
        }
    });
    $('.single-date-picker').on('apply.daterangepicker hide.daterangepicker', function(ev, picker) {
        $(this).val(picker.startDate.format('YYYY-MM-DD HH:mm:ss'));
    });

    $('.single-date-picker').on('cancel.daterangepicker', function(ev, picker) {
        $(this).val('');
    });
    var table = $('#list').DataTable({
        order: [],
        paging: false,
        searching: false,
        processing: true,
        ajax: {
            url: '{{ route('finance-assets.data') }}',
            dataSrc: '',
            data: function(d) {
                d.name = $('#filter-name').val();
            }
        },
        columns: [
            {data: 'name'},
            {
                data: 'qty',
                render: (data) => {
                    return new Intl.NumberFormat().format(data);
                }
            },
            {data: 'unit'},
            {data: 'buy_datetime'},
            {
                data: 'usd_unit',
                render: (data,type,row,meta) => {
                    return "&#36; "+new Intl.NumberFormat().format(new Number(data).toFixed(2));
                }
            },
            {
                data: 'usd_total',
                render: (data,type,row,meta) => {
                    return "&#36; "+new Intl.NumberFormat().format(new Number(data).toFixed(2));
                }
            },
            {
                data: 'cny_unit',
                render: (data,type,row,meta) => {
                    return "&yen; "+new Intl.NumberFormat().format(new Number(data).toFixed(2));
                }
            },
            {
                data: 'cny_total',
                render: (data,type,row,meta) => {
                    return "&yen; "+new Intl.NumberFormat().format(new Number(data).toFixed(2));
                }
            },
            {
                data: 'idr_unit',
                render: (data,type,row,meta) => {
                    return "Rp "+new Intl.NumberFormat().format(new Number(data).toFixed(2));
                }
            },
            {
                data: 'idr_total',
                render: (data,type,row,meta) => {
                    return "Rp "+new Intl.NumberFormat().format(new Number(data).toFixed(2));
                }
            },
            {
                data: null,
                width: "90px",
                render: (data, type, row, meta) => {
                    return '<button class="table-action-icon" type="button" data-toggle="modal" data-target="#popup-finance-asset" data-action="edit"'+
                        'data-id="'+row.id+'"><i class="fas fa-pen"></i></button>'+
                        '<button class="table-action-icon" data-id="'+row.id+'" type="button" onclick="deleteData(this)"><i class="fas fa-trash"></i></button>';
                }
            }
        ],
        footerCallback: function(row, data, start, end, display) {
            if(data.length > 0) {
                var total_usd_unit = 0, total_usd_total = 0, total_cny_unit = 0,
                    total_cny_total = 0, total_idr_unit = 0, total_idr_total = 0;
                for(item of data) {
                    total_usd_unit += item.usd_unit;
                    total_usd_total += item.usd_total;
                    total_cny_unit += item.cny_unit;
                    total_cny_total += item.cny_total;
                    total_idr_unit += item.idr_unit;
                    total_idr_total += item.idr_total;
                }

                $(row).find('th:eq(1)').html("&#36; "+new Intl.NumberFormat().format(new Number(total_usd_unit).toFixed(2)));
                $(row).find('th:eq(2)').html("&#36; "+new Intl.NumberFormat().format(new Number(total_usd_total).toFixed(2)));
                $(row).find('th:eq(3)').html("&yen; "+new Intl.NumberFormat().format(new Number(total_cny_unit).toFixed(2)));
                $(row).find('th:eq(4)').html("&yen; "+new Intl.NumberFormat().format(new Number(total_cny_total).toFixed(2)));
                $(row).find('th:eq(5)').html("Rp "+new Intl.NumberFormat().format(new Number(total_idr_unit).toFixed(2)));
                $(row).find('th:eq(6)').html("Rp "+new Intl.NumberFormat().format(new Number(total_idr_total).toFixed(2)));
            }
        }
    });

    $('#list').on('mutated', (e) => {
        table.ajax.reload();
    });

    $("#list tbody").on('click','tr',function(e) {
        if($(e.target).is("tr,td")) {
            var data = table.row(this).data();
            openAssetPriceChangeModal(data.id);
        }
    });
@endpush
