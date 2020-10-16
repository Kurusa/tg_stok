<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model {

    protected $table = 'product';
    protected $fillable = ['title', 'category_id', 'subcategory_id', 'title', 'price', 'amount'];

}