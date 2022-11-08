@extends('layouts.master')

@push('head')
    <link rel="stylesheet" href="{{ asset('lib/DataTables/datatables.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/content.css') }}">
    <style>
        .label-list {
            display: inline-block;
            padding: .3rem;
            border-radius: 5px;
            color: white;
        }
        .buy-price {
            font-size: 1rem;
        }
        .last-price{
            font-size: 1rem;
        }
        .last-change{
            margin-top: 10px;
            font-size: .8rem;
        }
    </style>
@endpush

@section('content')
    <div class="content container-fluid">
        @include('components.content-header')
        <div class="row mb-3">
            <div class="col-sm-3">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Currency</h5>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="currency" id="currency-usd" value="usd">
                            <label class="form-check-label" for="currency-usd">USD</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="currency" id="currency-cny" value="cny">
                            <label class="form-check-label" for="currency-yuan">CNY</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="currency" id="currency-idr" value="idr">
                            <label class="form-check-label" for="currency-idr">IDR</label>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-3">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Total Earn</h5>
                        <p class="h5">{!! $mutation_statistic['currency'] !!} {{$mutation_statistic['total_earning']}}</p>
                    </div>
                </div>
            </div>
            <div class="col-sm-3">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Total Debit</h5>
                        <p class="h5">{!! $mutation_statistic['currency'] !!} {{$mutation_statistic["total_debit"]}}</p>
                    </div>
                </div>
            </div>
            <div class="col-sm-3">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Total Credit</h5>
                        <p class="h5">{!! $mutation_statistic['currency'] !!} {{$mutation_statistic["total_credit"]}}</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="row flex-wrap">
            @foreach ($asset_statistic as $statistic)
            <div class="col-sm-3 mb-3">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <h5 class="card-title">{{$statistic['name']}}</h5>
                            <span class="caption {{$statistic['percentage'] > 0 ? 'text-success' : 'text-danger'}}">({{$statistic["percentage"]}} %)</span>
                        </div>
                        <div class="buy-price">Buy : {!! $statistic['currency'] !!} {{$statistic["buy_price"]}}</div>
                        <div class="last-price">Latest : {!! $statistic['currency'] !!} {{$statistic["latest_price"]}}</div>
                        <div class="last-change">Last change : {{ $statistic["latest_price_change_datetime"] }}</div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        <div class="row mb-3">
            <div class="col">
                <div class="card">
                    <h4 class="card-header">Mutation Statistic</h4>
                    <div class="card-body">
                        <div class="form-row">
                            <div class="form-group col-auto">
                                <label for="date-from">From</label>
                                <input type="date" class="form-control" id="date-from" value="">
                            </div>
                            <div class="form-group col-auto">
                                <label for="date-to">To</label>
                                <input type="date" class="form-control" id="date-to" value="">
                            </div>
                            <div class="form-group col-auto">
                                <label for="periode">Periode</label>
                                <select class="form-control" id="periode">
                                    <option value="weekly">Weekly</option>
                                    <option value="monthly">Monthly</option>
                                </select>
                            </div>
                            <div class="form-group col-auto align-self-end">
                                <button id="show" type="button" class="btn btn-default">Show</button>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-sm-3">
                                <div class="card">
                                    <div class="card-body">
                                        <h5 class="card-title">Total Earn</h5>
                                        <p id="mutation-total-earn" class="h5"></p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="card">
                                    <div class="card-body">
                                        <h5 class="card-title">Total Debit</h5>
                                        <p id="mutation-total-debit" class="h5"></p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="card">
                                    <div class="card-body">
                                        <h5 class="card-title">Total Credit</h5>
                                        <p id="mutation-total-credit" class="h5"></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div>
                            <canvas id="chart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mb-3">
            <div class="col">
                <div class="card">
                    <h4 class="card-header">Asset Statistic</h4>
                    <div class="card-body">
                        <div class="form-row">
                            <div class="form-group col-auto">
                                <label for="asset-date-from">From</label>
                                <input type="date" class="form-control" id="asset-date-from" value="">
                            </div>
                            <div class="form-group col-auto">
                                <label for="asset-date-to">To</label>
                                <input type="date" class="form-control" id="asset-date-to" value="">
                            </div>
                            <div class="form-group col-auto">
                                <label for="asset-periode">Periode</label>
                                <select class="form-control" id="asset-periode">
                                    <option value="weekly">Weekly</option>
                                    <option value="monthly">Monthly</option>
                                </select>
                            </div>
                            <div class="form-group col-auto align-self-end">
                                <button id="show-asset" type="button" class="btn btn-default">Show</button>
                            </div>
                        </div>
                        <div>
                            <canvas id="chart-asset"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col">
                <div class="card">
                    <h4 class="card-header">Data by Label</h4>
                    <div class="card-body">
                        <div class="form-row">
                            <div class="form-group col-auto">
                                <label for="filter-name">Name</label>
                                <input type="text" class="form-control" id="filter-name">
                            </div>
                            <div class="form-group col-auto align-self-end">
                                <button id="filter-button" type="button" class="btn btn-default">Filter</button>
                            </div>
                        </div>
                        <table id="list" class="table table-striped table-bordered">
                            <thead>
                                <tr>
                                    <th>Label</th>
                                    <th>Debit</th>
                                    <th>Credit</th>
                                    <th>Total</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('ready-scripts')
    $('.last-change').each((index, elem) => {
        let text = $(elem).text().split(' : ');
        let lastChanged = moment(text[1]).format('LL LTS');
        $(elem).text(text[0] + ' : ' + lastChanged);
    });
    $('#currency-'+currency).prop("checked",true);
    $('[name=currency]').on('click',(e) => {
        const currency = $(e.target).val();
        const dateFrom = $('#date-from').val();
        const dateTo = $('#date-to').val();
        const assetDateFrom = $('#asset-date-from').val();
        const assetDateTo = $('#asset-date-to').val();

        const url = new URL(window.location.href);
        const params = new URLSearchParams({
            currency: currency,
            date_from: dateFrom,
            date_to: dateTo,
            asset_date_from: assetDateFrom,
            asset_date_to: assetDateTo
        }).toString();
        window.location.href = url.origin + url.pathname + '?' + params;
    });

    $('#filter-name').on('keyup',(e) => { if(e.key == "Enter") $('#filter-button').click();});
    $('#filter-button').on('click',(e) => {
        document.querySelector('#list').dispatchEvent(new CustomEvent('mutated'));
    });

    $('#show').on('click',(e) => {
        updateChart();
    });

    $('#show-asset').on('click',(e) => {
        updateChartAsset();
    });

    var table = $('#list').DataTable({
        order: [],
        paging: false,
        searching: false,
        processing: true,
        ajax: {
            url: '{{ route('finance-dashboard.data-by-label') }}',
            dataSrc: '',
            data: function(d) {
                d.currency = currency,
                d.name = $('#filter-name').val();
            }
        },
        columns: [
            {
                data: 'label',
                render: (data,type,row,meta) => {
                    return '<span class="label-list" style="background-color:'+ (data.color ?? 'none') +'">'+data.name+'</span>';
                }
            },
            {
                data: 'debit',
                render: (data,type,row,meta) => {
                    return currencies[row.currency]+" "+new Intl.NumberFormat().format(new Number(data).toFixed(2));
                }
            },
            {
                data: 'credit',
                render: (data,type,row,meta) => {
                    return currencies[row.currency]+" "+new Intl.NumberFormat().format(new Number(data).toFixed(2));
                }
            },
            {
                data: 'total',
                render: (data,type,row,meta) => {
                    return currencies[row.currency]+" "+new Intl.NumberFormat().format(new Number(data).toFixed(2));
                }
            }
        ]
    });

    $('#list').on('mutated', (e) => {
        table.ajax.reload();
    });

    updateChart();
    updateChartAsset();
