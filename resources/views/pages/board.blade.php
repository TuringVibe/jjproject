@extends('layouts.master')

@push('head')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="{{ asset('css/content.css') }}">
    <style>
        .content {
            display: flex;
            flex-direction: column;
        }
        .user-img {
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

        .label-list {
            display: inline-block;
            padding: .3rem;
            border-radius: 5px;
            color: white;
        }

        .label-list:not(:first-child) {
            margin-left: 5px;
        }

        .board {
            width: auto;
            flex-grow: 1;
            display: flex;
            flex-direction: row;
            justify-content: space-between;
            overflow-x: auto;
        }

        .task-container {
            height:calc(100% - 2.5rem);
            box-sizing: border-box;
            width: 33%;
            min-width: 358px;
        }

        .task-container:first-child {
            padding-right: 1.5rem;
        }

        .task-container:nth-child(2) {
            padding-left: .75rem;
            padding-right: .75rem;
        }

        .task-container:last-child {
            padding-left: 1.5rem;
        }

        .task-status {
            margin-bottom: .5rem;
        }

        .task-list {
            padding: .5rem;
            min-height: 75px;
            max-height: 100%;
            border-radius: 5px;
            background-color: var(--task-list-bg-color);
            overflow-y:auto;
        }

        .task-card {
            margin-top: .5rem;
            border-radius: 5px;
            background-color: var(--task-card-bg-color);
            min-height: 100px;
            height: auto;
            padding: 1rem;
            position: relative;
            display: flex;
            flex-direction: column;
            cursor: pointer;
        }

        .task-action {
            position: absolute;
            top: 0;
            right: 10px;
            border-bottom-left-radius: 50%;
            border-bottom-right-radius: 50%;
            z-index: 100;
            box-shadow: 0px 5px 6px -5px #5c5c5c;
            width: 50px;
            background-color: var(--com-bg-color-default);
            height: 25px;
            display: flex;
            justify-content: space-evenly;
            align-items: center;
        }

        .task-action button {
            border: none;
            padding: 0;
            color: var(--com-color-default);
            background: none;
        }

        .task-card:first-child {
            margin-top: 0;
        }

        .task-title {
            font-size: 1.1rem;
            font-weight: bold;
        }

        .task-body {
            display: flex;
            flex-wrap: wrap;
            justify-content: flex-end;
            margin-top: auto;
        }

        .task-body span {
            margin-left: 10px;
        }

        .task-body span i {
            margin-left: 3px;
        }

        .task-body span:first-child {
            margin-right: auto;
            margin-left: 0;
        }

        .task-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .task-priority {
            display: inline-block;
            border-radius: 5px;
            padding-left: .3rem;
            padding-right: .3rem;
            font-size: .75rem;
            font-weight: bold;
            color: var(--task-priority-color);
        }

        .task-priority.low {
            background-color: #2765e3;
        }

        .task-priority.low::before {
            content: 'low';
        }

        .task-priority.medium {
            background-color: #cfcc21;
        }

        .task-priority.medium::before {
            content: 'medium';
        }

        .task-priority.high {
            background-color: #ed3232;
        }

        .task-priority.high::before {
            content: 'high';
        }

        .task-total-comments,
        .task-duedate,
        .task-total-files,
        .task-checklist {
            color: var(--task-card-info-color);
        }

        .placeholder {
            margin-top: .5rem;
            height: 100px;
            background-color: #8F9B8E;
            border-radius: 5px;
        }
    </style>
@endpush

@section('content')
    <div class="content container-fluid">
        @include('components.content-header',[
                'with_btn' => true,
                'btn_label' => 'Create Task',
                'action' => 'openModal()'
            ]
        )
        <div class="board">
            <div class="task-container">
                <h4 class="task-status">To Do</h4>
                <div id="todo" class="task-list"></div>
            </div>
            <div class="task-container">
                <h4 class="task-status">In Progress</h4>
                <div id="inprogress" class="task-list"></div>
            </div>
            <div class="task-container">
                <h4 class="task-status">Done</h4>
                <div id="done" class="task-list"></div>
            </div>
        </div>
    </div>
    @include('components.popup-task')
    @include('components.popup-card')
@endsection

@push('scripts')
    <script src="{{ asset('lib/moment-with-locales.min.js') }}"></script>
    <script>
        var taskLists = {};

        function deleteData(elem) {
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
                    $.ajax({
                        method: 'POST',
                        url: '{{ route('tasks.delete') }}',
                        data: {
                            id: $(elem).data('id')
                        },
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        }
                    }).done((res) => {
                        if (res.status == true) {
                            Swal.fire(
                                'Success!',
                                res.message,
                                'success'
                            )
                            document.querySelector('.board').dispatchEvent(new CustomEvent('list-mutated'));
                        } else {
                            Swal.fire(
                                'Failed!',
                                res.message,
                                'error'
                            )
                        }
                    }).fail((jqXHR) => {
                        var response = jqXHR.responseJSON;
                        var title = 'Failed!';
                        var message = response.message ?? @json(__('response.server_error'));
                        switch(jqXHR.status) {
                            case 403: title = 'Not Authorized!'; break;
                            case 500: title = 'Server Error'; break;
                        }
                        Swal.fire(
                            title,
                            message,
                            'error'
                        )
                    });
                }
            });
        }

        function refreshCard(task_id) {
            $.get(@json(route('tasks.card')), {
                    id: task_id
                })
                .done(function(res) {
                    $card = $('.task-card[data-id=' + res.id + ']');
                    $card.find('.task-title').text(res.name);
                    $card.find('.task-checklist').html(res.done_subtasks_count + '/' + res.subtasks_count +
                        ' <i class="far fa-check-square"></i>');
                    $card.find('.task-total-comments').html(res.comments_count + ' <i class="far fa-comment"></i>');
                    $card.find('.task-total-files').html(res.files_count + ' <i class="fas fa-paperclip"></i>');
                    $card.find('.task-priority').removeClass('low medium high').addClass(res.priority);
                    $card.find('.task-duedate').text((res.due_date == null ? "" : "Due by " + moment(res.due_date).format('LL')));
                    $card.find('.edit').css("display", res.can_update ? "block" : "none");
                    $card.find('.delete').css("display", res.can_delete ? "block" : "none");
                    if(res.can_update == false && res.can_delete == false)
                        $card.find('.task-action').hide();
                    else $card.find('.task-action').show();
                }).fail(function() {
                    Swal.fire({
                        toast: true,
                        position: 'top',
                        showConfirmButton: false,
                        timer: 3000,
                        timerProgressBar: true,
                        title: 'Error',
                        text: "Failed to refresh the card",
                        icon: 'error'
                    });
                });
        }

        function loadCards(firstLoad = false) {
            $.get(@json(route('tasks.cards')), {
                project_id: getQueryVariable('id')
            }).done(function(res) {
                $('.task-list').empty();
                for (task of res) {
                    $taskCard = $(taskCardHtml(
                        task.id,
                        task.name,
                        task.done_subtasks_count,
                        task.subtasks_count,
                        task.comments_count,
                        task.files_count,
                        task.priority,
                        task.due_date,
                        task.can_update,
                        task.can_delete
                    ));
                    if(task.can_update == false && task.can_delete == false) {
                        $taskCard.find('.task-action').hide();
                    } else {
                        $taskCard.find('.task-action').show();
                    }
                    $taskCard.data('order', task.order);
                    $taskCard.data('status', task.status);
                    $('#' + task.status).append($taskCard);
                }
                $('.task-card').on('card-mutated', function(e) {
                    if(e != undefined)
                        refreshCard(e.detail.task_id);
                });
                if(firstLoad) {
                    var task_id = getQueryVariable('task_id');
                    if(task_id != false) {
                        $('.task-card[data-id='+task_id+']').click();
                    }
                }
            }).fail(function(jqXHR) {
                Swal.fire({
                    toast: true,
                    position: 'top',
                    showConfirmButton: false,
                    timer: 3000,
                    timerProgressBar: true,
                    title: 'Error',
                    text: "Failed to refresh the list",
                    icon: 'error'
                });
            });
        }

        function taskCardHtml(
            taskId,
            taskName,
            totalDoneSubtasks,
            totalSubtasks,
            totalComments,
            totalFiles,
            priority,
            dueDate,
            canUpdate,
            canDelete
        ) {
            var btnEdit =
            '<button class="edit" type="button" data-action="edit" data-id="' + taskId + '">' +
                '<i class="fas fa-pen"></i>' +
            '</button>';
            var btnDelete =
            '<button class="delete" type="button" data-id="' + taskId + '">'+
                '<i class="fas fa-trash"></i>'+
            '</button>';
            html =
                '<div class="task-card" data-id="' + taskId +
                '" data-toggle="modal" data-target="#popup-card" draggable="true">' +
                '<div class="task-action">' +
                (!canUpdate ? '' : btnEdit) +
                (!canDelete ? '' : btnDelete) +
                '</div>'+
                '<p class="task-title">' + taskName + '</p>' +
                '<div class="task-body">' +
                '<span class="task-checklist">' + totalDoneSubtasks + '/' + totalSubtasks +
                ' <i class="far fa-check-square"></i></span>' +
                '<span class="task-total-comments">' + totalComments + ' <i class="far fa-comment"></i></span>' +
                '<span class="task-total-files">' + totalFiles + ' <i class="fas fa-paperclip"></i></span>' +
                '</div>' +
                '<div class="task-footer">' +
                '<span class="task-priority ' + priority + '"></span>' +
                '<span class="task-duedate">' + (dueDate == null ? "" : "Due by " + moment(dueDate).format('LL')) +
                '</span>' +
                '</div>' +
                '</div>';
            return html;
        }

        function openModal() {
            $('#popup-task').removeData();
            $('#popup-task').modal('show');
        }

        async function saveTaskCard(taskId, destStatus, destOrder) {
            var project_id = getQueryVariable('id');
            var result = await $.ajax({
                method: 'POST',
                url: '/tasks/move',
                data: {
                    project_id: project_id,
                    task_id: taskId,
                    dest_status: destStatus,
                    dest_order: destOrder
                },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            if(result.status == false) {
                Swal.fire(
                    'Failed!',
                    result.message,
                    'error'
                );
            }
            return result.status;
        }

    </script>
@endpush

@push('ready-scripts')
    loadCards(true);

    $('.board').on('list-mutated',function(e) {
        loadCards();
    }).on('click','.edit',function(e) {
        $('#popup-task').removeData();
        $('#popup-task').data('id',$(this).data('id'));
        $('#popup-task').data('action',$(this).data('action'));
        $('#popup-task').modal("show");
        e.stopPropagation();
    }).on('click','.delete',function(e) {
        deleteData(this);
        e.stopPropagation();
    });

    var $dragged = null;

    $('.board').on('dragstart',function(e){
        $dragged = $(e.target).closest('.task-card');
        $dragged.css('opacity','0.4');
        e.originalEvent.dataTransfer.effectAllowed = 'move';
        e.originalEvent.dataTransfer.setData('text',JSON.stringify($dragged.data()));
    }).on('dragover', function(e){
        if($(e.target).closest('.task-list')) {
            e.originalEvent.dataTransfer.dropEffect = "move";
            e.preventDefault();
        }
    }).on('dragleave',(e) => {
        if(e.target.classList.contains('task-list')) {
            $('.placeholder').remove();
        }
    }).on('dragenter',function(e){
        e.preventDefault();
        $placeholder = $('<div class="placeholder"></div>');
        if($(e.target).closest('.task-card').length
        && $(e.target).closest('.task-card').data('id') == $dragged.data('id')) return;

        if($(e.target).is('.task-list') && !$(e.target).has('.task-card').length) {
            if($(e.target).has('.placeholder').length) return ;
            $('.placeholder').remove();
            var status = $(e.target).attr('id');
            $(e.target).append($placeholder);
            $placeholder.data('dest-order',1);
            $placeholder.data('dest-status',status);
        } else if($(e.target).is('.task-title')) {
            $taskCard = $(e.target).closest('.task-card');
            if($taskCard.prev('.placeholder').length) return ;
            $('.placeholder').remove();
            $taskCard.before($placeholder);
            var destOrder = $taskCard.data('order');
            $placeholder.data('dest-order',destOrder);
            $placeholder.data('dest-status',$taskCard.data('status'));
        } else if($(e.target).parent().is('.task-card')){
            $taskCard = $(e.target).closest('.task-card');
            if($taskCard.next('.placeholder').length) return ;
            $('.placeholder').remove();
            $taskCard.after($placeholder);
            var destOrder = $taskCard.data('order');
            destOrder += 1;
            $placeholder.data('dest-order',destOrder);
            $placeholder.data('dest-status',$taskCard.data('status'));
        }
    }).on('dragend',function(e){
        if($(e.target).has('.task-card')) {
            $(e.target).css('opacity','1');
        }
        $('.placeholder').remove();
    }).on('drop',function(e) {
        e.preventDefault();
        $placeholder = $(this).find('.placeholder');
        var destOrder = $placeholder.data('dest-order');
        var destStatus = $placeholder.data('dest-status');
        var src = JSON.parse(e.originalEvent.dataTransfer.getData('text'));
        if(destOrder != $dragged.data('order') || destStatus != $dragged.data('status')) {
            saveTaskCard(src.id,destStatus,destOrder).then((res) => {
                document.querySelector('.board').dispatchEvent(new CustomEvent('list-mutated'))
            });
        }
        $placeholder.after($dragged);
        $('.placeholder').remove();
    });
@endpush
