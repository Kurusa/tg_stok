<?php

use App\Commands\Callback\AcceptUser;

return [
    'accept_user' => AcceptUser::class,
    'category' => \App\Commands\SubCategoryList::class,
    'subcat_back' => \App\Commands\CategoryList::class,
    'product_back' => \App\Commands\SubCategoryList::class,
    'subcat' => \App\Commands\ProductList::class,
    'product' => \App\Commands\SelectProductAmount::class,
    'prod_back' => \App\Commands\ProductList::class,
];