<?php

use App\Models\Account;
use App\Models\Category;
use App\Models\Group;
use App\Models\Transaction;

return [
    'user_model' => \App\Models\User::class,
    'user_foreign_key_column_name' => 'user_id',
    'active_models' => [
        Account::class,
        Category::class,
        Group::class,
        Transaction::class,
    ]
];
