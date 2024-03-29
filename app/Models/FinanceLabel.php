<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FinanceLabel extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['name','color','created_by','updated_by'];
    protected $hidden = [
        'deleted_at',
        'deleted_by',
        'created_at',
        'created_by',
        'updated_at',
        'updated_by'
    ];
    protected $casts = [
        'created_by' => 'integer',
        'updated_by' => 'integer',
        'deleted_by' => 'integer'
    ];

    public function mutations() {
        return $this->belongsToMany(FinanceMutation::class,'finance_attached_labels')->withTimestamps();
    }
}
