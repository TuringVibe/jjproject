@push('head')
<script src="https://cdn.ckeditor.com/ckeditor5/35.3.0/classic/ckeditor.js"></script>
<link rel="stylesheet" href="{{asset('css/extend-ckeditor5.css')}}" />
@endpush
<div id="popup-task" class="modal fade" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="create-task-title" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="create-task-title">Create Task</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="popup-task-form">
                    <input type="hidden" id="id" name="id">
                    <input type="hidden" id="project_id" name="project_id">
                    <div class="form-row">
                        <div class="col-sm-6 form-group">
                            <label for="name">Name <span class="text-danger">*</span></label>
                            <input type="text" id="name" name="name" class="form-control" aria-describedby="validate-name">
                            <div id="validate-name" class="invalid-feedback"></div>
                        </div>
                        <div class="col-sm-3 form-group">
                            <label for="status">Status <span class="text-danger">*</span></label>
                            <select id="status" class="form-control" name="status" aria-describedby="validate-status">
                                <option value="todo">To Do</option>
                                <option value="inprogress">In Progress</option>
                                <option value="done">Done</option>
                            </select>
                            <div id="validate-status" class="invalid-feedback"></div>
                        </div>
                        <div class="col-sm-3 form-group">
                            <label for="priority">Priority <span class="text-danger">*</span></label>
                            <select id="priority" class="form-control" name="priority" aria-describedby="validate-priority">
                                <option value="low">Low</option>
                                <option value="medium">Medium</option>
                                <option value="high">High</option>
                            </select>
                            <div id="validate-priority" class="invalid-feedback"></div>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="col-sm-6 form-group">
                            <label for="milestone-id">Milestone</label>
                            <select id="milestone-id" class="form-control" name="milestone_id" aria-describedby="validate-milestone_id">
                                <option value="">-- Not Selected --</option>
                                @foreach ($milestones as $milestone)
                                    <option value="{{$milestone['id']}}">{{$milestone['name']}}</option>
                                @endforeach
                            </select>
                            <div id="validate-milestone_id" class="invalid-feedback"></div>
                        </div>
                        <div class="col-sm-6 form-group">
                            <label for="due-date">Due Date</label>
                            <input type="date" id="due-date" name="due_date" class="form-control" aria-describedby="validate-duedate">
                            <div id="validate-duedate" class="invalid-feedback"></div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="description">Description<br/>(Press Space+Enter to add new line, press Enter to add new paragraph)</label>
                        <div id="description" name="description" class="editor form-control" aria-describedby="validate-description"></div>
                        <div id="validate-description" class="invalid-feedback"></div>
                    </div>
                    <div class="form-group">
                        <label for="user-ids">Users</label>
                        <select id="user-ids" name="user_ids[]" class="select2" multiple="multiple" aria-describedby="validate-user_ids">
                            @foreach ($users as $user)
                                <option value="{{$user['id']}}">{{$user['firstname'].' '.$user['lastname']}}</option>
                            @endforeach
                        </select>
                        <div id="validate-user_ids" class="invalid-feedback"></div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button class="btn btn-default" type="button" onclick="$('#popup-task-form').submit()">Submit</button>
                <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
            </div>
        </div>
    </div>
</div>

@push('ready-scripts')
$('#popup-task').on('hide.bs.modal', popUpTaskHide);
$('#popup-task').on('show.bs.modal', popUpTaskShow);
$('#popup-task').on('shown.bs.modal', popUpTaskShown);
@endpush

@push('scripts')
<script src="{{ asset('lib/moment-with-locales.min.js') }}"></script>
<script>
    var classicEditor = null;

    function popUpTaskHide(e) {
        const $modal = $(this);
        $modal.find('#popup-task-form').off('submit');
        $modal.find('#id').val(null);
        $modal.find('#project_id').val(null);
        $modal.find('#name').val(null);
        $modal.find('#status option:first').prop("selected",true);
        $modal.find('#priority option:first').prop("selected",true);
        $modal.find('#milestone-id').val(null);
        $modal.find('#due-date').val(null);
        $modal.find('#description').val(null);
        $modal.find('#user-ids').val([]).trigger('change');
        $modal.find('.is-invalid').removeClass('is-invalid');
        if(classicEditor) classicEditor.destroy();
    }

    function popUpTaskShow(e) {
        ClassicEditor
            .create( document.querySelector( '.editor' ), {
                toolbar: ['heading', 'bold', 'italic', 'link', 'undo', 'redo', 'numberedList', 'bulletedList']
            } )
            .then(editor => {
                classicEditor = editor;
            })
            .catch( error => {
                console.error( error );
            }
        );
        const $modal = $(this);
        $modal.find('#create-task-title').text('Create Task');
        $modal.find('#project_id').val(getQueryVariable('id'));
        if($modal.data('action') == 'edit') {
            const id = $modal.data('id');
            $modal.find('#id').val(id);
            $modal.find('#create-task-title').text('Update Task');
            $.get('{{route("tasks.edit")}}',{id: id}).done((res) => {
                $modal.find('#name').val(res.name);
                $modal.find('#status').val(res.status);
                $modal.find('#priority').val(res.priority);
                $modal.find('#milestone-id').val(res.milestone_id);
                $modal.find('#due-date').val(res.due_date);
                if(classicEditor) classicEditor.setData(res.description);
                const user_ids = [];
                for(user of res.users) {
                    user_ids.push(user['id']);
                }
                $modal.find('#user-ids').val(user_ids).trigger('change');
            });
        }
    }

    function popUpTaskShown(e) {
        const $modal = $(this);
        const status = $modal.find('#status').val();
        const id = $modal.find('#id').val();
        $modal.find('#popup-task-form').on('submit', (e) => {
            const formData = new FormData(e.target);
            formData.set('description', classicEditor.getData());
            $.ajax({
                method: 'POST',
                url: '{{route("tasks.save")}}',
                data: formData,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                processData: false,
                contentType: false
            }).done((res) => {
                if(res.status == true) {
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
                    if(id != res.id || status != res.data.status)
                        document.querySelector('.board').dispatchEvent(new CustomEvent('list-mutated'));
                    else
                        document.querySelector('.task-card').dispatchEvent(new CustomEvent('card-mutated',{detail: {task_id: res.data.id}}));
                }
            }).fail((jqXHR, textStatus, errorResponse) => {
                if(jqXHR.status == 422) {
                    const errors = jqXHR.responseJSON.errors;
                    for(error in errors) {
                        $('#validate-'+error).text(errors[error]);
                        $('[name='+error+']').addClass('is-invalid');
                    }
                } else {
                    const response = jqXHR.responseJSON;
                    const title = 'Failed!';
                    const message = response.message ?? @json(__('response.server_error'));
                    switch(jqXHR.status) {
                        case 403: title = 'Not Authorized!'; break;
                        case 500: title = 'Server Error'; break;
                    }
                    Swal.fire(
                        title,
                        message,
                        'error'
                    )
                }
            });
            e.preventDefault();
        });
    }
</script>
@endpush
