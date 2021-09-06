<?php

namespace App\Http\Controllers;

use App\Http\Requests\ValidateFinanceMutationSchedule;
use App\Http\Requests\ValidateFinanceMutationScheduleId;
use App\Http\Requests\ValidateFinanceMutationScheduleParams;
use App\Services\FinanceMutationScheduleService;

class FinanceMutationScheduleController extends Controller
{
    private $finance_mutation_schedule_service;

    public function __construct(FinanceMutationScheduleService $finance_mutation_schedule_service)
    {
        $this->finance_mutation_schedule_service = $finance_mutation_schedule_service;
    }

    public function data(ValidateFinanceMutationScheduleParams $request) {
        $result = $this->finance_mutation_schedule_service->get($request->validated());
        return response()->json($result);
    }

    public function edit(ValidateFinanceMutationScheduleId $request) {
        $result = $this->finance_mutation_schedule_service->detail($request->id);
        return $result;
    }

    public function save(ValidateFinanceMutationSchedule $request) {
        $result = $this->finance_mutation_schedule_service->save($request->validated());
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

    public function delete(ValidateFinanceMutationScheduleId $request) {
        $result = $this->finance_mutation_schedule_service->delete($request->id);
        return $result;
    }

}
