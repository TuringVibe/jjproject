@push('head')
    <link rel="stylesheet" href="{{ asset('lib/daterangepicker-3.1/daterangepicker.css') }}">
@endpush
<div id="popup-finance-asset" class="modal fade" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="create-finance-asset-title" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="create-finance-asset-title">Create Finance Asset</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="popup-finance-asset-form">
                    <input type="hidden" id="id" name="id">
                    <div class="form-row">
                        <div class="col-12 col-sm-6 form-group">
                            <label for="name">Name <span class="text-danger">*</span></label>
                            <input type="text" id="name" name="name" class="form-control" aria-describedby="validate-name">
                            <div id="validate-name" class="invalid-feedback"></div>
                        </div>
                        <div class="col-12 col-sm-3 form-group">
                            <label for="qty">Quantity <span class="text-danger">*</span></label>
                            <input type="text" id="qty" name="qty" class="form-control" aria-describedby="validate-qty">
                            <div id="validate-qty" class="invalid-feedback"></div>
                        </div>
                        <div class="col-12 col-sm-3 form-group">
                            <label for="unit">Unit <span class="text-danger">*</span></label>
                            <input type="text" id="unit" name="unit" class="form-control" aria-describedby="validate-unit">
                            <div id="validate-unit" class="invalid-feedback"></div>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="col-5 form-group">
                            <label for="buy-datetime">Buy Datetime <span class="text-danger">*</span></label>
                            <input type="text" id="buy-datetime" name="buy_datetime" class="single-date-picker form-control" aria-describedby="validate-buy_datetime">
                            <div id="validate-buy_datetime" class="invalid-feedback"></div>
                        </div>
                        <div class="col-5 form-group">
                            <label for="buy-price-per-unit">Buy Price per Unit <span class="text-danger">*</span></label>
                            <input type="text" id="buy-price-per-unit" name="buy_price_per_unit" class="form-control" aria-describedby="validate-buy_price_per_unit">
                            <div id="validate-buy_price_per_unit" class="invalid-feedback"></div>
                        </div>
                        <div class="col-2 form-group">
                            <label for="currency">Currency <span class="text-danger">*</span></label>
                            <select class="form-control" id="currency" name="currency">
                                <option value="usd">USD</option>
                                <option value="cny">CNY</option>
                                <option value="idr">IDR</option>
                            </select>
                            <div id="validate-currency" class="invalid-feedback"></div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button class="btn btn-default" type="button" onclick="$('#popup-finance-asset-form').submit()">Submit</button>
                <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
            </div>
        </div>
    </div>
</div>

@push('ready-scripts')
$('#popup-finance-asset').on('hide.bs.modal', popUpFinanceLabelHide);
$('#popup-finance-asset').on('show.bs.modal', popUpFinanceLabelShow);
$('#popup-finance-asset').on('shown.bs.modal', popUpFinanceLabelShown);
@endpush

@push('scripts')
<script src="{{ asset('lib/daterangepicker-3.1/moment.min.js') }}"></script>
<script src="{{ asset('lib/daterangepicker-3.1/daterangepicker.js') }}"></script>
<script>
    var colorpicker = null;
    function popUpFinanceLabelHide(e) {
        var $modal = $(this);
        $modal.find('#popup-finance-asset-form').off('submit');
        $modal.find('#id').val(null);
        $modal.find('#name').val(null);
        $modal.find('#qty').val(null);
        $modal.find('#unit').val(null);
        $modal.find('#buy-datetime').val(null);
        $modal.find('#currency').val("usd");
        $modal.find('#buy-price-per-unit').val(null);
        $modal.find('.is-invalid').removeClass('is-invalid');
    }

    function popUpFinanceLabelShow(e) {
        var $origin = $(e.relatedTarget);
        var $modal = $(this);
        var now = moment().format('YYYY-MM-DD HH:mm:ss');
        $modal.find('#create-finance-asset-title').text('Create Finance Asset');
        $modal.find('#buy-datetime').val(now);
        $modal.find('#buy-datetime').data('daterangepicker').setStartDate(now);
        if($origin.data('action') == 'edit') {
            var id = $origin.data('id');
            $modal.find('#create-finance-asset-title').text('Update Finance Asset');
            $.get('{{route("finance-assets.edit")}}',{id:id}).done((res) => {
                $modal.find('#id').val(res.id);
                $modal.find('#name').val(res.name);
                $modal.find('#qty').val(res.qty);
                $modal.find('#unit').val(res.unit);
                $modal.find('#buy-datetime').val(res.buy_datetime);
                $modal.find('#currency').val(res.currency);
                $modal.find('#buy-price-per-unit').val(res.buy_price_per_unit);
            });
        }
    }

    function loading() {
        var html = '<div class="spinner-border mr-3" role="status">'+
            '<span class="sr-only">Loading...</span>'+
        '</div>';
        return html;
    }

    function popUpFinanceLabelShown(e) {
        var $modal = $(this);
        $modal.find('#popup-finance-asset-form').on('submit', (e) => {
            $loading = $(loading());
            $modal.find('.modal-footer').prepend($loading);
            $modal.find('button').prop("disabled",true);
            var formData = new FormData(e.target);
            $.ajax({
                method: 'POST',
                url: '{{route("finance-assets.save")}}',
                data: formData,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                processData: false,
                contentType: false
            }).done((res) => {
                if(res) {
                    $modal.modal('hide');
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
                    document.querySelector('#list').dispatchEvent(new CustomEvent('mutated'));
                }
            }).fail((jqXHR, textStatus, errorResponse) => {
                if(jqXHR.status == 422) {
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
