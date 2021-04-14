@push('head')
    <link rel="stylesheet" href="{{ asset('lib/bootstrap-colorpicker-3.2.0/css/bootstrap-colorpicker.min.css') }}">
@endpush
<div id="popup-finance-label" class="modal fade" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="create-finance-label-title" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="create-finance-label-title">Create Finance Label</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="popup-finance-label-form">
                    <input type="hidden" id="id" name="id">
                    <div class="form-group">
                        <label for="name">Name</label>
                        <input type="text" id="name" name="name" class="form-control" aria-describedby="validate-name">
                        <div id="validate-name" class="invalid-feedback"></div>
                    </div>
                    <div class="form-group">
                        <label for="color-picker">Color</label>
                        <div class="input-group" id="color-picker" title="Choose Color">
                            <input type="text" data-color="#000000" class="form-control" id="color" name="color" placeholder="Click button to choose color.." aria-describedby="validate-color">
                            <span class="input-group-append">
                                <span class="input-group-text colorpicker-input-addon"><i></i></span>
                            </span>
                            <div id="validate-color" class="invalid-feedback"></div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button class="btn btn-default" type="button" onclick="$('#popup-finance-label-form').submit()">Submit</button>
                <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
            </div>
        </div>
    </div>
</div>

@push('ready-scripts')
$('#popup-finance-label').on('hide.bs.modal', popUpFinanceLabelHide);
$('#popup-finance-label').on('show.bs.modal', popUpFinanceLabelShow);
$('#popup-finance-label').on('shown.bs.modal', popUpFinanceLabelShown);
@endpush

@push('scripts')
<script src="{{ asset('lib/bootstrap-colorpicker-3.2.0/js/bootstrap-colorpicker.min.js') }}"></script>
<script>
    var colorpicker = null;
    function popUpFinanceLabelHide(e) {
        var $modal = $(this);
        $modal.find('#popup-finance-label-form').off('submit');
        $modal.find('#id').val(null);
        $modal.find('#name').val(null);
        colorpicker.setValue('#000000');
        $modal.find('.is-invalid').removeClass('is-invalid');
    }

    function popUpFinanceLabelShow(e) {
        var $origin = $(e.relatedTarget);
        var $modal = $(this);
        if(colorpicker == null) {
            $modal.find('#color-picker').colorpicker({useAlpha: false});
            colorpicker = $modal.find('#color-picker').colorpicker('colorpicker')
        }
        $modal.find('small.form-text.text-muted').hide();
        $modal.find('#create-finance-label-title').text('Create Finance Label');
        if($origin.data('action') == 'edit') {
            var id = $origin.data('id');
            $modal.find('small.form-text.text-muted').show();
            $modal.find('#create-finance-label-title').text('Update Finance Label');
            $.get('{{route("finance-labels.detail")}}',{id:id}).done((res) => {
                $modal.find('#id').val(res.id);
                $modal.find('#name').val(res.name);
                colorpicker.setValue(res.color);
            });
        }
    }

    function popUpFinanceLabelShown(e) {
        var $modal = $(this);
        $modal.find('#popup-finance-label-form').on('submit', (e) => {
            var formData = new FormData(e.target);
            $.ajax({
                method: 'POST',
                url: '{{route("finance-labels.save")}}',
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
            });
            e.preventDefault();
        });
    }
</script>
@endpush
