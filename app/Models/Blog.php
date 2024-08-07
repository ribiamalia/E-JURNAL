<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Blog extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'judul', 'konten', 'slug', 'dokumen' 
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
