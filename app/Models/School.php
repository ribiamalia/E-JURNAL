<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class School extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama', 'alamat', 'nama_pembimbing','no_hp'
    ];

    public function users()
    {
        return $this->hasMany(User::class);
    }
}
