<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

//class User extends Authenticatable
class User extends Model
{
    use HasFactory;
    protected $fillable = [
        'first_name',
        'last_name',
        'phone_number',
        'birthday',
        'admin'
    ];

    protected $attributes = [
        'admin' => 'no'
    ];

    public function addresses(){
        return $this->hasMany(UserAddress::class);
    }

    public function orders(){
        return $this->hasMany(Order::class);
    }

}
