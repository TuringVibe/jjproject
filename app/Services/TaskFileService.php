<?php
namespace App\Services;

use App\Models\File;
use App\Models\Task;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class TaskFileService {

    public function getTask($file_id) {
        $task = File::find($file_id)->task[0];
        return $task;
    }

    public function get($task_id) {
        $files = Task::find($task_id)->files()
            ->whereNull('files.deleted_at')
            ->orderBy('files.created_at','asc')
            ->get();

        return $files;
    }

    public function detail($id) {
        $file = File::whereNull('deleted_at')
            ->where('id',$id)->first();
        return $file;
    }

    public function save($task_id, $uploaded_file) {
        try{
            $file = null;
            DB::transaction(function () use(&$file, $task_id, $uploaded_file){
                $logged_in_user_id = Auth::user()->id;
                $task = Task::find($task_id);
                $file_path = $uploaded_file->store('tasks');
                $filename = $uploaded_file->getClientOriginalName();
                $ext = $uploaded_file->extension();
                $size = Storage::size($file_path);
                $created_by = $logged_in_user_id;
                $updated_by = $logged_in_user_id;
                $file = File::create(compact("filename","file_path","ext","size","created_by","updated_by"));
                $task->files()->attach($file->id, [
                    'updated_by' => $logged_in_user_id,
                    'created_by' => $logged_in_user_id
                ]);
            });
            return $file;
        } catch(\Exception $e) {
            throw new Exception(__('response.save_failed'), 500);
        }
    }

    public function delete($file_id) {
        try{
            $result = false;
            DB::transaction(function() use(&$result,$file_id){
                $file = File::find($file_id);
                if(Storage::delete($file->file_path)) {
                    $file->task()->detach();
                    $file->deleted_by = Auth::user()->id;
                    $file->deleted_at = Carbon::now();
                    $result = $file->save();
                }
            });
            return $result;
        } catch(\Exception $e) {
            throw new Exception(__('response.delete_failed'), 500);
        }
    }

}
