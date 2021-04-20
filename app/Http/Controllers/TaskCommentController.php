<?php

namespace App\Http\Controllers;

use App\Http\Requests\ValidateTaskComment;
use App\Http\Requests\ValidateTaskCommentId;
use App\Http\Requests\ValidateTaskCommentParams;
use App\Models\TaskComment;
use App\Services\TaskCommentService;
use Illuminate\Support\Facades\Gate;

class TaskCommentController extends Controller
{
    private $task_comment_service;

    public function __construct(TaskCommentService $task_comment_service)
    {
        $this->task_comment_service = $task_comment_service;
    }

    public function data(ValidateTaskCommentParams $request) {
        $result = $this->task_comment_service->get($request->task_id);
        $result = $result->map(function($item,$key) use($request){
            $task_comment = TaskComment::find($item['id']);
            $item['can_update'] = $request->user()->can('update',$task_comment);
            $item['can_delete'] = $request->user()->can('delete',$task_comment);
            return $item;
        });
        return response()->json($result);
    }

    public function edit(ValidateTaskCommentId $request) {
        $result = null;
        if($request->user()->can('update',TaskComment::find($request->id))) {
            $result = $this->task_comment_service->detail($request->id);
        } else {
            return response()->json([
                'status' => false,
                'message' => __('response.not_authorized'),
                'data' => $result
            ],403);
        }

        if($result) {
            return [
                'status' => true,
                'message' => __('response.retrieve_succeed'),
                'data' => $result
            ];
        }
        return [
            'status' => false,
            'message' => __('response.retrieve_failed'),
        ];
    }

    public function save(ValidateTaskComment $request) {
        if($request->id == null
        && !($response = Gate::inspect('create',App\Models\TaskComment::class))->allowed()) {
            return response()->json([
                'status' => false,
                'message' => $response->message()
            ],403);
        }
        if($request->id != null
        && !($response = Gate::inspect('update',TaskComment::find($request->id)))->allowed()) {
            return response()->json([
                'status' => false,
                'message' => $response->message()
            ],403);
        }

        $attr = $request->validated();
        unset($attr['task_id']);
        $result = $this->task_comment_service->save($request->task_id, $attr);

        if(isset($result)) {
            $result->can_update = $request->user()->can('update',$result);
            $result->can_delete = $request->user()->can('delete',$result);
            return [
                'status' => true,
                'message' => __('response.save_succeed'),
                'data' => $result
            ];
        }
        return [
            'status' => false,
            'message' => __('response.save_failed'),
            'errors' => []
        ];
    }

    public function delete(ValidateTaskCommentId $request) {
        $response = Gate::inspect('delete',TaskComment::find($request->id));
        if(!$response->allowed()) {
            return response()->json([
                'status' => false,
                'message' => $response->message()
            ],403);
        }
        $result = $this->task_comment_service->delete($request->id);
        if($result) {
            return [
                'status' => true,
                'message' => __('response.delete_succeed'),
            ];
        }
        return [
            'status' => false,
            'message' => __('response.delete_failed')
        ];
    }
}
