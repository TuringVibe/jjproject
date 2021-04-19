<?php
namespace App\Http\Controllers;

use App\Http\Requests\ValidateFinanceDashboardLabelParams;
use App\Http\Requests\ValidateFinanceDashboardParams;
use App\Http\Requests\ValidatePeriodicStatistic;
use App\Services\FinanceLabelService;
use App\Services\FinanceMutationService;

class FinanceDashboardController extends Controller {

    public function dashboard(ValidateFinanceDashboardParams $request)
    {
        $this->config['title'] = "FINANCE DASHBOARD";
        $this->config['active'] = "finance-dashboard";
        $this->config['navs'] = [
            [
                'label' => 'Finance Dashboard'
            ]
        ];
        $currencies = [
            'usd' => '&#36;',
            'cny' => '&yen;',
            'idr' => 'Rp'
        ];
        $statistic = (new FinanceMutationService())->statistic($request->currency ?? 'usd');
        $statistic['currency'] = $currencies[$statistic['currency']];
        $statistic['total_earn'] = number_format($statistic["total_earn"],2);
        $statistic['total_debit'] = number_format($statistic["total_debit"],2);
        $statistic['total_credit'] = number_format($statistic["total_credit"],2);
        $this->config['finance_statistic'] = $statistic;
        return view('pages.finance-dashboard', $this->config);
    }

    public function dataByLabel(ValidateFinanceDashboardLabelParams $request) {
        $params = $request->validated();
        unset($params['currency']);
        $result = (new FinanceLabelService())->statistic($request->currency ?? 'usd', $params);
        return $result;
    }

    public function periodicStatistic(ValidatePeriodicStatistic $request) {
        $result = (new FinanceMutationService())->periodicStatistic($request->date_from,$request->date_to,$request->periode,$request->currency ?? 'usd');
        return $result;
    }
}
