<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TaskStatusLog extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'project_id',
        'task_id',
        'last_status',
        'notes',
        'created_at',
        'created_by'
    ];
    protected $casts = [
        'project_id' => 'integer',
        'task_id' => 'integer',
        'created_by' => 'integer'
    ];

    public function project() {
        return $this->belongsTo(Project::class);
    }

    public function task() {
        return $this->belongsTo(Task::class);
    }
}
