<?php
namespace App\Services;

use App\Models\ProjectLabel;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class ProjectLabelService {

    public function get($params = []) {
        $query_builder = ProjectLabel::whereNull('deleted_at');
        foreach($params as $field => $val) {
            if(isset($val)) {
                if($field == 'name') {
                    $query_builder->where($field,'like',"%{$val}%");
                } else {
                    $query_builder->where($field,$val);
                }
            }
        }
        $project_lables = $query_builder->orderBy('created_at','desc')
            ->select('id','name','color')
            ->withCount('projects')
            ->get()->toArray();

        return $project_lables;
    }

    public function detail($id) {
        $project_label = ProjectLabel::find($id);
        return $project_label;
    }

    public function save($attr) {
        if(isset($attr['id'])) {
            $project_label = ProjectLabel::find($attr['id']);
            $attr['updated_by'] = Auth::user()->id;
            $project_label->fill($attr);
            $project_label->save();
        } else {
            $attr['created_by'] = Auth::user()->id;
            $attr['updated_by'] = $attr['created_by'];
            $project_label = ProjectLabel::create($attr);
        }
        return $project_label;
    }

    public function delete($id) {
        $project_label = ProjectLabel::find($id);
        $project_label->deleted_by = Auth::user()->id;
        $project_label->deleted_at = Carbon::now();
        return $project_label->save();
    }

}
