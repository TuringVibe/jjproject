<?php

namespace App\Http\Controllers;

use App\Http\Requests\ValidateProject;
use App\Http\Requests\ValidateProjectId;
use App\Http\Requests\ValidateProjectParams;
use App\Services\MilestoneService;
use App\Services\ProjectLabelService;
use App\Services\ProjectService;
use App\Services\TaskService;
use App\Services\UserService;
use Carbon\Carbon;

class ProjectController extends Controller
{
    private $project_service;
    private $task_service;

    public function __construct(
        ProjectService $project_service,
        TaskService $task_service
    )
    {
        $this->project_service = $project_service;
        $this->task_service = $task_service;
    }

    public function list()
    {
        $this->config['title'] = "PROJECTS";
        $this->config['active'] = "projects.list";
        $this->config['navs'] = [
            [
                'label' => 'Projects'
            ]
        ];
        $this->config['users'] = (new UserService())->get();
        $this->config['labels'] = (new ProjectLabelService())->get();
        return view('pages.project-list', $this->config);
    }

    public function data(ValidateProjectParams $request) {
        $params['status'] = $request->status;
        $params['user_id'] = $request->user_id;
        $params['project_label_id'] = $request->project_label_id;
        $params['name'] = $request->name;
        $result = $this->project_service->get($params);
        echo json_encode($result);
    }

    public function edit(ValidateProjectId $request) {
        $result = $this->project_service->detail($request->project_id);
        return $result;
    }

    public function save(ValidateProject $request) {
        $result = $this->project_service->save($request->validated());
        return $result;
    }

    public function delete(ValidateProjectId $request) {
        $result = $this->project_service->delete($request->id);
        return $result;
    }

    public function detail(ValidateProjectId $request) {
        $this->config['title'] = "PROJECT DETAIL";
        $this->config['active'] = "projects.list";
        $result = $this->project_service->detail($request->project_id)->toArray();
        $this->config['detail'] = $result;
        $this->config['detail']['startdate'] = Carbon::parse($result['startdate'])->format('d F Y');
        $this->config['detail']['enddate'] = Carbon::parse($result['enddate'])->format('d F Y');
        $this->config['detail']['days_left'] = Carbon::now()->diffInDays($result['enddate']);
        $this->config['tasks_statistic'] = $this->task_service->statistic($request->project_id);
        $this->config['navs'] = [
            [
                'label' => 'Projects',
                'link' => route('projects.list')
            ],
            [
                'label' => $result['name']
            ]
        ];
        return view('pages.project-detail', $this->config);
    }

    public function board(ValidateProjectId $request) {
        $project = (new ProjectService())->detail($request->project_id);
        $this->config['title'] = "PROJECT TASKS";
        $this->config['active'] = "projects.list";
        $this->config['milestones'] = (new MilestoneService())->get($request->project_id);
        $this->config['users'] = (new UserService())->get();
        $this->config['navs'] = [
            [
                'label' => 'Projects',
                'link' => route('projects.list')
            ],
            [
                'label' => $project->name,
                'link' => route('projects.detail',['project_id' => $request->project_id])
            ],
            [
                'label' => 'Board'
            ]
        ];
        return view('pages.board', $this->config);
    }
}
