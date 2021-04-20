function commentHtml() {
    var html =
    '<div class="comment">'+
        '<span class="user-img"></span>'+
        '<div class="comment-info">'+
            '<span class="user-name"></span>'+
            '<p class="text"></p>'+
            '<textarea class="text"></textarea>'+
        '</div>'+
        '<div class="action">'+
            '<button class="btn btn-negative standby edit"><i class="fas fa-pen"></i></button>'+
            '<button class="btn btn-negative standby delete"><i class="fas fa-trash"></i></button>'+
            '<button class="btn btn-negative confirm save"><i class="fas fa-check"></i></button>'+
            '<button class="btn btn-negative confirm cancel"><i class="fas fa-times"></i></button>'+
        '</div>'+
    '</div>';
    return html;
}

async function getCommentByHTMLComponent($comment, data) {
    var comment = $comment.find('textarea').val();
    var user_id = $comment.find('.user-name').data('id');
    if(data.id == null) {
        task_id = $('#popup-card').data('id');
        data.task_id = task_id;
        data.user_id = user_id;
        data.comment = comment;
    } else {
        if(comment != null && comment != "") {
            data.comment = comment;
        }
    }
    return data;
}

async function retrieveCommentFromServer(id) {
    var data = { id: id };
    if(data.id == null) return data;
    var res = await $.get('/task-comments/edit', {id:id});
    data.task_id = res.data.task_id;
    data.user_id = res.data.user_id;
    data.comment = res.data.comment;
    return data;
}

function saveComment(e) {
    $comment = $(this).closest('.comment');
    retrieveCommentFromServer($comment.data('id'))
    .then((data) => {
        getCommentByHTMLComponent($comment, data)
        .then((data) => {
            $.ajax({
                method: 'POST',
                url: '/task-comments/save',
                data: JSON.stringify(data),
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                processData: false,
                contentType: "application/json"
            }).done(function(res) {
                if(res.status == true) {
                    commentStandbyMode($comment, res.data);
                    document.querySelector('.task-card').dispatchEvent(new CustomEvent('card-mutated',{detail: {task_id: res.data.task_id}}));
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
            }).fail(function(jqXHR) {
                if(jqXHR.status == 422) {
                    var errors = jqXHR.responseJSON.errors;
                    for(error in errors) {
                        $('#validate-'+error).text(errors[error]);
                        $('[name='+error+']').addClass('is-invalid');
                    }
                } else {
                    var response = jqXHR.responseJSON;
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
        });
    })
}

function deleteComment(e) {
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
            $comment = $(this).closest(".comment");
            var id = $comment.data('id');
            var task_id = $('#popup-card').data('id');
            $.ajax({
                method: 'POST',
                url: '/task-comments/delete',
                data: {id: id},
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            }).done(function(res) {
                if(res.status == true) {
                    $comment.remove();
                    document.querySelector('.task-card').dispatchEvent(new CustomEvent('card-mutated',{detail: {task_id: task_id}}));
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
            }).fail(function(jqXHR) {
                if(jqXHR.status == 422) {
                    var errors = jqXHR.responseJSON.errors;
                    for(error in errors) {
                        $('#validate-'+error).text(errors[error]);
                        $('[name='+error+']').addClass('is-invalid');
                    }
                } else {
                    var response = jqXHR.responseJSON;
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
        }
    });
}

function editComment(e) {
    $comment = $(this).closest(".comment");
    var id = $comment.data('id');
    $.get('/task-comments/edit',{
        id: id
    }).done(function(res){
        if(res.status == true) {
            commentEditMode($comment, res.data);
        }
    });
}

function cancelComment(e) {
    $comment = $(this).closest('.comment');
    commentStandbyMode($comment);
}

function commentStandbyMode($comment, data) {
    if(data != null) {
        if($comment.data('id') == null) $comment.data('id', data.id);
        $comment.find('.user-img').data('id',data.user.user_id);
        if(data.user.img_path != null) {
            var img_url = '/storage/'+data.user.img_path;
            $comment.find('.user-img').css('background-image','url("'+img_url+'")');
        }
        else {
            var firstnameInitial = data.user.firstname.charAt(0);
            var lastnameInitial = data.user.lastname !== null ? data.user.lastname.charAt(0) : '';
            var nameInitial = firstnameInitial+lastnameInitial;
            $comment.find('.user-img').text(nameInitial);
        }
        $comment.find('.user-name').text(data.user.firstname+' '+data.user.lastname);
        $comment.find('p.text').text(data.comment);
    }

    $comment.find('p.text').show();
    $comment.find('.standby').show();
    $comment.find('textarea.text').hide();
    $comment.find('.confirm').hide();
    commentStandbyModeEvents($comment);
}

function commentEditMode($comment, data) {
    if(data != null) {
        $comment.find('.user-img').data('id',data.user_id);
        $comment.find('textarea.text').val(data.comment);
    }

    $comment.find('p.text').hide();
    $comment.find('.standby').hide();
    $comment.find('textarea.text').show();
    $comment.find('.confirm').show();
    commentEditModeEvents($comment);
}

function commentStandbyModeEvents($comment) {
    $comment.find('.confirm').off('click');
    $comment.find('.edit').on('click', editComment);
    $comment.find('.delete').on('click', deleteComment);
}

function commentEditModeEvents($comment) {
    $comment.find('.standby').off('click');
    $comment.find('.save').on('click', saveComment);
    $comment.find('.cancel').on('click', cancelComment);
}