@endpush

@push('scripts')
    <script src="{{ asset('lib/moment-with-locales.min.js') }}"></script>
    <script src="{{asset('lib/DataTables/datatables.min.js')}}"></script>
    <script src="{{asset('lib/chart.js-3.0.1/chart.min.js')}}"></script>
    <script>
        var currencies = {
            'usd': '&#36;',
            'cny': '&yen;',
            'idr': 'Rp'
        };
        const defaultDateFrom = moment().startOf('year').format('YYYY-MM-DD');
        const defaultDateTo = moment().format('YYYY-MM-DD');
        const initialDateFrom = getQueryVariable('date_from');
        const initialDateTo = getQueryVariable('date_to');
        const initialAssetDateFrom = getQueryVariable('asset_date_from');
        const initialAssetDateTo = getQueryVariable('asset_date_to');

        $('#date-from').val(initialDateFrom !== false ? initialDateFrom : defaultDateFrom);
        $('#date-to').val(initialDateTo !== false ? initialDateTo : defaultDateTo);
        $('#asset-date-from').val(initialAssetDateFrom !== false ? initialAssetDateFrom : defaultDateFrom);
        $('#asset-date-to').val(initialAssetDateTo !== false ? initialAssetDateTo : defaultDateTo);

        var currency = getQueryVariable('currency');
        currency = currency == false ? 'usd' : currency;

        var chart = null;
        if($('#chart').length) {
            const ctx = $('#chart');
            chart = new Chart(ctx,{
                type: 'bar',
                data: {
                    datasets: [
                        {
                            label: 'Earning',
                            data: [],
                            type: 'line',
                            borderColor: '#007bff',
                            backgroundColor: 'rgb(0, 123, 255)'
                        },
                        {
                            label: 'Debit',
                            data: [],
                            backgroundColor: 'rgba(18, 199, 63, .5)',
                            borderColor: 'rgb(18, 199, 63)',
                            borderWidth: 2
                        },
                        {
                            label: 'Credit',
                            data: [],
                            backgroundColor: 'rgba(237, 50, 50, .5)',
                            borderColor: 'rgb(237, 50, 50, 1)',
                            borderWidth: 2
                        }
                    ],
                    labels: []
                },
                options: {
                    plugins: {
                        tooltip: {
                            position: 'nearest'
                        }
                    }
                }
            });
        }

        var chartAsset = null;
        if($('#chart-asset').length) {
            const ctxAsset = $('#chart-asset');
            chartAsset = new Chart(ctxAsset,{
                type: 'line',
                options: {
                    plugins: {
                        tooltip: {
                            position: 'nearest'
                        }
                    }
                }
            });
        }

        function updateChart() {
            const dateFrom = $('#date-from').val();
            const dateTo = $('#date-to').val();
            const periode = $('#periode').val();
            const $mutationTotalEarn = $('#mutation-total-earn');
            const $mutationTotalDebit = $('#mutation-total-debit');
            const $mutationTotalCredit = $('#mutation-total-credit');

            $.get(@json(route("finance-dashboard.periodic-statistic")),{
                currency: currency,
                date_range: dateFrom + " - " + dateTo,
                periode: periode
            }).done(function(res){
                let totalEarn = 0;
                let totalDebit = 0;
                let totalCredit = 0;

                chart.data.labels = [];
                chart.data.datasets.forEach((dataset) => {
                    dataset.data = [];
                });

                for(data of res) {
                    chart.data.labels.push(data.label);
                    chart.data.datasets[0].data.push(data.total_earning);
                    chart.data.datasets[1].data.push(data.total_debit);
                    chart.data.datasets[2].data.push(data.total_credit);
                    totalDebit += data.total_debit;
                    totalCredit += data.total_credit;
                }
                totalCredit = totalCredit > 0 ? totalCredit * -1 : 0;
                totalEarn = totalDebit + totalCredit;

                $mutationTotalEarn.html(currencies[currency] + ' ' + Intl.NumberFormat("en-US", {maximumFractionDigits: 2}).format(totalEarn));
                $mutationTotalDebit.html(currencies[currency] + ' ' + Intl.NumberFormat("en-US", {maximumFractionDigits: 2}).format(totalDebit));
                $mutationTotalCredit.html(currencies[currency] + ' ' + Intl.NumberFormat("en-US", {maximumFractionDigits: 2}).format(totalCredit));

                chart.update();
            });
        }

        function updateChartAsset() {
            const dateFrom = $('#asset-date-from').val();
            const dateTo = $('#asset-date-to').val();
            const periode = $('#asset-periode').val();
            $.get(@json(route("finance-dashboard.asset-periodic-statistic")),{
                currency: currency,
                date_range: dateFrom + " - " + dateTo,
                periode: periode
            }).done(function(res){
                chartAsset.data.labels = [];
                chartAsset.data.datasets = [];
                chartAsset.data.labels = res.labels;
                for(idx in res.datasets) {
                    const r = Math.floor(Math.random() * 255);
                    const g = Math.floor(Math.random() * 255);
                    const b = Math.floor(Math.random() * 255);
                    chartAsset.data.datasets.push(
                        {
                            label: res.datasets[idx],
                            backgroundColor: 'rgb('+r+','+g+','+b+')'
                        }
                    );
                }
                for(idx in res.data) {
                    chartAsset.data.datasets[idx].data = res.data[idx];
                }
                chartAsset.update();
            });
        }
    </script>
@endpush
