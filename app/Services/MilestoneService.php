<?php
namespace App\Services;

use App\Models\File;
use App\Models\Milestone;
use App\Models\Project;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class MilestoneService {

    public function get($project_id, $params = []) {
        $query_builder = Milestone::whereNull('deleted_at')
            ->where('project_id',$project_id);

        foreach($params as $field => $val) {
            if(isset($val)) {
                switch($field) {
                    case 'name':
                        $query_builder->where('name','like',"%{$val}%");
                    break;
                    default:
                        $query_builder->where($field,$val);
                    break;
                }
            }
        }

        $milestones = $query_builder->orderBy('created_at','asc')
            ->get()->toArray();
        return $milestones;
    }

    public function detail($id) {
        $milestone = Milestone::whereNull('deleted_at')
            ->where('id',$id)->first();
        return $milestone;
    }

    public function save($project_id, $attr) {
        try{
            $milestone = null;
            DB::transaction(function () use(&$milestone, $project_id, $attr){
                if(isset($attr['id'])) {
                    $milestone = Milestone::find($attr['id']);
                    $attr['updated_by'] = Auth::user()->id;
                    $milestone->fill($attr);
                    $milestone->save();
                } else {
                    $project = Project::find($project_id);
                    $attr['created_by'] = Auth::user()->id;
                    $attr['updated_by'] = $attr['created_by'];
                    $milestone = new Milestone($attr);
                    $project->milestones()->save($milestone);
                }
            });
            return $milestone;
        } catch(\Exception $e) {
            throw new Exception(__('response.save_failed'), 500);
        }
    }

    public function delete($milestone_id) {
        try{
            $result = false;
            DB::transaction(function() use(&$result,$milestone_id){
                $milestone = Milestone::find($milestone_id);
                $milestone->deleted_by = Auth::user()->id;
                $milestone->deleted_at = Carbon::now();
                $result = $milestone->save();
            });
            return $result;
        } catch(\Exception $e) {
            throw new Exception(__('response.delete_failed'), 500);
        }
    }

}
