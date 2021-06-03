<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class Project extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'description',
        'startdate',
        'enddate',
        'budget',
        'status',
        'created_by',
        'updated_by'
    ];
    protected $hidden = [
        'deleted_at',
        'deleted_by'
    ];
    protected $casts = [
        'startdate' => 'datetime:Y-m-d',
        'enddate' => 'datetime:Y-m-d',
        'budget' => 'integer',
        'created_by' => 'integer',
        'updated_by' => 'integer',
        'deleted_by' => 'integer'
    ];

    public function users() {
        return $this->belongsToMany(User::class,'project_users')->withTimestamps();
    }

    public function labels() {
        return $this->belongsToMany(ProjectLabel::class, 'project_attached_labels')->withTimestamps();
    }

    public function files() {
        return $this->belongsToMany(File::class, 'project_files')->withTimestamps()
        ->withPivot('created_by','updated_by');
    }

    public function tasks() {
        return $this->hasMany(Task::class);
    }

    public function task_status_logs(){
        return $this->hasMany(TaskStatusLog::class);
    }

    public function tasks_done_count($total_days){
        $latestLogs = $this->task_status_logs()
            ->select('task_id',DB::raw('MAX(created_at) as last_log'))
            ->groupBy('task_id');

        return $this->task_status_logs()
            ->joinSub($latestLogs, 'latest_logs', function($join){
                $join->on('task_status_logs.task_id','=','latest_logs.task_id');
            })
            ->whereRaw('task_status_logs.created_at = latest_logs.last_log')
            ->where('task_status_logs.last_status','done')
            ->whereRaw('TIMESTAMPDIFF(DAY,latest_logs.last_log,CURDATE()) <= ?',[$total_days])
            ->count();
    }

    public function comments() {
        return $this->hasManyThrough(TaskComment::class, Task::class);
    }

    public function milestones() {
        return $this->hasMany(Milestone::class);
    }

    public function scopeNotStarted($query) {
        return $query->whereNull('deleted_at')->where('status','notstarted');
    }

    public function scopeOnGoing($query) {
        return $query->whereNull('deleted_at')->where('status','ongoing');
    }

    public function scopeComplete($query) {
        return $query->whereNull('deleted_at')->where('status','complete');
    }

    public function scopeOnHold($query) {
        return $query->whereNull('deleted_at')->where('status','onhold');
    }

    public function scopeCanceled($query) {
        return $query->whereNull('deleted_at')->where('status','canceled');
    }
}
