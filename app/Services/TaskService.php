<?php
namespace App\Services;

use App\Events\TaskSaved;
use App\Models\Task;
use Carbon\Carbon;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TaskService {

    public function statistic($project_id = null) {
        return [
            'total' => Task::ofProject($project_id)->count(),
            'todo' => Task::ofProject($project_id)->todo()->count(),
            'inprogress' => Task::ofProject($project_id)->inProgress()->count(),
            'done' => Task::ofProject($project_id)->done()->count()
        ];
    }

    public function get($params = []) {
        $query_builder = Task::with(['users:id,firstname,lastname,img_path','project','project.labels'])->whereNull('deleted_at');
        foreach($params as $field => $val) {
            if(isset($val)) {
                switch($field) {
                    case 'name':
                        $query_builder->where($field,'like',"%{$val}%");
                    break;
                    case 'due_date_from':
                        $query_builder->where('due_date','>=',$val);
                    break;
                    case 'due_date_to':
                        $query_builder->where('due_date','<=',$val);
                    break;
                    case 'user_id':
                        $query_builder->where(function($query) use($val){
                            $query->whereHas('users',function(Builder $query) use($val){
                                $query->where('users.id',$val);
                            })->orWhere('tasks.created_by',$val);
                        });
                    break;
                    case 'project_label_id':
                        $query_builder->whereHas('project.labels', function(Builder $query) use($val){
                            $query->wherePivot('project_label_id',$val);
                        });
                    break;
                    case 'status':
                        $query_builder->whereIn('status',$val);
                        break;
                    default:
                        $query_builder->where($field,$val);
                    break;
                }
            }
        }
        $tasks = $query_builder->orderBy('created_at','desc')
            ->get()->toArray();

        return $tasks;
    }

    public function getCard($task_id) {
        $task = Task::with([
            'milestone',
            'subtasks',
            'files',
            'comments',
            'comments.user',
            'users',
        ])->whereNull('deleted_at')
            ->where('id',$task_id)
            ->withCount(
                'files',
                'subtasks',
                'comments',
                'doneSubtasks'
            )->first();

        return $task;
    }

    public function getCards($project_id) {
        $tasks = Task::withCount([
            'files',
            'subtasks',
            'comments',
            'doneSubtasks'
        ])->whereNull('deleted_at')
            ->where('project_id',$project_id)
            ->orderBy('status','asc')
            ->orderBy('order','asc')
            ->get();

        return $tasks;
    }

    public function detail($id) {
        $task = Task::with(['users:id'])->where('id',$id)->first();
        return $task;
    }

    public function move($project_id, $task_id, $status_destination, $order_destination) {
        $old_status = null;

        $orders = Task::whereNull('deleted_at')
            ->where('project_id',$project_id)
            ->orderBy('order','asc')
            ->select('id','status','order','updated_at')
            ->get()->map(function($item, $key) use($task_id, $status_destination, $order_destination, &$old_status){
                if($item['id'] == $task_id) {
                    $old_status = $item['status'];
                    $now = Carbon::now();
                    $item['order'] = (int)$order_destination;
                    $item['status'] = $status_destination;
                    $item['updated_at'] = $now;
                }
                return $item;
            })->sortBy([['status','asc'],['order','asc'],['updated_at','desc']]);

        $ids = [];
        $order = [];
        $user_id = Auth::user()->id;
        foreach($orders as $val) {
            $ids[] = $val['id'];
            if(!isset($order[$val['status']])) $order[$val['status']] = 1;
            $status_cases[] = "When {$val['id']} then '".$val['status']."'";
            $order_cases[] = "When {$val['id']} then ".$order[$val['status']];
            $order[$val['status']]++;
        }

        $ids = implode(',',$ids);
        $status_cases = implode(' ',$status_cases);
        $order_cases = implode(' ',$order_cases);
        $update_query = "UPDATE tasks SET `order` = CASE id {$order_cases} END, `status` = CASE id {$status_cases} END, updated_by = ? WHERE id IN ({$ids})";
        try{
            DB::transaction(function () use($update_query, $user_id){
                DB::update($update_query,[$user_id]);
            });
            TaskSaved::dispatch($project_id, $task_id, $old_status, $status_destination);
            $result = true;
        }catch(\Exception $e){
            throw new Exception('Failed: '.$e->getMessage(),500);
        }
        return $result;
    }

    public function save($attr) {
        try{
            $task = null;
            DB::transaction(function () use(&$task, $attr){
                if(isset($attr['id'])) {
                    $task = Task::find($attr['id']);
                    if($task->status != $attr['status']) {
                        $attr['order'] = 1;
                    }
                    if(array_key_exists('order',$attr) && !isset($attr['order'])) {
                        unset($attr['order']);
                    }
                    if(!empty($attr['user_ids'])) {
                        $task->users()->sync($attr['user_ids']);
                    }
                    if(array_key_exists('user_ids',$attr)) unset($attr['user_ids']);
                    $attr['updated_by'] = Auth::user()->id;
                    $task->fill($attr);
                    $task->save();
                    if($task->wasChanged('order') OR $task->wasChanged('status')) {
                        $this->move($task->project_id,$task->id,$task->status,$task->order);
                    }
                } else {
                    $user_ids = $attr['user_ids'] ?? null;
                    if(array_key_exists('user_ids',$attr)) unset($attr['user_ids']);
                    $attr['order'] = Task::newOrder($attr['project_id'], $attr['status']);
                    $attr['created_by'] = Auth::user()->id;
                    $attr['updated_by'] = $attr['created_by'];
                    $task = Task::create($attr);
                    if(!empty($user_ids)) {
                        $task->users()->sync($user_ids);
                    }
                    TaskSaved::dispatch($task->project_id, $task->id, null, $task->status);
                }
            });
            return $task;
        } catch(\Exception $e) {
            throw new Exception("Failed to save data: ".$e->getMessage(), 500);
        }
    }

    public function delete($id) {
        try{
            DB::transaction(function () use($id){
                $task = Task::find($id);
                $task->users()->detach();
                $task->deleted_by = Auth::user()->id;
                $task->deleted_at = Carbon::now();
                $task->save();
            });
            return true;
        }catch(\Exception $e) {
            throw new Exception("Failed to delete data", 500);
        }
    }

}
