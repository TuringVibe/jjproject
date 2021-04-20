<?php

namespace App\Http\Controllers;

use App\Http\Requests\ValidateTask;
use App\Http\Requests\ValidateTaskId;
use App\Http\Requests\ValidateTaskMove;
use App\Http\Requests\ValidateTaskParams;
use App\Models\File;
use App\Models\Task;
use App\Models\TaskComment;
use App\Services\ProjectLabelService;
use App\Services\ProjectService;
use App\Services\TaskService;
use App\Services\UserService;
use Illuminate\Support\Facades\Gate;

class TaskController extends Controller
{
    private $task_service;

    public function __construct(TaskService $task_service)
    {
        $this->task_service = $task_service;
    }

    public function list()
    {
        $this->config['title'] = "TASKS";
        $this->config['active'] = "tasks.list";
        $this->config['navs'] = [
            [
                'label' => 'Tasks'
            ]
        ];
        $this->config['projects'] = (new ProjectService())->get();
        $this->config['project_labels'] = (new ProjectLabelService())->get();
        $this->config['users'] = (new UserService())->get();
        return view('pages.task-list', $this->config);
    }

    public function data(ValidateTaskParams $request) {
        $params = $request->validated();
        if(!$request->user()->can('viewAny',App\Models\Task::class)) {
            $params['user_id'] = $request->user()->id;
        }
        $result = $this->task_service->get($params);
        echo json_encode($result);
    }

    public function cards(ValidateTaskParams $request) {
        $result = $this->task_service->getCards($request->project_id);
        $result = $result->map(function($item,$key) use($request){
            $task = Task::find($item['id']);
            $item['can_update'] = $request->user()->can('update',$task);
            $item['can_delete'] = $request->user()->can('delete',$task);
            return $item;
        });
        return $result;
    }

    public function card(ValidateTaskId $request) {
        $result = $this->task_service->getCard($request->id);
        $task = Task::find($result->id);
        $result->can_update = $request->user()->can('update',$task);
        $result->can_delete = $request->user()->can('delete',$task);
        $result->comments = $result->comments->map(function($item,$key) use($request){
            $task_comment = TaskComment::find($item['id']);
            $item['can_update'] = $request->user()->can('update',$task_comment);
            $item['can_delete'] = $request->user()->can('delete',$task_comment);
            return $item;
        });
        $result->files = $result->files->map(function($item,$key) use($request, $task){
            $item['can_delete'] = $request->user()->can('deleteFile',[$task, $item['id']]);
            return $item;
        });
        return $result->toArray();
    }

    public function edit(ValidateTaskId $request) {
        $result = null;
        if($request->user()->can('update',Task::find($request->id))) {
            $result = $this->task_service->detail($request->id);
        }
        return $result;
    }

    public function detail(ValidateTaskId $request) {
        $task = $this->task_service->detail($request->id);
        $task->loadMissing('comments','files','subtasks');
        return $task;
    }

    public function save(ValidateTask $request) {
        if($request->id == null
        && !($response = Gate::inspect('create',App\Models\Task::class))->allowed()) {
            return response()->json([
                'status' => false,
                'message' => $response->message()
            ],403);
        }
        if($request->id != null
        && !($response = Gate::inspect('update',Task::find($request->id)))->allowed()) {
            return response()->json([
                'status' => false,
                'message' => $response->message()
            ],403);
        }

        $result = $this->task_service->save($request->validated());
        if($result) return response()->json([
            'status' => true,
            'message' => 'Saved data successfully',
            'data' => $result
        ]);
        return response()->json([
            'status' => false,
            'message' => 'Failed to save data'
        ]);
    }

    public function delete(ValidateTaskId $request) {
        $task = Task::find($request->id);
        $response = Gate::inspect('delete',$task);
        if(!$response->allowed())
            return response()->json(['status' => false, 'message' => $response->message()], 403);

        $result = $this->task_service->delete($request->id);
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

    public function move(ValidateTaskMove $request) {
        $result = $this->task_service->move($request->project_id,$request->task_id,$request->dest_status,$request->dest_order);
        if($result) return response()->json([
            'status' => true,
            'message' => 'Moved data successfully',
            'data' => $result
        ]);
        return response()->json([
            'status' => false,
            'message' => 'Failed to move data'
        ]);
    }
}
