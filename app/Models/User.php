<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Laravel\Sanctum\HasApiTokens;

//class User extends Authenticatable
class User extends Authenticatable implements JWTSubject
{
    use HasFactory;
    use Notifiable;
    use HasApiTokens;
    protected $fillable = [
        'first_name',
        'last_name',
        'phone_number',
        'birthday',
        'admin',
        'password'
    ];

    protected $attributes = [
        'admin' => 'no'
    ];

    public function addresses(){
        return $this->hasMany(UserAddress::class, 'user_id');
    }

    public function orders(){
        return $this->hasMany(Order::class);
    }

    public function getJWTIdentifier()
    {
        // TODO: Implement getJWTIdentifier() method.
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        // TODO: Implement getJWTCustomClaims() method.
        return [];
    }
}
