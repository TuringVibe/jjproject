<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FinanceMutationSchedule extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'next_mutation_date',
        'name',
        'currency',
        'nominal',
        'mode',
        'project_id',
        'attached_label_ids',
        'repeat',
        'notes'
    ];
    protected $hidden = [
        'deleted_at',
        'deleted_by'
    ];
    protected $casts = [
        'next_mutation_date' => 'datetime:Y-m-d',
        'nominal' => 'float',
        'project_id' => 'integer',
        'attached_label_ids' => 'array',
        'created_by' => 'integer',
        'updated_by' => 'integer',
        'deleted_by' => 'integer'
    ];

    public function project() {
        return $this->belongsTo(Project::class);
    }

    public function getLabelsAttribute() {
        return FinanceLabel::whereIn('id',$this->attached_label_ids)->get();
    }
}
