<div id="popup-finance-mutation-schedule" class="modal fade" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="create-finance-mutation-schedule-title" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="create-finance-mutation-schedule-title">Add Mutation Schedule</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="popup-finance-mutation-schedule-form">
                    <input type="hidden" id="id" name="id">
                    <div class="form-row">
                        <div class="col-sm-3 form-group">
                            <label for="mutation-date">Next Date <span class="text-danger">*</span></label>
                            <input type="date" id="next-mutation-date" name="next_mutation_date" class="form-control" aria-describedby="validate-next_mutation_date">
                            <div id="validate-next_mutation_date" class="invalid-feedback"></div>
                        </div>
                        <div class="col-sm-3 form-group">
                            <label for="repeat">Repeat <span class="text-danger">*</span></label>
                            <select id="repeat" name="repeat" class="form-control" aria-describedby="validate-repeat">
                                <option value="daily">Daily</option>
                                <option value="weekly">Weekly</option>
                                <option value="monthly">Monthly</option>
                                <option value="yearly">Yearly</option>
                            </select>
                            <div id="validate-repeat" class="invalid-feedback"></div>
                        </div>
                        <div class="col-sm-6 form-group">
                            <label for="name">Name <span class="text-danger">*</span></label>
                            <input type="text" id="name" name="name" class="form-control" aria-describedby="validate-name">
                            <div id="validate-name" class="invalid-feedback"></div>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="col-sm-3 form-group">
                            <label for="mode">Mode <span class="text-danger">*</span></label>
                            <select id="mode" class="form-control" name="mode" aria-describedby="validate-mode">
                                <option value="debit">Debit</option>
                                <option value="credit">Credit</option>
                                <option value="transfer">Transfer</option>
                            </select>
                            <div id="validate-mode" class="invalid-feedback"></div>
                        </div>
                        <div class="col-sm-3 form-group">
                            <label for="currency">Currency <span class="text-danger">*</span></label>
                            <select id="currency" name="currency" class="form-control" aria-describedby="validate-currency">
                                <option value="usd">USD</option>
                                <option value="cny">CNY</option>
                                <option value="idr">IDR</option>
                            </select>
                            <div id="validate-currency" class="invalid-feedback"></div>
                        </div>
                        <div class="col-sm-6 form-group">
                            <label for="nominal">Nominal <span class="text-danger">*</span></label>
                            <input type="text" name="nominal" id="nominal" class="form-control" aria-describedby="validate-nominal">
                            <div id="validate-nominal" class="invalid-feedback"></div>
                        </div>
                    </div>
                    <div id="source-of-fund" class="form-group">
                        <label for="from-wallet">Source of Fund</label>
                        <select id="from-wallet" class="form-control select2" name="from_wallet_id" aria-describedby="validate-from_wallet_id">
                            <option value="">-- No wallet --</option>
                            @foreach ($wallets as $wallet)
                            <option value="{{$wallet['id']}}">{{$wallet['name']}} [&#36; {{number_format($wallet['total_usd'],1)}}] [&yen; {{number_format($wallet['total_cny'],1)}}] [Rp {{number_format($wallet['total_idr'],1)}}]</option>
                            @endforeach
                        </select>
                    </div>
                    <div id="transfer-to" class="d-none form-group">
                        <label for="to-wallet">Transfer to</label>
                        <select id="to-wallet" class="form-control select2" name="to_wallet_id" aria-describedby="validate-to_wallet_id">
                            @foreach ($wallets as $wallet)
                            <option value="{{$wallet['id']}}">{{$wallet['name']}} [&#36; {{number_format($wallet['total_usd'],1)}}] [&yen; {{number_format($wallet['total_cny'],1)}}] [Rp {{number_format($wallet['total_idr'],1)}}]</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="attached-label-ids">Label</label>
                        <select id="attached-label-ids" class="select2 form-control" name="attached_label_ids[]" aria-describedby="validate-attached_label_ids" multiple="multiple">
                            @foreach ($labels as $label)
                                <option value="{{$label['id']}}">{{$label['name']}}</option>
                            @endforeach
                        </select>
                        <div id="validate-attached_label_ids" class="invalid-feedback"></div>
                    </div>
                    <div class="form-group">
                        <label for="scheduled-project-id">Link To Project</label>
                        <select id="scheduled-project-id" name="project_id" class="select2 form-control" aria-describedby="validate-project_id">
                            <option value="">-- No Project --</option>
                            @foreach ($projects as $project)
                                <option value="{{$project['id']}}">{{$project['name']}}</option>
                            @endforeach
                        </select>
                        <div id="validate-project_id" class="invalid-feedback"></div>
                    </div>
                    <div class="form-group">
                        <label for="notes">Notes</label>
                        <textarea id="notes" name="notes" class="form-control" aria-describedby="validate-notes"></textarea>
                        <div id="validate-notes" class="invalid-feedback"></div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button class="btn btn-default" type="button" onclick="$('#popup-finance-mutation-schedule-form').submit()">Submit</button>
                <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
            </div>
        </div>
    </div>
</div>

@push('ready-scripts')
$('#popup-finance-mutation-schedule').on('hide.bs.modal', popUpFinanceMutationScheduleHide);
$('#popup-finance-mutation-schedule').on('show.bs.modal', popUpFinanceMutationScheduleShow);
$('#popup-finance-mutation-schedule').on('shown.bs.modal', popUpFinanceMutationScheduleShown);
@endpush

@push('scripts')
<script src="{{ asset('lib/moment-with-locales.min.js') }}"></script>
<script>
    function modeChanged($modal, mode) {
        var sourceOfFundElem = $modal.find("#source-of-fund");
        var transferTo = $modal.find("#transfer-to");
        if(mode == "transfer") {
            transferTo.removeClass("d-none");
            transferTo.find('#to-wallet').prop("disabled",false);
        } else {
            transferTo.addClass("d-none");
            transferTo.find('#to-wallet').prop("disabled",true);
        }
    }

    function popUpFinanceMutationScheduleHide(e) {
        var $modal = $(this);
        $modal.find('#popup-finance-mutation-schedule-form').off('submit');
        $modal.find('#id').val(null);
        $modal.find('#next-mutation-date').val(null);
        $modal.find('#repeat').val(null);
        $modal.find('#name').val(null);
        $modal.find('#mode option[value="debit"]').prop("selected",true);
        $modal.find('#mode option[value="transfer"]').show();
        modeChanged($modal, $modal.find('#mode').val());
        $modal.find('#nominal').val(null);
        $modal.find('#currency option[value="usd"]').prop("selected",true);
        $modal.find('#currency').val(null);
        $modal.find('#attached-label-ids').val([]).trigger('change');
        $modal.find('#scheduled-project-id').val(null).trigger('change');
        $modal.find('#notes').val(null);
        $modal.find('.is-invalid').removeClass('is-invalid');
    }

    function popUpFinanceMutationScheduleShow(e) {
        var $origin = $(e.relatedTarget);
        var $modal = $(this);
        $modal.find('small.form-text.text-muted').hide();
        $modal.find('#create-finance-mutation-schedule-title').text('Add Mutation Schedule');
        $modal.find('#next-mutation-date').val(moment().add(1,'d').format('YYYY-MM-DD'));
        if($origin.data('action') == 'edit') {
            var id = $origin.data('id');
            $modal.find('small.form-text.text-muted').show();
            $modal.find('#create-finance-mutation-schedule-title').text('Update Mutation Schedule');
            $.get('{{route("finance-mutations.scheduled.edit")}}',{id: id}).done((res) => {
                $modal.find('#id').val(res.id);
                $modal.find('#next-mutation-date').val(res.next_mutation_date);
                $modal.find('#repeat').val(res.repeat);
                $modal.find('#currency').val(res.currency);
                $modal.find('#name').val(res.name);
                $modal.find('#mode').val(res.mode);
                $modal.find('#nominal').val(Intl.NumberFormat('en-US',{maximumFractionDigits: 2}).format(res.nominal));
                $modal.find('#from-wallet').val(res.from_wallet_id).trigger('change');
                if($res.mode == "transfer") $modal.find('#to-wallet').val(res.to_wallet_id).trigger('change');
                $modal.find('#scheduled-project-id').val(res.project_id).trigger('change');
                $modal.find('#notes').val(res.notes);
                var label_ids = [];
                for(label of res.attached_label_ids) {
                    label_ids.push(label);
                }
                $modal.find('#attached-label-ids').val(label_ids).trigger('change');
                modeChanged($modal, res.mode);
            });
        }
    }

    function popUpFinanceMutationScheduleShown(e) {
        var $modal = $(this);
        $modal.find('#mode').on("change", (e) => { modeChanged($modal, $(e.target).val()); });
        $modal.find('#popup-finance-mutation-schedule-form').on('submit', (e) => {
            var formData = new FormData(e.target);
            $.ajax({
                method: 'POST',
                url: '{{route("finance-mutations.scheduled.save")}}',
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
                    document.querySelector('#list-next').dispatchEvent(new CustomEvent('mutated'));
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
        });
    }
</script>
@endpush
