<?php

namespace App\Http\Controllers;

use App\Http\Requests\ValidateProjectFile;
use App\Http\Requests\ValidateProjectFileId;
use App\Http\Requests\ValidateProjectFileParams;
use App\Models\Project;
use App\Services\ProjectFileService;
use Illuminate\Support\Facades\Gate;

class ProjectFileController extends Controller
{
    private $project_file_service;

    public function __construct(ProjectFileService $project_file_service)
    {
        $this->project_file_service = $project_file_service;
    }

    public function data(ValidateProjectFileParams $request) {
        $result = $this->project_file_service->get($request->project_id);
        $result = $result->map(function($item,$key) use($request){
            $item['can_delete'] = $request->user()->can('deleteFile',[Project::find($request->project_id),$item['id']]);
            return $item;
        });
        return response()->json($result);
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
        $project = $this->project_file_service->getProject($request->id);
        $response = Gate::inspect('deleteFile',[$project,$request->id]);
        if(!$response->allowed())
            return response()->json(['status' => false, 'message' => $response->message()], 403);

        $result = $this->project_file_service->delete($request->id);
        return $result;
    }
}
