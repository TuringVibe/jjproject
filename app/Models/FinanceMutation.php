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
        'currency',
        'nominal',
        'usd_cny',
        'usd_idr',
        'cny_usd',
        'cny_idr',
        'idr_usd',
        'idr_cny',
        'conversion_datetime',
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
        'nominal' => 'float',
        'usd_cny' => 'float',
        'usd_idr' => 'float',
        'cny_usd' => 'float',
        'cny_idr' => 'float',
        'idr_usd' => 'float',
        'idr_cny' => 'float',
        'conversion_datetime' => 'datetime',
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

    public function scopeTotalDebit($query, $currency) {
        $total_debit = 0;
        foreach($query->where('mode','debit')->cursor() as $mutation) {
            $convert_to = $mutation->{$mutation->currency."_".$currency} ?? 1;
            $total_debit += $mutation->nominal * $convert_to;
        }
        return $total_debit;
    }

    public function scopeTotalCredit($query, $currency) {
        $total_credit = 0;
        foreach($query->where('mode','credit')->cursor() as $mutation) {
            $convert_to = $mutation->{$mutation->currency."_".$currency} ?? 1;
            $total_credit += $mutation->nominal * $convert_to;
        }
        return $total_credit * -1;
    }
}
