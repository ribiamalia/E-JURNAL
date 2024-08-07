<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Jurnal extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama_projek', 'user_id', 'deadline', 'status', 'prioritas', 'dokumen'
    ];

    public function users()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    protected function dokumen(): Attribute
    {
       return Attribute::make(
           get: fn ($image) => asset('/storage/' . $image),
        );
    }


}
