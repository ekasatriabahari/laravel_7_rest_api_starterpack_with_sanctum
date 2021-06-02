<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    //determine fillable columns
    protected $fillable = [
        'name', 'detail',
    ];
}
