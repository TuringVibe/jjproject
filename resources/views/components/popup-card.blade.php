@push('head')
    <style>
        .user-img{
            display: inline-flex;
            justify-content: center;
            vertical-align: middle;
            align-items: center;
            width: 3rem;
            height: 3rem;
            border-radius: 50%;
            border: 2px solid white;
            text-transform: uppercase;
            color: white;
            font-weight: bold;
            background-color: var(--com-bg-color-default);
            background-size: cover;
            background-position: center;
        }
        .user-img:not(:first-child) {
            margin-left: -10px;
        }

        .comment {
            display: flex;
            flex-wrap: nowrap;
            align-items: center;
            padding-top: 1rem;
            padding-bottom: 1rem;
        }
        .comment p.text {
            white-space: pre-line;
        }
        .comment .user-img {
            width: 4rem;
            height: 4rem;
            margin-right: 10px;
        }
        .comment .comment-info {
            flex-grow: 1;
            display: flex;
            flex-direction: column;
        }
        .comment .comment-info .user-name {

        }
        .comment .comment-info .text {

        }
        .comment .comment-info textarea {
            display: none;
        }

        .subtasks {
            display: flex;
            flex-direction: column;
            justify-content: flex-start;
            margin-top: 1rem;
        }
        .subtasks .subtask {
            display: flex;
            flex-wrap: nowrap;
            align-items: center;
            margin-bottom: 5px;
            padding-bottom: .2rem;
        }
        .subtasks .subtask input[type=checkbox] {
            margin-right: 1rem;
        }
        .subtasks .subtask span {
            flex-grow: 1;
        }
        .subtasks .subtask span.done {
            text-decoration: line-through;
        }
        .subtasks .subtask textarea{
            display: none;
            flex-grow: 1;
        }
        .subtasks button.add-subtask {
            border: 1px dashed #dee2e6;
            color: #9c9a9a;
        }

        .files-list {
            margin-top: 1rem;
        }
        .file{
            display: flex;
            flex-wrap: nowrap;
            align-items: center;
            padding-top: .5rem;
            padding-bottom: .5rem;
        }
        .file .file-icon {
            width: 3rem;
            height: 3rem;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        .file .file-icon i {
            font-size: 2rem;
        }
        .file .file-icon.pdf, .file .file-icon.ppt, .file .file-icon.pptx{
            color: #ed3232;
        }
        .file .file-icon.xls, .file .file-icon.xlsx{
            color: #0cb800;
        }
        .file .file-icon.doc, .file .file-icon.docx {
            color : #0025b8;
        }
        .file .file-icon.img, .file .file-icon.etc {
            color: #474745;
        }
        .file .file-info {
            flex-grow: 1;
            margin-left: 10px;
            margin-right: 10px;
            display: flex;
            flex-direction: column;
        }
        .file .file-info .size {
            font-size: .8rem;
        }

        .action {
            min-width: 53px;
        }

        .action button {
            margin-left: 5px;
            padding: 5px;
        }

        .nav-tabs .nav-link {
            background-color: var(--com-bg-color-default);
            color: var(--com-color-default);
        }
    </style>
@endpush
<div id="popup-card" class="modal fade" data-backdrop="static" data-keyboard="true" tabindex="-1" aria-labelledby="task-card-title" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body">
                <div class="d-flex flex-nowrap mb-3">
                    <div class="flex-grow-1">
                        <h4 class="modal-title" id="task-card-title"></h4>
                        <span id="priority" class="task-priority"></span>
                    </div>
                    <div>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                </div>
                <div class="row mb-2">
                    <div class="col">
                        <p id="description"></p>
                    </div>
                </div>
                <div class="row mb-2">
                    <div class="col-auto">
                        <h5>Due By</h5>
                        <p id="due-date"></p>
                    </div>
                    <div class="col-auto">
                        <h5>Milestone</h5>
                        <p id="milestone"></p>
                    </div>
                </div>
                <div class="row mb-2">
                    <div class="col">
                        <h5>Assignees</h5>
                        <div id="users"></div>
                    </div>
                </div>
                <div class="row my-5">
                    <div class="col">
                        <h5>Subtasks</h5>
                        <div class="subtasks">
                            <button class="add-subtask">Add subtask +</button>
                        </div>
                    </div>
                </div>
                <ul class="nav nav-tabs" id="myTab" role="tablist">
                    <li class="nav-item" role="presentation">
                      <a class="nav-link active" id="comments-tab" data-toggle="tab" href="#comments" role="tab" aria-controls="comments" aria-selected="true">Comments</a>
                    </li>
                    <li class="nav-item" role="presentation">
                      <a class="nav-link" id="files-tab" data-toggle="tab" href="#files" role="tab" aria-controls="files" aria-selected="false">Files</a>
                    </li>
                  </ul>
                  <div class="tab-content mt-3" id="myTabContent">
                    <div class="tab-pane fade show active" id="comments" role="tabpanel" aria-labelledby="comments-tab">
                        <form id="popup-card-comment-form">
                            <input type="hidden" name="user_id" value="{{auth()->user()->id}}">
                            <div class="form-row flex-nowrap align-items-center">
                                <div class="col-auto flex-grow-1 form-group">
                                    <label for="comment">Write your comment</label>
                                    <textarea id="comment" name="comment" class="form-control" aria-describedby="validate-comment"></textarea>
                                    <div id="validate-comment" class="invalid-feedback"></div>
                                </div>
                                <div class="col-auto">
                                    <button class="btn btn-default">Submit</button>
                                </div>
                            </div>
                        </form>
                        <div class="comments-list"></div>
                    </div>
                    <div class="tab-pane fade" id="files" role="tabpanel" aria-labelledby="files-tab">
                        <form id="popup-card-file-form" enctype="multipart/form-data">
                            <input type="hidden" name="user_id" value="{{auth()->user()->id}}">
                            <div class="form-row flex-nowrap align-items-center">
                                <div class="col-auto flex-grow-1">
                                    <div class="custom-file">
                                        <input type="file" class="custom-file-input" id="file" name="file" aria-describedby="file-help-block validate-file">
                                        <label class="custom-file-label" for="file">Choose file...</label>
                                        <div id="validate-file" class="invalid-feedback"></div>
                                    </div>
                                </div>
                                <div class="col-auto">
                                    <button class="btn btn-default">Submit</button>
                                </div>
                            </div>
                        </form>
                        <div class="files-list"></div>
                    </div>
                  </div>
            </div>
        </div>
    </div>
</div>

@push('ready-scripts')
$('#popup-card').on('hide.bs.modal', popUpCardHide);
$('#popup-card').on('show.bs.modal', popUpCardShow);
$('#popup-card').on('shown.bs.modal', popUpCardShown);
$('.add-subtask').on('click',addSubtask);
@endpush

@push('scripts')
<script src="{{ asset('lib/daterangepicker-3.1/moment.min.js') }}"></script>
<script src="{{ asset('js/tasks/subtask.js') }}"></script>
<script src="{{ asset('js/tasks/comment.js') }}"></script>
<script src="{{ asset('js/tasks/file.js') }}"></script>
<script>
    function popUpCardHide(e) {
        var $modal = $(this);
        $modal.find('#popup-card-comment-form').off('submit');
        $modal.find('#popup-card-file-form').off('submit');
        $modal.find('#task_id').val(null);
        $modal.find('#comment').val(null);
        $modal.find('#file').val(null);
        $modal.find('#task-card-title').text(null);
        $modal.find('#created-date').text(null);
        $modal.find('#due-date').text(null);
        $modal.find('#priority').removeClass().addClass("text-priority");
        $modal.find('#description').text(null);
        $modal.find('#milestone').text(null);
        $modal.find('#users').empty();
        $modal.find('.subtask').remove()
        $modal.find('.comments-list').empty();
        $modal.find('.files-list').empty();
        $modal.find('.is-invalid').removeClass('is-invalid');
    }

    function popUpCardShow(e) {
        var $origin = $(e.relatedTarget);
        var $modal = $(this);
        var id = $origin.data('id');
        $modal.data('id',id);
        $modal.find('.subtask .standby').show();
        $modal.find('.subtask .confirm').hide();
        $modal.find('.subtasks').on('click','.subtask .edit', function(e) {
            var subtask_id = $(this).parent().data('id');
            $(this).parent().siblings("span").hide();
            $(this).parent().siblings("textarea").show();
            $(this).parent().children('.standby').hide();
            $(this).parent().children('.confirm').show();
        });
        $modal.find('.subtasks').on('click','.subtask .cancel', function(e) {
            $(this).parent().siblings("span").show();
            $(this).parent().siblings("textarea").hide();
            $(this).parent().children('.standby').show();
            $(this).parent().children('.confirm').hide();
        });
        $.get('{{route("tasks.card")}}',{id: id}).done((res) => {
            $modal.find('#task_id').val(res.id);
            $modal.find('#task-card-title').text(res.name);
            $modal.find('#created-date').text(moment(res.created_at).format('DD MMM YYYY'));
            $modal.find('#due-date').text(res.due_date == null ? "-" : moment(res.due_date).format('DD MMM YYYY'));
            $modal.find('#priority').addClass(res.priority);
            $modal.find('#description').text(res.description ?? "-");
            $modal.find('#milestone').text(res.milestone == null ? "-" : res.milestone.name);
            if(res != null) {
                for(user of res.users) {
                    var img_url = '/storage/'+user.img_path;
                    var firstnameInitial = user.firstname.charAt(0);
                    var lastnameInitial = user.lastname !== null ? user.lastname.charAt(0) : '';
                    var nameInitial = user.img_path !== null ? '' : firstnameInitial+lastnameInitial;
                    var imgPath = user.img_path !== null ? 'style="background-image: url(\''+img_url+'\')"' : '';
                    $modal.find('#users').append('<span class="user-img" '+imgPath+'>'+nameInitial+'</span>');
                }
            }
            if(res == null || res.users.length == 0) $modal.find('#users').append('-');
            for(subtask of res.subtasks) {
                $subtask = $(subtaskHtml());
                $modal.find('.add-subtask').before($subtask);
                subtaskStandbyMode($subtask, subtask);
            }
            for(comment of res.comments) {
                $comment = $(commentHtml());
                $modal.find('.comments-list').append($comment);
                commentStandbyMode($comment, comment);
                $comment.find('.edit').css("display", comment.can_update ? 'inline-block' : 'none');
                $comment.find('.delete').css("display", comment.can_delete ? 'inline-block' : 'none');
                if(comment.can_update == false && comment.can_delete == false) {
                    $comment.find('.action').hide();
                } else {
                    $comment.find('.action').show();
                }
            }
            for(file of res.files) {
                $file = $(fileHtml());
                $modal.find('.files-list').append($file);
                fileStandbyMode($file, file);
                $file.find('.delete').css("display", file.can_delete ? 'inline-block' : 'none');
                if(file.can_update == false && file.can_delete == false) {
                    $file.find('.action').hide();
                } else {
                    $file.find('.action').show();
                }
            }
        });
    }

    function popUpCardShown(e) {
        var $modal = $(this);
        registerSubmitCommentEvent($modal);
        registerSubmitFileEvent($modal);
    }

    function registerSubmitCommentEvent($modal) {
        $modal.find('#popup-card-comment-form').on('submit', (e) => {
            var formData = new FormData(e.target);
            formData.append('task_id',$modal.data('id'));
            $.ajax({
                method: 'POST',
                url: '{{route("task-comments.save")}}',
                data: formData,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                processData: false,
                contentType: false
            }).done((res) => {
                if(res.status == true) {
                    $comment = $(commentHtml());
                    $modal.find('.comments-list').append($comment);
                    commentStandbyMode($comment, res.data);
                    $comment.find('.edit').css("display", res.data.can_update ? 'inline-block' : 'none');
                    $comment.find('.delete').css("display", res.data.can_delete ? 'inline-block' : 'none');
                    if(res.data.can_update == false && res.data.can_delete == false) {
                        $comment.find('.action').hide();
                    } else {
                        $comment.find('.action').show();
                    }

                    $modal.find('#comment').val(null);
                    $modal.find('#validate-comment').text(null);
                    $modal.find('#comment').removeClass('is-invalid');
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
                    document.querySelector('.task-card').dispatchEvent(new CustomEvent('card-mutated',{detail: {task_id: res.data.task_id}}));
                }
            }).fail((jqXHR, textStatus, errorResponse) => {
                var response = jqXHR.responseJSON;
                if(jqXHR.status == 422) {
                    var errors = response.errors;
                    for(error in errors) {
                        $('#validate-'+error).text(errors[error]);
                        $('[name='+error+']').addClass('is-invalid');
                    }
                } else {
                    var title = 'Failed!';
                    var message = response.message;

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

    function registerSubmitFileEvent($modal) {
        $modal.find('#popup-card-file-form').on('submit', (e) => {
            var formData = new FormData(e.target);
            formData.append('task_id',$modal.data('id'));
            $.ajax({
                method: 'POST',
                url: '{{route("task-files.save")}}',
                data: formData,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                processData: false,
                contentType: false
            }).done((res) => {
                if(res.status == true) {
                    $file = $(fileHtml());
                    $modal.find('.files-list').append($file);
                    fileStandbyMode($file, res.data);
                    $file.find('.delete').css("display", res.data.can_delete ? 'inline-block' : 'none');

                    $modal.find('#file').val(null);
                    $modal.find('#validate-file').text(null);
                    $modal.find('#file').removeClass('is-invalid');
                    $modal.find('.custom-file-label').html("Choose file...");
                    document.querySelector('.task-card').dispatchEvent(new CustomEvent('card-mutated',{detail: {task_id: $modal.data('id')}}));
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
                }
            }).fail((jqXHR, textStatus, errorResponse) => {
                var response = jqXHR.responseJSON;
                if(jqXHR.status == 422) {
                    var errors = response.errors;
                    for(error in errors) {
                        $('#validate-'+error).text(errors[error]);
                        $('[name='+error+']').addClass('is-invalid');
                    }
                } else {
                    var title = 'Failed!';
                    var message = response.message;
                    switch(jqXHR.status) {
                        case 403: title = 'Not Authorized!'; break;
                        case 500: title = 'Server Error'; break;
                        case 413:
                            title = 'File is too big';
                            message = 'File size is bigger than server allowance';
                        break;
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
