<?php
namespace App\Http\Controllers;

use App\Http\Requests\ValidateFinanceDashboardLabelParams;
use App\Http\Requests\ValidateFinanceDashboardParams;
use App\Http\Requests\ValidatePeriodicStatistic;
use App\Services\FinanceDashboardService;
use App\Services\FinanceLabelService;

class FinanceDashboardController extends Controller {

    private $finance_dashboard_service;
    public function __construct(FinanceDashboardService $finance_dashboard_service)
    {
        $this->finance_dashboard_service = $finance_dashboard_service;
    }

    public function dashboard(ValidateFinanceDashboardParams $request)
    {
        $this->config['title'] = "FINANCE DASHBOARD";
        $this->config['active'] = "finance-dashboard";
        $this->config['navs'] = [
            [
                'label' => 'Finance Dashboard'
            ]
        ];
        $this->config['mutation_statistic'] = $this->finance_dashboard_service->mutationStatistic($request->currency ?? 'usd');
        $this->config['asset_statistic'] = $this->finance_dashboard_service->assetStatistic($request->currency ?? 'usd');
        return view('pages.finance-dashboard', $this->config);
    }

    public function dataByLabel(ValidateFinanceDashboardLabelParams $request) {
        $params = $request->validated();
        $currency = $params['currency'];
        unset($params['currency']);
        if($params['name'] === null) unset($params['name']);
        $result = $this->finance_dashboard_service->mutationStatisticByLabel($currency ?? 'usd', $params);
        return $result;
    }

    public function periodicStatistic(ValidatePeriodicStatistic $request) {
        $result = $this->finance_dashboard_service->mutationPeriodicStatistic(
            $request->date_from, $request->date_to, $request->periode, $request->currency ?? 'usd'
        );
        return $result;
    }

    public function assetPeriodicStatistic(ValidatePeriodicStatistic $request) {
        $result = $this->finance_dashboard_service->assetPeriodicStatistic(
            $request->date_from, $request->date_to, $request->periode, $request->currency ?? 'usd'
        );
        return $result;
    }
}
