<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Timeline extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'user_id', 'tanggal', 'start_time', 'end_time',

    ];

    public function users()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
