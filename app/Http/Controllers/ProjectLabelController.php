<?php

namespace App\Http\Controllers;

use App\Http\Requests\ValidateProjectLabel;
use App\Http\Requests\ValidateProjectLabelId;
use App\Http\Requests\ValidateProjectLabelParams;
use App\Services\ProjectLabelService;

class ProjectLabelController extends Controller
{
    private $project_label_service;

    public function __construct(ProjectLabelService $project_label_service)
    {
        $this->project_label_service = $project_label_service;
    }

    public function list() {
        $this->config['title'] = "PROJECT LABEL";
        $this->config['active'] = "project-labels.list";
        $this->config['navs'] = [
            [
                'label' => 'Project Label'
            ]
        ];
        return view('pages.project-label-list', $this->config);
    }

    public function data(ValidateProjectLabelParams $request) {
        $params['name'] = $request->name;
        $result = $this->project_label_service->get($params);
        echo json_encode($result);
    }

    public function detail(ValidateProjectLabelId $request) {
        $result = $this->project_label_service->detail($request->id);
        return $result;
    }

    public function save(ValidateProjectLabel $request) {
        $result = $this->project_label_service->save($request->validated());
        return $result;
    }

    public function delete(ValidateProjectLabelId $request) {
        $result = $this->project_label_service->delete($request->id);
        return $result;
    }
}
