<?php

namespace App\Http\Controllers;

use App\Http\Requests\ValidateMilestone;
use App\Http\Requests\ValidateMilestoneId;
use App\Http\Requests\ValidateMilestoneParams;
use App\Models\Milestone;
use App\Services\MilestoneService;
use Illuminate\Support\Facades\Gate;

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
        if($request->user()->can('update',Milestone::find($request->id)))
            $result = $this->milestone_service->detail($request->id);
        else $result = null;
        return $result;
    }

    public function save(ValidateMilestone $request) {
        if($request->id == null
        && !($response = Gate::inspect('create',App\Models\Milestone::class))->allowed()) {
            return response()->json([
                'status' => false,
                'message' => $response->message()
            ],403);
        }
        if($request->id != null
        && !($response = Gate::inspect('update',Milestone::find($request->id)))->allowed()) {
            return response()->json([
                'status' => false,
                'message' => $response->message()
            ],403);
        }

        $attr = $request->validated();
        unset($attr['project_id']);
        $result = $this->milestone_service->save($request->project_id, $attr);
        if($result) {
            return response()->json([
                'status' => true,
                'message' => __('response.save_succeed'),
                'data' => $result
            ]);
        }
        return response()->json([
            'status' => false,
            'message' => __('response.save_failed')
        ]);
    }

    public function delete(ValidateMilestoneId $request) {
        $response = Gate::inspect('delete',Milestone::find($request->id));
        if(!$response->allowed()) {
            return response()->json([
                'status' => false,
                'message' => $response->message()
            ],403);
        }
        $result = $this->milestone_service->delete($request->id);
        if($result) {
            return response()->json([
                'status' => true,
                'message' => __('response.delete_succeed')
            ]);
        }
        return response()->json([
            'status' => false,
            'message' => __('response.delete_failed')
        ]);
    }
}
