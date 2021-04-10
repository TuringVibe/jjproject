<?php

namespace App\Http\Controllers;

use App\Http\Requests\ValidateSubtask;
use App\Http\Requests\ValidateSubtaskBulkInsert;
use App\Http\Requests\ValidateSubtaskId;
use App\Http\Requests\ValidateSubtaskParams;
use App\Services\SubtaskService;

class SubtaskController extends Controller
{
    private $subtask_service;

    public function __construct(SubtaskService $subtask_service)
    {
        $this->subtask_service = $subtask_service;
    }

    public function data(ValidateSubtaskParams $request) {
        $result = $this->subtask_service->get($request->task_id);
        echo json_encode($result);
    }

    public function edit(ValidateSubtaskId $request) {
        $result = $this->subtask_service->detail($request->id);
        if($result) {
            return [
                'status' => true,
                'message' => 'Data retrieved successfully',
                'data' => $result
            ];
        }
        return [
            'status' => false,
            'message' => 'Failed to retrieve data',
            'errors' => []
        ];
    }

    public function save(ValidateSubtask $request) {
        $attr = $request->validated();
        unset($attr['task_id']);
        $result = $this->subtask_service->save($request->task_id, $attr);
        if(isset($result)) return [
            'status' => true,
            'message' => 'Data saved successfully',
            'data' => $result
        ];
        return [
            'status' => false,
            'message' => 'Failed to save data',
            'errors' => []
        ];
    }

    public function bulkInsert(ValidateSubtaskBulkInsert $request) {
        $attr = $request->validated();
        unset($attr['task_id']);
        $result = $this->subtask_service->bulkInsert($request->task_id, $attr);
        if(isset($result)) return [
            'status' => true,
            'message' => 'Data saved successfully',
            'data' => $result
        ];
        return [
            'status' => false,
            'message' => 'Failed to save data',
            'errors' => []
        ];
    }

    public function delete(ValidateSubtaskId $request) {
        $result = $this->subtask_service->delete($request->id);
        if($result) {
            return [
                'status' => true,
                'message' => 'Data deleted successfully',
            ];
        }
        return [
            'status' => false,
            'message' => 'Failed to delete data'
        ];
    }
}
