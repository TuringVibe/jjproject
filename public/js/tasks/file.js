function fileHtml() {
    var html =
    '<div class="file">'+
        '<span class="file-icon"></span>'+
        '<div class="file-info">'+
            '<span class="filename"></span>'+
            '<span class="size"></span>'+
        '</div>'+
        '<div class="action">'+
            '<button class="btn btn-negative download"><i class="fas fa-download"></i></button>'+
            '<button class="btn btn-negative delete"><i class="fas fa-trash"></i></button>'+
        '</div>'+
    '</div>';
    return html;
}

function deleteFile(e) {
    $file = $(this).closest(".file");
    var id = $file.data('id');
    var task_id = $('#popup-card').data('id');
    $.ajax({
        method: 'POST',
        url: '/task-files/delete',
        data: {id: id},
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    }).done(function(res) {
        if(res.status == true) {
            $file.remove();
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

function downloadFile(e) {
    $file = $(this).closest('.file');
    var id = $file.data('id');
    window.location.href = '/task-files/download?id='+id;
}

function fileStandbyMode($file, data) {
    if(data != null) {
        if($file.data('id') == null) $file.data('id',data.id);
        $file.find('.filename').text(data.filename);
        $file.find('.size').text(new Intl.NumberFormat().format(new Number(data.size/1000).toFixed(1))+" KB");
        $file.find('.file-icon').addClass(data.ext);
        switch(data.ext) {
            case "pdf":
                $file.find('.file-icon').html('<i class="fas fa-file-pdf"></i>');
            break;
            case "ppt": case "pptx":
                $file.find('.file-icon').html('<i class="fas fa-file-powerpoint"></i>');
            break;
            case "doc": case "docx":
                $file.find('.file-icon').html('<i class="fas fa-file-word"></i>');
                break;
            case "xls": case "xlsx":
                $file.find('.file-icon').html('<i class="fas fa-file-excel"></i>');
                break;
            case "png": case "jpg": case "jpeg": case "bmp": case "gif": case "svg":
                $file.find('.file-icon').html('<i class="fas fa-file-image"></i>');
                break;
            case "csv":
                $file.find('.file-icon').html('<i class="fas fa-file-csv"></i>');
                break;
            case "ogg": case "mp4": case "flv": case "mov": case "mpg": case "mpeg": case "mkv": case "webm": case "avi": case "avchd": case "wmv":
                $file.find('.file-icon').html('<i class="fas fa-file-video"></i>');
                break;
            case "mp3": case "m4a": case "flac": case "wav": case "wma": case "aac":
                $file.find('.file-icon').html('<i class="fas fa-file-audio"></i>');
            default:
                $file.find('.file-icon').html('<i class="fas fa-file"></i>');
                break;
        }
    }
    fileStandbyModeEvents($file);
}

function fileStandbyModeEvents($file) {
    $file.find('.download').on('click', downloadFile);
    $file.find('.delete').on('click', deleteFile);
}
