<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class plan extends Model
{

    protected $primaryKey = 'planId';
    protected $fillable = ['planName', 'frequency','fee','monthlyFee','initiatonFee','modDate','modDate'];
	

}
