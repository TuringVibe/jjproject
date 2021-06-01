<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FinanceAsset extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'qty',
        'unit',
        'buy_datetime',
        'buy_price_per_unit',
        'currency',
        'usd_cny',
        'usd_idr',
        'cny_usd',
        'cny_idr',
        'idr_usd',
        'idr_cny',
        'conversion_datetime',
        'created_by',
        'updated_by'
    ];
    protected $hidden = [
        'deleted_at',
        'deleted_by'
    ];
    protected $casts = [
        'qty' => 'decimal:3',
        'buy_datetime' => 'datetime:Y-m-d H:i:s',
        'buy_price_per_unit' => 'decimal:3',
        'usd_cny' => 'decimal:15',
        'usd_idr' => 'decimal:15',
        'cny_usd' => 'decimal:15',
        'cny_idr' => 'decimal:15',
        'idr_usd' => 'decimal:15',
        'idr_cny' => 'decimal:15',
        'conversion_datetime' => 'datetime:Y-m-d H:i:s',
        'created_by' => 'integer',
        'updated_by' => 'integer',
        'deleted_by' => 'integer'
    ];

    public function price_changes() {
        return $this->hasMany(AssetPriceChange::class);
    }
}
