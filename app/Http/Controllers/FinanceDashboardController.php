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
        $mutation_statistic = $this->finance_dashboard_service->mutationStatistic($request->currency ?? 'usd');
        $asset_statistic = $this->finance_dashboard_service->assetStatistic($request->currency ?? 'usd');

        $this->config['mutation_statistic'] = $mutation_statistic;
        $this->config['asset_statistic'] = $asset_statistic;
        return view('pages.finance-dashboard', $this->config);
    }

    public function dataByLabel(ValidateFinanceDashboardLabelParams $request) {
        $params = $request->validated();
        unset($params['currency']);
        $result = $this->finance_dashboard_service->mutationStatisticByLabel($request->currency ?? 'usd', $params);
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
