<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class plan extends Model
{
    protected $fillable = ['planName', 'frequency','fee','monthlyFee','initiatonFee','modDate','modDate'];
	

}
