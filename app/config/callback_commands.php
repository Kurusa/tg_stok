<?php

use App\Commands\Callback\AcceptUser;

return [
    'accept_user' => AcceptUser::class,
    'cart_back' => \App\Commands\CategoryList::class,
    'category' => \App\Commands\SubCategoryList::class,
    'subcat_back' => \App\Commands\CategoryList::class,
    'product_back' => \App\Commands\SubCategoryList::class,
    'subcat' => \App\Commands\ProductList::class,
    'product' => \App\Commands\SelectProductAmount::class,
    'prod_back' => \App\Commands\ProductList::class,
    'prod_am' => \App\Commands\AddProductToCart::class,
    'cart' => \App\Commands\DisplayCart::class,
    'delete' => \App\Commands\DeleteProductFromOrder::class,
    'finish_order' => \App\Commands\FinishOrder::class,
    'left_order' => \App\Commands\Contragent::class,
];