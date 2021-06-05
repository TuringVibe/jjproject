<?php
namespace App\Services;

use App\Models\Task;
use App\Models\TaskComment;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TaskCommentService {

    public function get($task_id) {
        $query_builder = TaskComment::with(['user:id,firstname,lastname,img_path'])
            ->whereNull('deleted_at')
            ->where('task_id', $task_id);
        $comments = $query_builder->orderBy('created_at','desc')
            ->get();

        return $comments;
    }

    public function detail($id) {
        $comment = TaskComment::with(['user:id,firstname,lastname,img_path'])
            ->whereNull('deleted_at')
            ->where('id',$id)->first();
        return $comment;
    }

    public function save($task_id, $attr) {
        try{
            $comment = null;
            DB::transaction(function () use(&$comment, $task_id, $attr){
                if(isset($attr['id'])) {
                    $comment = TaskComment::find($attr['id']);
                    $attr['updated_by'] = Auth::user()->id;
                    $comment->fill($attr);
                    $comment->save();
                    $comment->load('user');
                } else {
                    $task = Task::find($task_id);
                    $attr['created_by'] = Auth::user()->id;
                    $attr['updated_by'] = $attr['created_by'];
                    $comment = new TaskComment($attr);
                    $task->comments()->save($comment);
                    $comment->load('user');
                }
            });
            return $comment;
        } catch(\Exception $e) {
            throw new Exception(__('response.save_failed'), 500);
        }
    }

    public function delete($comment_id) {
        try{
            $result = false;
            DB::transaction(function() use(&$result,$comment_id){
                $comment = TaskComment::find($comment_id);
                $comment->deleted_by = Auth::user()->id;
                $comment->deleted_at = Carbon::now();
                $result = $comment->save();
            });
            return $result;
        } catch(\Exception $e) {
            throw new Exception(__('response.delete_failed'), 500);
        }
    }

}
