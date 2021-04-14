<?php

namespace App\Http\Controllers;

use App\Http\Requests\ValidateFinanceMutation;
use App\Http\Requests\ValidateFinanceMutationId;
use App\Http\Requests\ValidateFinanceMutationParams;
use App\Services\FinanceLabelService;
use App\Services\FinanceMutationService;
use App\Services\ProjectService;

class FinanceMutationController extends Controller
{
    private $finance_mutation_service;

    public function __construct(FinanceMutationService $finance_mutation_service)
    {
        $this->finance_mutation_service = $finance_mutation_service;
    }

    public function list() {
        $this->config['title'] = "FINANCE MUTATION";
        $this->config['active'] = "finance-mutations.list";
        $this->config['navs'] = [
            [
                'label' => 'Finance Mutation'
            ]
        ];
        $this->config['labels'] = (new FinanceLabelService())->get();
        $this->config['projects'] = (new ProjectService())->get();
        return view('pages.finance-mutation', $this->config);
    }

    public function data(ValidateFinanceMutationParams $request) {
        $result = $this->finance_mutation_service->get($request->validated());
        echo json_encode($result->toArray());
    }

    public function edit(ValidateFinanceMutationId $request) {
        $result = $this->finance_mutation_service->detail($request->id);
        return $result;
    }

    public function save(ValidateFinanceMutation $request) {
        $result = $this->finance_mutation_service->save($request->validated());
        if($result) {
            return [
                'status' => true,
                'message' => 'Data saved succesfully',
                'data' => $result
            ];
        }
        return [
            'status' => false,
            'message' => 'Failed to saved data',
        ];
    }

    public function delete(ValidateFinanceMutationId $request) {
        $result = $this->finance_mutation_service->delete($request->id);
        return $result;
    }

}
