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
        'from_wallet_id',
        'to_wallet_id',
        'project_id',
        'attached_label_ids',
        'repeat',
        'notes',
        'created_by',
        'updated_by'
    ];
    protected $hidden = [
        'deleted_at',
        'deleted_by'
    ];
    protected $casts = [
        'from_wallet_id' => 'integer',
        'to_wallet_id' => 'integer',
        'next_mutation_date' => 'datetime:Y-m-d',
        'nominal' => 'decimal:65',
        'project_id' => 'integer',
        'attached_label_ids' => 'array',
        'created_by' => 'integer',
        'updated_by' => 'integer',
        'deleted_by' => 'integer'
    ];

    public function fromWallet() {
        return $this->belongsTo(Wallet::class, "from_wallet_id");
    }

    public function toWallet() {
        return $this->belongsTo(Wallet::class, "to_wallet_id");
    }

    public function project() {
        return $this->belongsTo(Project::class);
    }

    public function getLabelsAttribute() {
        return FinanceLabel::whereIn('id',$this->attached_label_ids)->get();
    }
}
