<div id="popup-milestone" class="modal fade" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="create-project-label-title" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="create-milestone-title">Create Milestone</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="popup-milestone-form">
                    <input type="hidden" id="id" name="id">
                    <input type="hidden" id="project_id" name="project_id">
                    <div class="form-group">
                        <label for="name">Name <span class="text-danger">*</span></label>
                        <input type="text" id="name" name="name" class="form-control" aria-describedby="validate-name">
                        <div id="validate-name" class="invalid-feedback"></div>
                    </div>
                    <div class="form-group">
                        <label for="status">Status <span class="text-danger">*</span></label>
                        <select id="status" class="form-control" name="status">
                            <option value="incomplete">Incomplete</option>
                            <option value="complete">Complete</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="description">Description</label>
                        <textarea id="description" name="description" class="form-control" aria-describedby="validate-description"></textarea>
                        <div id="validate-description" class="invalid-feedback"></div>
                    </div>
                    <div class="form-group">
                        <label for="cost">Cost</label>
                        <div class="input-group">
                            <span class="input-group-append">
                                <span class="input-group-text">USD</span>
                            </span>
                            <input type="text" id="cost" name="cost" class="form-control" aria-describedby="validate-cost">
                            <div id="validate-cost" class="invalid-feedback"></div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button class="btn btn-default" type="button" onclick="$('#popup-milestone-form').submit()">Submit</button>
                <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
            </div>
        </div>
    </div>
</div>

@push('ready-scripts')
$('#popup-milestone').on('hide.bs.modal', popUpMilestoneHide);
$('#popup-milestone').on('show.bs.modal', popUpMilestoneShow);
$('#popup-milestone').on('shown.bs.modal', popUpMilestoneShown);
@endpush

@push('scripts')
<script>
    function popUpMilestoneHide(e) {
        var $modal = $(this);
        $modal.find('#popup-milestone-form').off('submit');
        $modal.find('#id').val(null);
        $modal.find('#name').val(null);
        $modal.find('#status').val(null);
        $modal.find('#description').val(null);
        $modal.find('#cost').val(null);
        $modal.find('.is-invalid').removeClass('is-invalid');
    }

    function popUpMilestoneShow(e) {
        var $origin = $(e.relatedTarget);
        var $modal = $(this);
        $modal.find('#create-milestone-title').text('Create Milestone');
        $modal.find('#project_id').val(getQueryVariable('id'));
        if($origin.data('action') == 'edit') {
            var id = $origin.data('id');
            $modal.find('#create-milestone-title').text('Update Milestone');
            $.get('{{route("project-milestones.edit")}}',{id:id}).done((res) => {
                $modal.find('#id').val(res.id);
                $modal.find('#name').val(res.name);
                $modal.find('#status').val(res.status);
                $modal.find('#description').val(res.description);
                $modal.find('#cost').val(res.cost);
            });
        }
    }

    function popUpMilestoneShown(e) {
        var $modal = $(this);
        $modal.find('#popup-milestone-form').on('submit', (e) => {
            var formData = new FormData(e.target);
            $.ajax({
                method: 'POST',
                url: '{{route("project-milestones.save")}}',
                data: formData,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                processData: false,
                contentType: false
            }).done((res) => {
                if(res.status) {
                    Swal.fire(
                        'Success!',
                        res.message,
                        'success'
                    )
                    $modal.modal('hide');
                    document.querySelector('#milestones').dispatchEvent(new CustomEvent('mutated'));
                }
            }).fail((jqXHR, textStatus, errorResponse) => {
                var response = jqXHR.responseJSON;
                switch(jqXHR.status) {
                    case 422:
                        var errors = response.errors;
                        for(error in errors) {
                            $('#validate-'+error).text(errors[error]);
                            $('[name='+error+']').addClass('is-invalid');
                        }
                    break;
                    case 403:
                        var title = 'Not Authorized';
                        var message = response.message;
                        Swal.fire(
                            title,
                            message,
                            'error'
                        )
                        $modal.modal('hide');
                    break;
                    case 500:
                        var title = 'Server Error';
                        var message = response.message ?? @json(__('response.server_error'));
                        Swal.fire(
                            title,
                            message,
                            'error'
                        );
                        $modal.modal('hide');
                    break;
                }
            });
            e.preventDefault();
        });
    }
</script>
@endpush
