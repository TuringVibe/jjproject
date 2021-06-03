<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ThemeUser extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'theme_id'
    ];
    protected $casts = [
        'user_id' => 'integer',
        'theme_id' => 'integer',
    ];

    public function theme() {
        return $this->belongsTo(Theme::class);
    }

    public function user() {
        return $this->belongsTo(User::class);
    }
}
