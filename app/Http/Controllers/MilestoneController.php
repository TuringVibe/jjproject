<?php

namespace App\Http\Controllers;

use App\Http\Requests\ValidateMilestone;
use App\Http\Requests\ValidateMilestoneId;
use App\Http\Requests\ValidateMilestoneParams;
use App\Services\MilestoneService;

class MilestoneController extends Controller
{
    private $milestone_service;

    public function __construct(MilestoneService $milestone_service)
    {
        $this->milestone_service = $milestone_service;
    }

    public function data(ValidateMilestoneParams $request) {
        $result = $this->milestone_service->get($request->project_id);
        echo json_encode($result);
    }

    public function edit(ValidateMilestoneId $request) {
        $result = $this->milestone_service->detail($request->id);
        return $result;
    }

    public function save(ValidateMilestone $request) {
        $attr = $request->validated();
        unset($attr['project_id']);
        $result = $this->milestone_service->save($request->project_id, $attr);
        return $result;
    }

    public function delete(ValidateMilestoneId $request) {
        $result = $this->milestone_service->delete($request->id);
        return $result;
    }
}
