<?php

//namespace App;
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
//use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Agent extends Authenticatable
{
    use Notifiable;

        protected $guard = 'agent';

        protected $fillable = [
            'agentName', 'address1', 'address2','email','city','country','api_token'
        ];

        protected $hidden = [
            'password', 'remember_token',
        ];
}
