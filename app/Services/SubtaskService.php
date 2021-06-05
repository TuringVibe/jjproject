<?php
namespace App\Services;

use App\Models\Subtask;
use App\Models\Task;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SubtaskService {

    public function get($task_id) {
        $query_builder = Subtask::whereNull('deleted_at')
            ->where('task_id', $task_id);
        $subtasks = $query_builder->orderBy('created_at','desc')
            ->get()->toArray();

        return $subtasks;
    }

    public function detail($id) {
        $subtask = Subtask::whereNull('deleted_at')
            ->where('id',$id)->first();
        return $subtask;
    }

    public function save($task_id, $attr) {
        try{
            $subtask = null;
            DB::transaction(function () use(&$subtask, $task_id, $attr){
                if(isset($attr['id'])) {
                    $subtask = Subtask::find($attr['id']);
                    $attr['updated_by'] = Auth::user()->id;
                    $subtask->fill($attr);
                    $subtask->save();
                } else {
                    $task = Task::find($task_id);
                    $attr['created_by'] = Auth::user()->id;
                    $attr['updated_by'] = $attr['created_by'];
                    $subtask = new Subtask($attr);
                    $task->subtasks()->save($subtask);
                }
            });
            return $subtask;
        } catch(\Exception $e) {
            throw new Exception(__('response.save_failed'), 500);
        }
    }

    public function bulkInsert($task_id, $attr) {
        try{
            $subtasks = [];
            DB::transaction(function () use(&$subtasks, $task_id, $attr){
                $user_id = Auth::user()->id;
                $task = Task::find($task_id);
                foreach($attr['name'] as $name) {
                    $subtasks[] = new Subtask([
                        'name' => $name,
                        'is_done' => $attr['is_done'],
                        'created_by' => $user_id,
                        'updated_by' => $user_id
                    ]);
                }
                $task->subtasks()->saveMany($subtasks);
            });
            return $subtasks;
        }catch(\Exception $e){ throw new Exception("Failed to save all data", 500); }
    }

    public function delete($subtask_id) {
        try{
            $result = false;
            DB::transaction(function() use(&$result,$subtask_id){
                $subtask = Subtask::find($subtask_id);
                $subtask->deleted_by = Auth::user()->id;
                $subtask->deleted_at = Carbon::now();
                $result = $subtask->save();
            });
            return $result;
        } catch(\Exception $e) {
            throw new Exception(__('response.delete_failed'), 500);
        }
    }

}
