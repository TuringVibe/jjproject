<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FinanceMutation extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'mutation_date',
        'name',
        'nominal',
        'mode',
        'project_id',
        'notes',
        'created_by',
        'updated_by'
    ];
    protected $hidden = [
        'deleted_at',
        'deleted_by'
    ];
    protected $casts = [
        'mutation_date' => 'datetime:Y-m-d',
        'nominal' => 'integer',
        'project_id' => 'integer',
        'created_by' => 'integer',
        'updated_by' => 'integer',
        'deleted_by' => 'integer'
    ];

    public function labels() {
        return $this->belongsToMany(FinanceLabel::class,'finance_attached_labels')->withTimestamps();
    }

    public function project() {
        return $this->belongsTo(Project::class);
    }
}
