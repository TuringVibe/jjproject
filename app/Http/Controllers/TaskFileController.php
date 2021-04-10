<?php

namespace App\Http\Controllers;

use App\Http\Requests\ValidateTaskFile;
use App\Http\Requests\ValidateTaskFileId;
use App\Http\Requests\ValidateTaskFileParams;
use App\Services\TaskFileService;

class TaskFileController extends Controller
{
    private $task_file_service;

    public function __construct(TaskFileService $task_file_service)
    {
        $this->task_file_service = $task_file_service;
    }

    public function data(ValidateTaskFileParams $request) {
        $result = $this->task_file_service->get($request->task_id);
        echo json_encode($result->toArray());
    }

    public function download(ValidateTaskFileId $request) {
        $result = $this->task_file_service->detail($request->id);
        return response()->download(storage_path('app/'.$result->file_path), $result->filename);
    }

    public function save(ValidateTaskFile $request) {
        $result = $this->task_file_service->save($request->task_id, $request->file('file'));
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

    public function delete(ValidateTaskFileId $request) {
        $result = $this->task_file_service->delete($request->id);
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
