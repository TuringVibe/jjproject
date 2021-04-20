<?php

namespace App\Http\Controllers;

use App\Http\Requests\ValidateTaskFile;
use App\Http\Requests\ValidateTaskFileId;
use App\Http\Requests\ValidateTaskFileParams;
use App\Models\Task;
use App\Services\TaskFileService;
use Illuminate\Support\Facades\Gate;

class TaskFileController extends Controller
{
    private $task_file_service;

    public function __construct(TaskFileService $task_file_service)
    {
        $this->task_file_service = $task_file_service;
    }

    public function data(ValidateTaskFileParams $request) {
        $result = $this->task_file_service->get($request->task_id);
        $result = $result->map(function($item,$key) use($request){
            $item['can_delete'] = $request->user()->can('deleteFile',[Task::find($request->task_id),$item['id']]);
            return $item;
        });
        return response()->json($result);
    }

    public function download(ValidateTaskFileId $request) {
        $result = $this->task_file_service->detail($request->id);
        return response()->download(storage_path('app/'.$result->file_path), $result->filename);
    }

    public function save(ValidateTaskFile $request) {
        $result = $this->task_file_service->save($request->task_id, $request->file('file'));
        if(isset($result)) {
            $task = Task::find($request->task_id);
            $result->can_delete = $request->user()->can('deleteFile',[$task, $result->id]);
            return [
                'status' => true,
                'message' => 'Data saved successfully',
                'data' => $result
            ];
        }
        return [
            'status' => false,
            'message' => 'Failed to save data',
            'errors' => []
        ];
    }

    public function delete(ValidateTaskFileId $request) {
        $task = $this->task_file_service->getTask($request->id);
        $response = Gate::inspect('deleteFile',[$task, $request->id]);
        if(!$response->allowed())
            return response()->json(['status' => false, 'message' => $response->message()], 403);

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
