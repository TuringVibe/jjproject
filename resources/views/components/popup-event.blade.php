@push('head')
    <link rel="stylesheet" href="{{ asset('lib/daterangepicker-3.1/daterangepicker.css') }}">
@endpush
<div id="popup-event" class="modal fade" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="create-event-title" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="create-event-title">Create Event</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="popup-event-form">
                    <input type="hidden" id="id" name="id">
                    <div class="form-row">
                        <div class="col-6 form-group">
                            <label for="startdatetime">Start Datetime</label>
                            <p id="p-startdatetime"></p>
                            <input type="text" id="startdatetime" name="startdatetime" class="single-date-picker form-control" aria-describedby="validate-startdatetime">
                            <div id="validate-startdatetime" class="invalid-feedback"></div>
                        </div>
                        <div class="col-6 form-group">
                            <label for="enddatetime">End Datetime</label>
                            <p id="p-enddatetime"></p>
                            <input type="text" id="enddatetime" name="enddatetime" class="single-date-picker form-control" aria-describedby="validate-enddatetime">
                            <div id="validate-enddatetime" class="invalid-feedback"></div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="name">Name</label>
                        <p id="p-name"></p>
                        <input type="text" id="name" name="name" class="form-control" aria-describedby="validate-name">
                        <div id="validate-name" class="invalid-feedback"></div>
                    </div>
                    <div class="form-group">
                        <label for="repeat">Repeat</label>
                        <p id="p-repeat"></p>
                        <select id="repeat" class="form-control" name="repeat" aria-describedby="validate-repeat">
                            <option value="once">Once</option>
                            <option value="daily">Daily</option>
                            <option value="weekly">Weekly</option>
                            <option value="biweekly">Bi-Weekly</option>
                            <option value="monthly">Monthly</option>
                            <option value="yearly">Yearly</option>
                        </select>
                        <div id="validate-repeat" class="invalid-feedback"></div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button class="btn btn-default show" type="button" id="edit">Edit</button>
                <button class="btn btn-danger show" type="button" id="delete">Delete</button>
                <button class="btn btn-default edit" type="button" id="submit" onclick="$('#popup-event-form').submit()">Submit</button>
                <button class="btn btn-secondary edit" type="button" id="cancel" data-dismiss="modal">Cancel</button>
            </div>
        </div>
    </div>
</div>

@push('ready-scripts')
    $('#popup-event').on('hide.bs.modal', popUpEventHide);
    $('#popup-event').on('show.bs.modal', popUpEventShow);

    $('.single-date-picker').on('apply.daterangepicker', function(ev, picker) {
        $(this).val(picker.startDate.format('YYYY-MM-DD HH:mm:ss'));
    });

    $('.single-date-picker').on('cancel.daterangepicker', function(ev, picker) {
        $(this).val('');
    });
@endpush

