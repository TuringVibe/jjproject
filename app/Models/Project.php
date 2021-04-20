<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

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
