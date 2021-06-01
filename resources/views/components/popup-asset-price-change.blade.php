@push('head')
    <link rel="stylesheet" href="{{ asset('lib/daterangepicker-3.1/daterangepicker.css') }}">
@endpush
<div id="popup-asset-price-change" class="modal fade" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="asset-price-change-title" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="asset-price-change-title">Asset Price Changes</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="popup-asset-price-change-form">
                    <input type="hidden" id="finance-asset-id" name="finance_asset_id">
                    <div class="form-row">
                        <div class="col-12 col-sm-4 form-group">
                            <label for="change-datetime">Change Datetime <span class="text-danger">*</span></label>
                            <input type="text" id="change-datetime" name="change_datetime" class="single-date-picker form-control" aria-describedby="validate-change_datetime">
                            <div id="validate-change_datetime" class="invalid-feedback"></div>
                        </div>
                        <div class="col-9 col-sm-4 form-group">
                            <label for="price-per-unit">Price per Unit <span class="text-danger">*</span></label>
                            <input type="text" id="price-per-unit" name="price_per_unit" class="form-control" aria-describedby="validate-price_per_unit">
                            <div id="validate-price_per_unit" class="invalid-feedback"></div>
                        </div>
                        <div class="col-3 col-sm-2 form-group">
                            <label for="currency">Currency <span class="text-danger">*</span></label>
                            <select class="form-control" id="currency" name="currency">
                                <option value="usd">USD</option>
                                <option value="cny">CNY</option>
                                <option value="idr">IDR</option>
                            </select>
                            <div id="validate-currency" class="invalid-feedback"></div>
                        </div>
                        <div class="col-12 col-sm-2 form-group">
                            <label>&nbsp;</label>
                            <button class="btn btn-default btn-block" type="submit" id="submit">Submit</button>
                        </div>
                    </div>
                </form>
                <table id="price-change-list" class="table table-striped table-bordered">
                    <thead>
                        <tr>
                            <th rowspan="3">Datetime</th>
                            <th colspan="6">Price</th>
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
                </table>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" type="button" id="close" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

@push('ready-scripts')
    $('#popup-asset-price-change').on('hide.bs.modal', popUpAssetPriceChangeHide);
    $('#popup-asset-price-change').on('show.bs.modal', popUpAssetPriceChangeShow);
    $('#popup-asset-price-change').on('shown.bs.modal', popUpAssetPriceChangeShown);
@endpush

@push('scripts')
<script src="{{ asset('lib/moment-with-locales.min.js') }}"></script>
<script src="{{ asset('lib/daterangepicker-3.1/daterangepicker.js') }}"></script>
<script>
    var tableAssetPriceChange = null;
    function deletePriceChange(elem) {
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
                var $modal = $('#popup-asset-price-change');
                var id = $(elem).data('id');
                $.ajax({
                    url: @json(route('finance-assets.price-changes.delete')),
                    method: 'POST',
                    data: {id: id},
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                }).done((res) => {
                    if(res.status == true) {
                        Swal.fire({
                            toast: true,
                            position: 'top',
                            showConfirmButton: false,
                            timer: 3000,
                            timerProgressBar: true,
                            title: 'Success',
                            text: res.message,
                            icon: 'success'
                        });
                        document.querySelector('#price-change-list').dispatchEvent(new CustomEvent('mutated'));
                    } else {
                        Swal.fire({
                            toast: true,
                            position: 'top',
                            showConfirmButton: false,
                            timer: 3000,
                            timerProgressBar: true,
                            title: 'Error',
                            text: res.message,
                            icon: 'error'
                        });
                    }
                }).fail((jqXHR, textStatus, errorResponse) => {
                    Swal.fire({
                        toast: true,
                        position: 'top',
                        showConfirmButton: false,
                        timer: 3000,
                        timerProgressBar: true,
                        title: 'Error',
                        text: 'There is something wrong happened in your server',
                        icon: 'error'
                    });
                });
            }
        });
    }

    function popUpAssetPriceChangeHide(e) {
        var $modal = $(this);
        $modal.find('#popup-asset-price-change-form').off('submit');
        $modal.find('#finance-asset-id').val(null);
        $modal.find('#change-datetime').val(null);
        $modal.find('#price-per-unit').val(null);
        $modal.find('#currency').val(null);
        tableAssetPriceChange.destroy();
        $modal.find('.is-invalid').removeClass('is-invalid');
    }

    function popUpAssetPriceChangeShow(e) {
        var $modal = $(this);
        var financeAssetId = $modal.data('financeAssetId');
        $modal.find('#finance-asset-id').val(financeAssetId);
    }

    function loading() {
        var html = '<div class="spinner-border ml-3" role="status">'+
            '<span class="sr-only">Loading...</span>'+
        '</div>';
        return html;
    }

    function popUpAssetPriceChangeShown(e) {
        var $modal = $(this);
        var financeAssetId = $modal.data('financeAssetId');
        tableAssetPriceChange = $modal.find('#price-change-list').DataTable({
            order: [],
            paging: false,
            searching: false,
            processing: true,
            ajax: {
                url: @json(route('finance-assets.price-changes.data')),
                dataSrc: '',
                data: (d) => {
                    d.finance_asset_id = financeAssetId;
                }
            },
            columns: [
                {data: 'change_datetime'},
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
                    width: "30px",
                    render: (data, type, row, meta) => {
                        return '<button class="table-action-icon" data-id="'+row.id+'" type="button" onclick="deletePriceChange(this)"><i class="fas fa-trash"></i></button>';
                    }
                }
            ]
        });

        $('#price-change-list').on('mutated', (e) => {
            tableAssetPriceChange.ajax.reload();
        });

        $modal.find('#popup-asset-price-change-form').on('submit',function(e){
            $loading = $(loading());
            $modal.find('form').after($loading);
            $modal.find('button').prop("disabled",true);
            var formData = new FormData(e.target);
            $.ajax({
                method: 'POST',
                url: @json(route("finance-assets.price-changes.save")),
                data: formData,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                processData: false,
                contentType: false
            }).done((res) => {
                if(res.status == true) {
                    Swal.fire({
                        toast: true,
                        position: 'top',
                        showConfirmButton: false,
                        timer: 3000,
                        timerProgressBar: true,
                        title: 'Success',
                        text: res.message,
                        icon: 'success'
                    });
                    $('input').removeClass('is-invalid');
                    $('select').removeClass('is-invalid');
                    document.querySelector('#price-change-list').dispatchEvent(new CustomEvent('mutated'));
                }
            }).fail((jqXHR, textStatus, errorResponse) => {
                if(jqXHR.status == 422) {
                    $('input').removeClass('is-invalid');
                    $('select').removeClass('is-invalid');
                    var errors = jqXHR.responseJSON.errors;
                    for(error in errors) {
                        $('#validate-'+error).text(errors[error]);
                        $('[name='+error+']').addClass('is-invalid');
                    }
                } else {
                    Swal.fire({
                        toast: true,
                        position: 'top',
                        showConfirmButton: false,
                        timer: 3000,
                        timerProgressBar: true,
                        title: 'Error',
                        text: 'There is something wrong happened in your server',
                        icon: 'error'
                    });
                }
            }).always(() => {
                $loading.remove();
                $modal.find('button').prop("disabled",false);
            });
            e.preventDefault();
        });
    }
</script>
@endpush