@push('scripts')
<script src="{{ asset('lib/moment-with-locales.min.js') }}"></script>
<script src="{{ asset('lib/daterangepicker-3.1/daterangepicker.js') }}"></script>
<script>
    var drpOptions = {
        singleDatePicker: true,
        timePicker: true,
        applyClass: "btn-default",
        cancelClass: "btn-secondary",
        timePicker24Hour: true,
        timePickerSeconds: true,
        locale: {
            cancelLabel: 'Clear',
            format: 'YYYY-MM-DD HH:mm:ss'
        }
    };

    function deleteEvent() {
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
                var $modal = $('#popup-event');
                var id = $modal.data('id');
                $.ajax({
                    url: @json(route('events.delete')),
                    method: 'POST',
                    data: {id: id},
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
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
                        document.querySelector('#calendar').dispatchEvent(new CustomEvent('mutated'));
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
                }).fail((jqXHR, textStatus, errorResponse) => {
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
                });
            }
        });
    }

    function editMode() {
        var $modal = $('#popup-event');
        $modal.find('#p-startdatetime').hide();
        $modal.find('#p-enddatetime').hide();
        $modal.find('#p-name').hide();
        $modal.find('#p-repeat').hide();
        $modal.find('#startdatetime').show();
        $modal.find('#enddatetime').show();
        $modal.find('#name').show();
        $modal.find('#repeat').show();
        $modal.find('.show').hide();
        $modal.find('.edit').show();
        $modal.find('#edit').off('click');
        $modal.find('#delete').off('click');
        $modal.find('#create-event-title').text('Update Event');
        $modal.find('#popup-event-form').on('submit', (e) => {
            submitForm(e.target,$modal);
        });

        var id = $modal.data('id');
        $.get('{{route("events.edit")}}',{id: id}).done((res) => {
            $modal.find('#id').val(res.id);
            $modal.find('#startdatetime').val(moment.utc(res.startdatetime).format('YYYY-MM-DD HH:mm:ss'));
            $modal.find('#enddatetime').val(moment.utc(res.enddatetime).format('YYYY-MM-DD HH:mm:ss'));
            $('.single-date-picker').daterangepicker(drpOptions);
            $modal.find('#name').val(res.name);
            $modal.find('#repeat').val(res.repeat);
        });
    }

    function popUpEventHide(e) {
        var $modal = $(this);
        $modal.find('#popup-event-form').off('submit');
        $modal.find('#id').val(null);
        $modal.find('#startdatetime').val(null);
        $modal.find('#enddatetime').val(null);
        $modal.find('#name').val(null);
        $modal.find('#repeat').val(null);
        $modal.find('.is-invalid').removeClass('is-invalid');
    }

    function popUpEventShow(e) {
        var $modal = $(this);
        if($modal.data('action') == 'show') {
            $modal.find('#p-startdatetime').show();
            $modal.find('#p-enddatetime').show();
            $modal.find('#p-name').show();
            $modal.find('#p-repeat').show();
            $modal.find('#startdatetime').hide();
            $modal.find('#enddatetime').hide();
            $modal.find('#name').hide();
            $modal.find('#repeat').hide();
            $modal.find('.show').show();
            $modal.find('.edit').hide();
            $modal.find('#edit').on('click', (e) => { editMode(); });
            $modal.find('#delete').on('click', (e) => { deleteEvent(); });
            $modal.find('#create-event-title').text('Event');
            $modal.find('')

            var id = $modal.data('id');
            $.get('{{route("events.edit")}}',{id: id}).done((res) => {
                $modal.find('#id').val(res.id);
                $modal.find('#p-startdatetime').text(moment.utc(res.startdatetime).format('YYYY-MM-DD HH:mm:ss'));
                $modal.find('#p-enddatetime').text(moment.utc(res.enddatetime).format('YYYY-MM-DD HH:mm:ss'));
                $modal.find('#p-name').text(res.name);
                $modal.find('#p-repeat').text(res.repeat);
            });
        } else {
            $modal.find('#create-event-title').text('Create Event');
            $modal.find('#id').val(null);
            $modal.find('#p-startdatetime').hide();
            $modal.find('#p-enddatetime').hide();
            $modal.find('#p-name').hide();
            $modal.find('#p-repeat').hide();

            $modal.find('#startdatetime').show();
            $modal.find('#enddatetime').show();
            $modal.find('#name').show();
            $modal.find('#repeat').show();
            $modal.find('#edit').off('click');
            $modal.find('#delete').off('click');
            $modal.find('.show').hide();
            $modal.find('.edit').show();
            $modal.find('#popup-event-form').on('submit', (e) => {
                submitForm(e.target,$modal);
            });
            $('.single-date-picker').daterangepicker(drpOptions);
        }
    }

    function submitForm(form, $modal) {
        console.log(form, $modal)
        var formData = new FormData(form);
        $.ajax({
            method: 'POST',
            url: '{{route("events.save")}}',
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
                document.querySelector('#calendar').dispatchEvent(new CustomEvent('mutated'));
            }
        }).fail((jqXHR, textStatus, errorResponse) => {
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
        e.preventDefault();
    }
</script>
@endpush
