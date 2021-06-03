<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

class Task extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'project_id',
        'milestone_id',
        'name',
        'status',
        'priority',
        'order',
        'description',
        'due_date',
        'created_by',
        'updated_by',
    ];
    protected $hidden = [];
    protected $casts = [
        'created_by' => 'integer',
        'updated_by' => 'integer',
        'deleted_by' => 'integer'
    ];

    public function project() {
        return $this->belongsTo(Project::class);
    }

    public function milestone() {
        return $this->belongsTo(Milestone::class);
    }

    public function users() {
        return $this->belongsToMany(User::class,'task_assignees')->withTimestamps();
    }

    public function files() {
        return $this->belongsToMany(File::class, 'task_files')->withTimestamps();
    }

    public function comments() {
        return $this->hasMany(TaskComment::class);
    }

    public function subtasks() {
        return $this->hasMany(Subtask::class);
    }

    public function scopeDoneSubtasks($query) {
        return $this->subtasks()->where('is_done',true);
    }

    public function scopeOfProject($query, $project_id) {
        if(!isset($project_id)) return $query;
        return $query->where('project_id', $project_id);
    }

    public function scopeNewOrder($query, $project_id, $status) {
        $last_task = $query->whereNotNull('order')
            ->where('project_id',$project_id)
            ->where('status',$status)
            ->orderBy('order','desc')
            ->first();
        return $last_task == null ? 1 : $last_task->order + 1;
    }

    public function scopeTodo($query) {
        return $query->where('status','todo');
    }

    public function scopeInProgress($query) {
        return $query->where('status','inprogress');
    }

    public function scopeDone($query) {
        return $query->where('status','done');
    }
}
