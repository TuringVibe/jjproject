<div id="popup-wallet" class="modal fade" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="create-wallet-title" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="create-wallet-title">Create Wallet</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="popup-wallet-form">
                    <input type="hidden" id="id" name="id">
                    <div class="form-row">
                        <div class="form-group col-6">
                            <label for="name">Name <span class="text-danger">*</span></label>
                            <input type="text" id="name" name="name" class="form-control" aria-describedby="validate-name">
                            <div id="validate-name" class="invalid-feedback"></div>
                        </div>
                        <div class="form-group col-3">
                            <label for="default-currency">Default Currency <span class="text-danger">*</span></label>
                            <select id="default-currency" name="default_currency" class="form-control" aria-describedby="validate-default_currency">
                                <option value="usd">USD</option>
                                <option value="cny">CNY</option>
                                <option value="idr">IDR</option>
                            </select>
                            <div id="validate-default_currency" class="invalid-feedback"></div>
                        </div>
                        <div class="form-group col-3">
                            <label for="initial-balance">Initial Balance</label>
                            <input type="text" id="initial-balance" name="initial_balance" class="form-control" aria-describedby="validate-initial_balance" value="0">
                            <div id="validate-initial_balance" class="invalid-feedback"></div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button class="btn btn-default" type="button" onclick="$('#popup-wallet-form').submit()">Submit</button>
                <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
            </div>
        </div>
    </div>
</div>

@push('ready-scripts')
$('#popup-wallet').on('hide.bs.modal', popUpWalletHide);
$('#popup-wallet').on('show.bs.modal', popUpWalletShow);
$('#popup-wallet').on('shown.bs.modal', popUpWalletShown);
@endpush

@push('scripts')
<script src="{{ asset('lib/bootstrap-colorpicker-3.2.0/js/bootstrap-colorpicker.min.js') }}"></script>
<script>
    var colorpicker = null;
    function popUpWalletHide(e) {
        var $modal = $(this);
        $modal.find('#popup-wallet-form').off('submit');
        $modal.find('#id').val(null);
        $modal.find('#name').val(null);
        $modal.find('#default-currency option:first').prop("selected",true);
        $modal.find('#initial-balance').attr('readonly',false);
        $modal.find('#initial-balance').attr('disabled',false);
        $modal.find('#initial-balance').prev().text("Initial Balance");
        $modal.find('#initial-balance').val(0);
        $modal.find('.is-invalid').removeClass('is-invalid');
    }

    function popUpWalletShow(e) {
        var $origin = $(e.relatedTarget);
        var $modal = $(this);
        $modal.find('small.form-text.text-muted').hide();
        $modal.find('#create-wallet-title').text('Create Wallet');
        if($origin.data('action') == 'edit') {
            var id = $origin.data('id');
            $modal.find('small.form-text.text-muted').show();
            $modal.find('#create-wallet-title').text('Update Wallet');
            $modal.find('#initial-balance').attr('readonly',true);
            $modal.find('#initial-balance').attr('disabled',true);
            $modal.find('#initial-balance').prev().text("Total Balance");
            $.get('{{route("wallets.detail")}}',{id:id}).done((res) => {
                $modal.find('#id').val(res.id);
                $modal.find('#name').val(res.name);
                $modal.find('#initial-balance').val(Intl.NumberFormat("en-US",{maximumFractionDigits: 2}).format(res.total_balance));
                $modal.find('#default-currency option[value="'+res.default_currency+'"]').prop("selected",true);
            });
        }
    }

    function popUpWalletShown(e) {
        var $modal = $(this);
        $modal.find('#name').focus();
        $modal.find('#popup-wallet-form').on('submit', (e) => {
            $loading = $(loading());
            $modal.find('.modal-footer').prepend($loading);
            $modal.find('button').prop("disabled",true);
            var formData = new FormData(e.target);
            $.ajax({
                method: 'POST',
                url: '{{route("wallets.save")}}',
                data: formData,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                processData: false,
                contentType: false
            }).done((res) => {
                if(res) {
                    $modal.modal('hide');
                    document.querySelector('#list').dispatchEvent(new CustomEvent('mutated'));
                }
            }).fail((jqXHR, textStatus, errorResponse) => {
                if(jqXHR.status == 422) {
                    var errors = jqXHR.responseJSON.errors;
                    for(error in errors) {
                        $('#validate-'+error).text(errors[error]);
                        $('[name='+error+']').addClass('is-invalid');
                    }
                }
            }).always(() => {
                $loading.remove();
                $modal.find('button').prop("disabled",false);
            });
            e.preventDefault();
        });
    }

    function loading() {
        var html = '<div class="spinner-border mr-3" role="status">'+
            '<span class="sr-only">Loading...</span>'+
        '</div>';
        return html;
    }
</script>
@endpush
