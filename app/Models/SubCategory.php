<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SubCategory extends Model {

    protected $table = 'subcategory';
    protected $fillable = ['category_id', 'title'];

    const UPDATED_AT = null;

}