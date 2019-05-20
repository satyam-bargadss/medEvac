<?php

namespace App;
//namespace App\Models;
use Illuminate\Database\Eloquent\Model;
//use Illuminate\Notifications\Notifiable;
//use Illuminate\Foundation\Auth\User as Authenticatable;
//use Illuminate\Foundation\Auth\User as Authenticatable;
//use Illuminate\Contracts\Auth\Authenticatable;
class Agent  extends Model
{
   // use Notifiable;

       // protected $guard = 'agent-api';

        protected $fillable = [
            'agentName', 'address1', 'address2','email','city','country','api_token'
        ];

        protected $hidden = [
            'password', 'remember_token',
        ];
}
