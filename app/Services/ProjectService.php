<?php
namespace App\Services;

use App\Models\Project;
use Carbon\Carbon;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ProjectService {

    public function get($params = []) {
        $query_builder = Project::with(['users:id,firstname,lastname,img_path','labels'])->whereNull('deleted_at');
        foreach($params as $field => $val) {
            if(isset($val)) {
                switch($field) {
                    case 'name':
                        $query_builder->where($field,'like',"%{$val}%");
                    break;
                    case 'user_id':
                        $query_builder->whereHas('users',function(Builder $query) use($val){
                            $query->where('users.id',$val);
                        });
                    break;
                    case 'project_label_id':
                        $query_builder->whereHas('labels',function(Builder $query) use($val){
                            $query->where('project_labels.id',$val);
                        });
                    break;
                    default:
                        $query_builder->where($field,$val);
                    break;
                }
            }
        }
        $projects = $query_builder->orderBy('created_at','desc')
            ->select('id','name','status')
            ->withCount('tasks')
            ->get()->toArray();

        return $projects;
    }

    public function detail($id) {
        $project = Project::with(['users:id,firstname,lastname,img_path','labels:id,name,color'])->where('id',$id)->first();
        return $project;
    }

    #TODO: ubah algoritma save disesuaikan dengan financemutationservice
    public function save($attr) {
        try{
            $project = null;
            DB::transaction(function () use(&$project, $attr){
                if(isset($attr['id'])) {
                    $project = Project::find($attr['id']);
                    if(!empty($attr['user_ids'])) {
                        $project->users()->detach();
                        foreach($attr['user_ids'] as $user_id) {
                            $project->users()->attach($user_id);
                        }
                    }
                    if(array_key_exists('user_ids',$attr)) unset($attr['user_ids']);
                    if(!empty($attr['project_label_ids'])) {
                        $project->labels()->detach();
                        foreach($attr['project_label_ids'] as $label_id) {
                            $project->labels()->attach($label_id);
                        }
                    }
                    if(array_key_exists('project_label_ids',$attr)) unset($attr['project_label_ids']);
                    $attr['updated_by'] = Auth::user()->id;
                    $project->fill($attr);
                    $project->save();
                } else {
                    $user_ids = $attr['user_ids'] ?? null;
                    $project_label_ids = $attr['project_label_ids'] ?? null;
                    unset($attr['user_ids'],$attr['project_label_ids']);
                    $attr['created_by'] = Auth::user()->id;
                    $attr['updated_by'] = $attr['created_by'];
                    $project = Project::create($attr);
                    if(!empty($user_ids)) {
                        foreach($user_ids as $user_id) {
                            $project->users()->attach($user_id);
                        }
                    }
                    if(!empty($project_label_ids)) {
                        foreach($project_label_ids as $label_id) {
                            $project->labels()->attach($label_id);
                        }
                    }
                }
            });
            return $project;
        } catch(\Exception $e) {
            throw new Exception("Failed to save data", 500);
        }
    }

    public function delete($id) {
        try{
            DB::transaction(function() use($id){
                $user_id = Auth::user()->id;
                $now = Carbon::now();
                $project = Project::find($id);
                $project->tasks()->update(['deleted_by' => $user_id, 'deleted_at' => $now]);
                $project->milestones()->update(['deleted_by' => $user_id, 'deleted_at' => $now]);
                $project->labels()->detach();
                $project->users()->detach();
                $project->files()->detach();
                $project->deleted_by = $user_id;
                $project->deleted_at = Carbon::now();
                $project->save();
            });
            return true;
        }catch(\Exception $e) {
            throw new Exception("Failed to delete data", 500);
        }
    }

}
