<?php

namespace App\Http\Controllers;

use App\Http\Requests\ValidateProject;
use App\Http\Requests\ValidateProjectId;
use App\Http\Requests\ValidateProjectParams;
use App\Models\Project;
use App\Services\MilestoneService;
use App\Services\ProjectLabelService;
use App\Services\ProjectService;
use App\Services\TaskService;
use App\Services\UserService;
use Carbon\Carbon;
use Illuminate\Auth\Access\Response;
use Illuminate\Support\Facades\Gate;

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
        $params['project_label_id'] = $request->project_label_id;
        $params['name'] = $request->name;
        if(!$request->user()->can('viewAny',Project::class)) {
            $params['user_id'] = $request->user()->id;
        } else {
            $params['user_id'] = $request->user_id;
        }
        $result = $this->project_service->get($params);
        return $result->toArray();
    }

    public function edit(ValidateProjectId $request) {
        $this->authorize('update',Project::find($request->id));
        $result = $this->project_service->detail($request->id);
        return $result;
    }

    public function save(ValidateProject $request) {
        if($request->id == null
        && !($response = Gate::inspect('create',App\Models\Project::class))->allowed()) {
            return response()->json([
                'status' => false,
                'message' => $response->message()
            ],403);
        }
        if($request->id != null
        && !($response = Gate::inspect('update',Project::find($request->id)))->allowed()) {
            return response()->json([
                'status' => false,
                'message' => $response->message()
            ],403);
        }

        $result = $this->project_service->save($request->validated());
        return $result;
    }

    public function delete(ValidateProjectId $request) {
        $this->authorize('delete',Project::find($request->id));
        $result = $this->project_service->delete($request->id);
        return $result;
    }

    public function detail(ValidateProjectId $request) {
        $project = Project::find($request->id);
        $response = Gate::inspect('view',$project);
        if(!$response->allowed()) return redirect()->route('projects.list')->with('error',$response->message());

        $this->config['title'] = "PROJECT DETAIL";
        $this->config['active'] = "projects.list";
        $result = $this->project_service->detail($request->id)->toArray();
        $this->config['detail'] = $result;
        $this->config['detail']['startdate'] = Carbon::parse($result['startdate'])->format('d F Y');
        $this->config['detail']['enddate'] = Carbon::parse($result['enddate'])->format('d F Y');
        $this->config['detail']['days_left'] = Carbon::now()->diffInDays($result['enddate']);
        $this->config['tasks_statistic'] = $this->task_service->statistic($request->id);
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
        $this->authorize('view',Project::find($request->id));

        $project = (new ProjectService())->detail($request->id);
        $this->config['title'] = "PROJECT'S TASKS";
        $this->config['active'] = "projects.list";
        $this->config['milestones'] = (new MilestoneService())->get($request->id);
        $this->config['users'] = (new UserService())->get();
        $this->config['navs'] = [
            [
                'label' => 'Projects',
                'link' => route('projects.list')
            ],
            [
                'label' => $project->name,
                'link' => route('projects.detail',['id' => $request->id])
            ],
            [
                'label' => 'Board'
            ]
        ];
        return view('pages.board', $this->config);
    }
}
