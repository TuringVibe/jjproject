<?php

namespace App\Http\Controllers;

use App\Http\Requests\ValidateFinanceLabel;
use App\Http\Requests\ValidateFinanceLabelId;
use App\Http\Requests\ValidateFinanceLabelParams;
use App\Services\FinanceLabelService;

class FinanceLabelController extends Controller
{
    private $finance_label_service;

    public function __construct(FinanceLabelService $finance_label_service)
    {
        $this->finance_label_service = $finance_label_service;
    }

    public function list() {
        $this->config['title'] = "FINANCE LABEL";
        $this->config['active'] = "finance-labels.list";
        $this->config['navs'] = [
            [
                'label' => 'Finance Label'
            ]
        ];
        return view('pages.finance-label-list', $this->config);
    }

    public function data(ValidateFinanceLabelParams $request) {
        $params['name'] = $request->name;
        $result = $this->finance_label_service->get($params);
        echo json_encode($result);
    }

    public function detail(ValidateFinanceLabelId $request) {
        $result = $this->finance_label_service->detail($request->id);
        return $result;
    }

    public function save(ValidateFinanceLabel $request) {
        $result = $this->finance_label_service->save($request->validated());
        return $result;
    }

    public function delete(ValidateFinanceLabelId $request) {
        $result = $this->finance_label_service->delete($request->id);
        return $result;
    }
}
