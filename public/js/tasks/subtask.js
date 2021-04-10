function subtaskHtml() {
    html =
    '<div class="subtask">'+
        '<input type="checkbox">'+
        '<span></span>'+
        '<textarea></textarea>'+
        '<div class="action">'+
            '<button class="btn btn-negative standby edit"><i class="fas fa-pen"></i></button>'+
            '<button class="btn btn-negative standby delete"><i class="fas fa-trash"></i></button>'+
            '<button class="btn btn-negative confirm save"><i class="fas fa-check"></i></button>'+
            '<button class="btn btn-negative confirm cancel"><i class="fas fa-times"></i></button>'+
        '</div>'+
    '</div>';
    return html;
}

function addSubtask(e) {
    $subtask = $(subtaskHtml());
    $(this).before($subtask);
    subtaskEditMode($subtask, null);
}

async function getSubtaskByHTMLComponent($subtask, data) {
    var name = $subtask.find('textarea').val();
    var isDone = $subtask.find('input[type=checkbox]');
    if(data.id == null) {
        name = name.split("\n");
        if(name.length == 1) name = name[0];
        task_id = $('#popup-card').data('id');
        data.task_id = task_id;
        data.name = name;
        data.is_done = false;
    } else {
        if(name != null && name != "") {
            data.name = name;
        }
        if(isDone.length && isDone.prop('disabled') == false){
            data.is_done = isDone.is(":checked");
        }
    }
    return data;
}

async function retrieveSubtaskFromServer(id) {
    var data = {
        id: id
    };
    if(data.id == null) return data;
    var res = await $.get('/subtasks/edit',{
        id: data.id
    });
    data.name = res.data.name;
    data.task_id = res.data.task_id;
    data.is_done = res.data.is_done;
    return data;
}

function saveSubtask(e) {
    var $subtask = $(this).closest(".subtask");
    retrieveSubtaskFromServer($subtask.data('id'))
    .then((data) => {
        getSubtaskByHTMLComponent($subtask, data)
        .then((data) => {
            var url = '/subtasks/save';
            if(Array.isArray(data.name)) url = '/subtasks/bulk-insert';
            $.ajax({
                method: 'POST',
                url: url,
                data: JSON.stringify(data),
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                processData: false,
                contentType: "application/json"
            }).done(function(res) {
                if(res.status == true) {
                    if(res.data.length > 1) {
                        for(i in res.data) {
                            if(i == 0) subtaskStandbyMode($subtask, res.data[i]);
                            else {
                                $nextSubtask = $(subtaskHtml());
                                $subtask.after($nextSubtask);
                                subtaskStandbyMode($nextSubtask, res.data[i]);
                                $subtask = $nextSubtask;
                            }
                        }
                    } else {
                        subtaskStandbyMode($subtask, res.data);
                    }
                    document.querySelector('.task-card').dispatchEvent(new CustomEvent('card-mutated',{detail: {task_id: data.task_id}}));
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
            });
        });
    })
}

function editSubtask(e) {
    $subtask = $(this).closest(".subtask");
    var id = $subtask.data('id');
    $.get('/subtasks/edit',{
        id: id
    }).done(function(res){
        if(res.status == true) {
            subtaskEditMode($subtask, res.data);
        }
    });
}

function cancelSubtask(e) {
    $subtask = $(this).closest('.subtask');
    var id = $subtask.data('id');
    if(id == null) {
        $subtask.remove();
    } else {
        subtaskStandbyMode($subtask);
    }
}

function deleteSubtask(e) {
    $this = $(this);
    var id = $this.closest(".subtask").data('id');
    var task_id = $('#popup-card').data('id');
    $.ajax({
        method: 'POST',
        url: '/subtasks/delete',
        data: {id: id},
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    }).done(function(res) {
        if(res.status == true) {
            $this.closest(".subtask").remove();
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
    });
}

function subtaskEditMode($subtask, data) {
    if(data != null) {
        $subtask.find("textarea").val(data.name);
        $subtask.find('input[type=checkbox]').prop('checked',data.is_done);
    }

    $subtask.find('input[type=checkbox]').prop('disabled',true);
    $subtask.find('span').hide();
    $subtask.find('textarea').show();
    $subtask.find('textarea').focus();
    $subtask.find('.standby').hide();
    $subtask.find('.confirm').show();

    subtaskEditModeEvents($subtask);
}

function subtaskStandbyMode($subtask, data) {
    if(data != null) {
        if($subtask.data('id') == null) $subtask.data('id', data.id);
        $subtask.find("span").text(data.name);
        $subtask.find("input[type=checkbox]").prop('checked',data.is_done);
        if(data.is_done) $subtask.find("span").addClass('done');
        else $subtask.find("span").removeClass('done');
    }

    $subtask.find("textarea").hide();
    $subtask.find("span").show();
    $subtask.find("input[type=checkbox]").prop('disabled',false);
    $subtask.find(".standby").show();
    $subtask.find(".confirm").hide();
    subtaskStandbyModeEvents($subtask);
}

function subtaskEditModeEvents($subtask) {
    $subtask.find('textarea').on('keydown',function(e){ if(e.key == "Enter") e.preventDefault(); });
    $subtask.find('textarea').on('keyup',function(e){ if(e.key == "Enter" && $(this).val() != "") $('.save').click() });
    $subtask.find(".standby").off("click");
    $subtask.find(".save").on("click",saveSubtask);
    $subtask.find(".cancel").on("click",cancelSubtask);
}

function subtaskStandbyModeEvents($subtask) {
    $subtask.find('input[type=checkbox]').on('change',saveSubtask);
    $subtask.find('textarea').off('keydown');
    $subtask.find('textarea').off('keyup');
    $subtask.find(".confirm").off("click");
    $subtask.find(".edit").on("click",editSubtask);
    $subtask.find(".delete").on("click",deleteSubtask);
}
