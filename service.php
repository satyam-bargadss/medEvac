<?php

namespace App;

use Illuminate\Database\Eloquent\Model;


class service extends Model
{
    protected $primaryKey = 'serviceId';
    protected $fillable = ['serviceName', 'serviceDesc'];
}
