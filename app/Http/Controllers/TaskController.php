<?php

namespace App\Http\Controllers;

use App\Http\Requests\ValidateTask;
use App\Http\Requests\ValidateTaskId;
use App\Http\Requests\ValidateTaskMove;
use App\Http\Requests\ValidateTaskParams;
use App\Services\ProjectLabelService;
use App\Services\ProjectService;
use App\Services\TaskService;
use App\Services\UserService;

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
        $result = $this->task_service->get($params);
        echo json_encode($result);
    }

    public function cards(ValidateTaskParams $request) {
        $result = $this->task_service->getCards($request->project_id);
        return $result->toArray();
    }

    public function card(ValidateTaskId $request) {
        $result = $this->task_service->getCard($request->id);
        return $result->toArray();
    }

    public function edit(ValidateTaskId $request) {
        $result = $this->task_service->detail($request->id);
        return $result;
    }

    public function detail(ValidateTaskId $request) {
        $task = $this->task_service->detail($request->id);
        $task->loadMissing('comments','files','subtasks');
        return $task;
    }

    public function save(ValidateTask $request) {
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
        $result = $this->task_service->delete($request->id);
        return $result;
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
