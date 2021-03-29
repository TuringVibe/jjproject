<div id="popup-user" class="modal fade" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="create-user-title" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="create-user-title">Create User</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="popup-user-form" method="POST" action="{{route("users.save")}}" enctype="multipart/form-data">
                    <input type="hidden" id="id" name="id">
                    <div class="form-row">
                        <div class="col-sm-6 form-group">
                            <label for="email">Email</label>
                            <input type="email" id="email" name="email" class="form-control" aria-describedby="validate-email">
                            <div id="validate-email" class="invalid-feedback">
                            </div>
                        </div>
                        <div class="col-sm-6 form-group">
                            <label for="role">Role</label>
                            <select id="role" name="role" class="form-control" aria-describedby="validate-role">
                                <option value="user">User</option>
                                <option value="admin">Admin</option>
                            </select>
                            <div id="validate-role" class="invalid-feedback">
                            </div>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="col-sm-6 form-group">
                            <label for="password">Password</label>
                            <input type="password" autocomplete="off" id="password" name="password" class="form-control" aria-describedby="password-help-block validate-password">
                            <small id="password-help-block" class="form-text text-muted">
                                Leave it blank, unless if you want to update the password.
                            </small>
                            <div id="validate-password" class="invalid-feedback">
                            </div>
                        </div>
                        <div class="col-sm-6 form-group">
                            <label for="password-confirmation">Confirm Password</label>
                            <input type="password" autocomplete="off" id="password-confirmation" name="password_confirmation" class="form-control" aria-describedby="validate-password_confirmation">
                            <div id="validate-password-confirmation" class="invalid-feedback">
                            </div>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="col-sm-6 form-group">
                            <label for="firstname">Firstname</label>
                            <input id="firstname" name="firstname" type="text" class="form-control" aria-describedby="validate-firstname">
                            <div id="validate-firstname" class="invalid-feedback">
                            </div>
                        </div>
                        <div class="col-sm-6 form-group">
                            <label for="lastname">Lastname</label>
                            <input id="lastname" name="lastname" type="text" class="form-control" aria-describedby="validate-lastname">
                            <div id="validate-lastname" class="invalid-feedback">
                            </div>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="col form-group">
                            <label for="img">Profile Picture</label>
                            <div class="custom-file">
                                <input type="file" class="custom-file-input" id="img" name="img" aria-describedby="img-help-block validate-img">
                                <label class="custom-file-label" for="img">Choose image file...</label>
                            </div>
                            <small id="img-help-block" class="form-text text-muted">
                                Leave it blank, unless if you want to update the profile picture.
                            </small>
                            <div id="validate-img" class="invalid-feedback">
                            </div>
                        </div>
                        <div class="col form-group">
                            <label for="phone">Phone</label>
                            <input id="phone" name="phone" type="text" class="form-control" aria-describedby="validate-phone">
                            <div id="validate-phone" class="invalid-feedback">
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="address">Address</label>
                        <textarea id="address" name="address" class="form-control" rows="2" aria-describedby="validate-address"></textarea>
                        <div id="validate-address" class="invalid-feedback">
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="col-sm-6">
                            <div class="form-row">
                                <div class="col form-group">
                                    <label for="city">City</label>
                                    <input id="city" name="city" type="text" class="form-control" aria-describedby="validate-city">
                                    <div id="validate-city" class="invalid-feedback">
                                    </div>
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="col form-group">
                                    <label for="state">State</label>
                                    <input id="state" name="state" type="text" class="form-control" aria-describedby="validate-state">
                                    <div id="validate-state" class="invalid-feedback">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-row">
                                <div class="col form-group">
                                    <label for="country">Country</label>
                                    <input id="country" name="country" type="text" class="form-control" aria-describedby="validate-country">
                                    <div id="validate-country" class="invalid-feedback">
                                    </div>
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="col form-group">
                                    <label for="zip-code">Zip Code</label>
                                    <input id="zip-code" name="zip_code" type="text" class="form-control" aria-describedby="vaidate-zip_code">
                                    <div id="validate-zip_code" class="invalid-feedback">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button class="btn btn-default" type="button" onclick="$('#popup-user-form').submit()">Submit</button>
                <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
            </div>
        </div>
    </div>
</div>

@push('ready-scripts')
$('#popup-user').on('hide.bs.modal', popUpUserHide);
$('#popup-user').on('show.bs.modal', popUpUserShow);
$('#popup-user').on('shown.bs.modal', popUpUserShown);
@endpush

@push('scripts')
<script>
    function popUpUserHide(e) {
        var $modal = $(this);
        $modal.find('.custom-file-input').off('change');
        $modal.find('#popup-user-form').off('submit');
        $modal.find('#id').val(null);
        $modal.find('#email').val(null);
        $modal.find('#role').val(null);
        $modal.find('#password').val(null);
        $modal.find('#password-confirmation').val(null);
        $modal.find('#firstname').val(null);
        $modal.find('#lastname').val(null);
        $modal.find('#phone').val(null);
        $modal.find('#address').val(null);
        $modal.find('#city').val(null);
        $modal.find('#state').val(null);
        $modal.find('#country').val(null);
        $modal.find('#zip-code').val(null);
        $modal.find('.is-invalid').removeClass('is-invalid');
    }

    function popUpUserShow(e) {
        var $origin = $(e.relatedTarget);
        var $modal = $(this);
        $modal.find('small.form-text.text-muted').hide();
        $modal.find('#create-user-title').text('Create User');
        if($origin.data('action') == 'edit') {
            var id = $origin.data('id');
            $modal.find('small.form-text.text-muted').show();
            $modal.find('#create-user-title').text('Update User');
            $.get('{{route("users.detail")}}',{id:id}).done((res) => {
                $modal.find('#id').val(res.id);
                $modal.find('#email').val(res.email);
                $modal.find('#role').val(res.role);
                $modal.find('#firstname').val(res.firstname);
                $modal.find('#lastname').val(res.lastname);
                $modal.find('#phone').val(res.phone);
                $modal.find('#address').val(res.address);
                $modal.find('#city').val(res.city);
                $modal.find('#state').val(res.state);
                $modal.find('#country').val(res.country);
                $modal.find('#zip-code').val(res.zip_code);
            });
        }
    }

    function popUpUserShown(e) {
        var $modal = $(this);

        $modal.find('.custom-file-input').on('change', (e) => {
            var fileName = e.target.files[0].name;
            $(e.target).next('.custom-file-label').html(fileName);
        });

        $modal.find('#popup-user-form').on('submit', (e) => {
            var formData = new FormData(e.target);
            $.ajax({
                method: 'POST',
                url: '{{route("users.save")}}',
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
                } else {
                    console.log(res);
                }
            }).fail((jqXHR, textStatus, errorResponse) => {
                if(jqXHR.status == 422) {
                    var errors = jqXHR.responseJSON.errors;
                    for(error in errors) {
                        $('#validate-'+error).text(errors[error]);
                        $('[name='+error+']').addClass('is-invalid');
                    }
                }
                console.log('fail',jqXHR,textStatus,errorResponse);
            });
            e.preventDefault();
        });
    }
</script>
@endpush
