<?php

namespace App\Http\Controllers;

use App\Http\Requests\ValidateProjectFile;
use App\Http\Requests\ValidateProjectFileId;
use App\Http\Requests\ValidateProjectFileParams;
use App\Services\ProjectFileService;

class ProjectFileController extends Controller
{
    private $project_file_service;

    public function __construct(ProjectFileService $project_file_service)
    {
        $this->project_file_service = $project_file_service;
    }

    public function data(ValidateProjectFileParams $request) {
        $result = $this->project_file_service->get($request->project_id);
        echo json_encode($result->toArray());
    }

    public function download(ValidateProjectFileId $request) {
        $result = $this->project_file_service->detail($request->id);
        return response()->download(storage_path('app/'.$result->file_path), $result->filename);
    }

    public function save(ValidateProjectFile $request) {
        $result = $this->project_file_service->save($request->project_id, $request->file('file'));
        return $result;
    }

    public function delete(ValidateProjectFileId $request) {
        $result = $this->project_file_service->delete($request->id);
        return $result;
    }
}
