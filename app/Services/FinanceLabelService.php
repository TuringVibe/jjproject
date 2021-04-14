<?php
namespace App\Services;

use App\Models\FinanceLabel;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class FinanceLabelService {

    public function get($params = []) {
        $query_builder = FinanceLabel::whereNull('deleted_at');
        foreach($params as $field => $val) {
            if(isset($val)) {
                if($field == 'name') {
                    $query_builder->where($field,'like',"%{$val}%");
                } else {
                    $query_builder->where($field,$val);
                }
            }
        }
        $finance_labels = $query_builder->orderBy('created_at','desc')
            ->select('id','name','color')
            ->withCount('mutations')
            ->get()->toArray();

        return $finance_labels;
    }

    public function detail($id) {
        $finance_label = FinanceLabel::find($id);
        return $finance_label;
    }

    public function save($attr) {
        if(isset($attr['id'])) {
            $finance_label = FinanceLabel::find($attr['id']);
            $attr['updated_by'] = Auth::user()->id;
            $finance_label->fill($attr);
            $finance_label->save();
        } else {
            $attr['created_by'] = Auth::user()->id;
            $attr['updated_by'] = $attr['created_by'];
            $finance_label = FinanceLabel::create($attr);
        }
        return $finance_label;
    }

    public function delete($id) {
        $finance_label = FinanceLabel::find($id);
        $finance_label->deleted_by = Auth::user()->id;
        $finance_label->deleted_at = Carbon::now();
        return $finance_label->save();
    }

}
