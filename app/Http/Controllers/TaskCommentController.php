<?php

namespace App\Http\Controllers;

use App\Http\Requests\ValidateTaskComment;
use App\Http\Requests\ValidateTaskCommentId;
use App\Http\Requests\ValidateTaskCommentParams;
use App\Services\TaskCommentService;

class TaskCommentController extends Controller
{
    private $task_comment_service;

    public function __construct(TaskCommentService $task_comment_service)
    {
        $this->task_comment_service = $task_comment_service;
    }

    public function data(ValidateTaskCommentParams $request) {
        $result = $this->task_comment_service->get($request->task_id);
        echo json_encode($result->toArray());
    }

    public function edit(ValidateTaskCommentId $request) {
        $result = $this->task_comment_service->detail($request->id);
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

    public function save(ValidateTaskComment $request) {
        $attr = $request->validated();
        unset($attr['task_id']);
        $result = $this->task_comment_service->save($request->task_id, $attr);
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

    public function delete(ValidateTaskCommentId $request) {
        $result = $this->task_comment_service->delete($request->id);
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
