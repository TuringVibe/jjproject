@push('head')
    <link rel="stylesheet" href="{{ asset('lib/bootstrap-colorpicker-3.2.0/css/bootstrap-colorpicker.min.css') }}">
    <link rel="stylesheet" href="{{ asset('lib/select2-4.0.13/css/select2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('lib/select2-bootstrap4-theme-1.5.2/select2-bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('lib/daterangepicker-3.1/daterangepicker.css') }}">
@endpush
<div id="popup-project" class="modal fade" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="create-project-title" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="create-project-title">Create Project</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="popup-project-form">
                    <input type="hidden" id="id" name="id">
                    <div class="form-row">
                        <div class="col-sm-8 form-group">
                            <label for="name">Name</label>
                            <input type="text" id="name" name="name" class="form-control" aria-describedby="validate-name">
                            <div id="validate-name" class="invalid-feedback"></div>
                        </div>
                        <div class="col-sm-4 form-group">
                            <label for="status">Status</label>
                            <select id="status" class="form-control" name="status">
                                <option value="notstarted">Not Started</option>
                                <option value="ongoing">On Going</option>
                                <option value="complete">Complete</option>
                                <option value="onhold">On Hold</option>
                                <option value="canceled">Canceled</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="col-sm-4 form-group">
                            <label for="startdate">Start Date</label>
                            <input type="text" id="startdate" name="startdate" class="date-picker form-control" aria-describedby="validate-startdate">
                            <div id="validate-startdate" class="invalid-feedback"></div>
                        </div>
                        <div class="col-sm-4 form-group">
                            <label for="enddate">End Date</label>
                            <input type="text" id="enddate" name="enddate" class="date-picker form-control" aria-describedby="validate-enddate">
                            <div id="validate-enddate" class="invalid-feedback"></div>
                        </div>
                        <div class="col-sm-4 form-group">
                            <label for="budget">Budget</label>
                            <div class="input-group">
                                <span class="input-group-append">
                                    <span class="input-group-text">USD</span>
                                </span>
                                <input type="text" id="budget" name="budget" class="form-control" aria-describedby="validate-budget">
                                <div id="validate-budget" class="invalid-feedback"></div>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="description">Description</label>
                        <textarea id="description" name="description" class="form-control" aria-describedby="validate-description"></textarea>
                        <div id="validate-description" class="invalid-feedback"></div>
                    </div>
                    <div class="form-group">
                        <label for="user-ids">Users</label>
                        <select id="user-ids" name="user_ids[]" class="select2" multiple="multiple" aria-describedby="validate-user_ids">
                            <option value="">-- No User --</option>
                            @foreach ($users as $user)
                                <option value="{{$user['id']}}">{{$user['firstname'].' '.$user['lastname']}}</option>
                            @endforeach
                        </select>
                        <div id="validate-user_ids" class="invalid-feedback"></div>
                    </div>
                    <div class="form-group">
                        <label for="project-label-ids">Labels</label>
                        <select id="project-label-ids" name="project_label_ids[]" class="select2" multiple="multiple" aria-describedby="validate-project_label_ids">
                            <option value="">-- No Label --</option>
                            @foreach ($labels as $label)
                                <option value="{{$label['id']}}">{{$label['name']}}</option>
                            @endforeach
                        </select>
                        <div id="validate-project_label_ids" class="invalid-feedback"></div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button class="btn btn-default" type="button" onclick="$('#popup-project-form').submit()">Submit</button>
                <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
            </div>
        </div>
    </div>
</div>

@push('ready-scripts')
$('#popup-project').on('hide.bs.modal', popUpProjectHide);
$('#popup-project').on('show.bs.modal', popUpProjectShow);
$('#popup-project').on('shown.bs.modal', popUpProjectShown);
$('.select2').select2();
$('.date-picker').daterangepicker({
    singleDatePicker: true,
    showDropdowns: true,
    locale: {format: 'YYYY-MM-DD'}
});
@endpush

@push('scripts')
<script src="{{ asset('lib/bootstrap-colorpicker-3.2.0/js/bootstrap-colorpicker.min.js') }}"></script>
<script src="{{ asset('lib/select2-4.0.13/js/select2.min.js') }}"></script>
<script src="{{ asset('lib/daterangepicker-3.1/moment.min.js') }}"></script>
<script src="{{ asset('lib/daterangepicker-3.1/daterangepicker.js') }}"></script>
<script>
    function popUpProjectHide(e) {
        var $modal = $(this);
        $modal.find('#popup-project-form').off('submit');
        $modal.find('#id').val(null);
        $modal.find('#name').val(null);
        $modal.find('#status').val(null);
        $modal.find('#startdate').val(null);
        $modal.find('#enddate').val(null);
        $modal.find('#budget').val(null);
        $modal.find('#description').val(null);
        $modal.find('#user-ids').val(null).trigger('change');
        $modal.find('#project-label-ids').val(null).trigger('change');
        $modal.find('.is-invalid').removeClass('is-invalid');
    }

    function popUpProjectShow(e) {
        var $origin = $(e.relatedTarget);
        var $modal = $(this);
        $modal.find('#create-project-title').text('Create Project');
        if($origin.data('action') == 'edit') {
            var id = $origin.data('id');
            $modal.find('#create-project-title').text('Update Project');
            $.get('{{route("projects.edit")}}',{id: id}).done((res) => {
                $modal.find('#id').val(res.id);
                $modal.find('#name').val(res.name);
                $modal.find('#status').val(res.status);
                $modal.find('#startdate').val(res.startdate);
                $modal.find('#enddate').val(res.enddate);
                $modal.find('#budget').val(res.budget);
                $modal.find('#description').val(res.description);
                var user_ids = [];
                for(user of res.users) {
                    user_ids.push(user['id']);
                }
                $modal.find('#user-ids').val(user_ids).trigger('change');
                var label_ids = [];
                for(label of res.labels) {
                    label_ids.push(label['id']);
                }
                $modal.find('#project-label-ids').val(label_ids).trigger('change');
            });
        }
    }

    function popUpProjectShown(e) {
        var $modal = $(this);
        $modal.find('#popup-project-form').on('submit', (e) => {
            var formData = new FormData(e.target);
            $.ajax({
                method: 'POST',
                url: '{{route("projects.save")}}',
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
