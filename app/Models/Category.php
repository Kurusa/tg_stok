<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model {

    protected $table = 'category';
    protected $fillable = ['title'];

    const UPDATED_AT = null;

}