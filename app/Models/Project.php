<?php

namespace App\Models;

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
        return $this->belongsToMany(File::class, 'project_files')->withTimestamps();
    }

    public function tasks() {
        return $this->hasMany(Task::class);
    }

    public function milestones() {
        return $this->hasMany(Milestone::class);
    }
}
