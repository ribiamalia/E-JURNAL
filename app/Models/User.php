<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles; 
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Database\Eloquent\Casts\Attribute;

class User extends Authenticatable implements JWTSubject
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
       'image',
           'nomor_induk',
          'jurusan',
           'kelas',
           'school_id',
            'gender',
           'alamat',
          'nama_ortu',
          'alamat_ortu',
            'no_hp_ortu',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }

    public function schools()
    {

        return $this->belongsTo(School::class, 'school_id');
    }
    public function timelines()
    {

        return $this->hasMany(Timeline::class);
    }
    public function jurnals()
    {

        return $this->hasOne(Jurnal::class);
    }

    protected function image(): Attribute
     {
        return Attribute::make(
            get: fn ($image) => asset('/storage/' . $image),
         );
     }
}
