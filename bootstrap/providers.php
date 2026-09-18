<?php

use App\Providers\AppServiceProvider;
use App\Providers\VoltServiceProvider;
use Maatwebsite\Excel\ExcelServiceProvider;

return [
    AppServiceProvider::class,
    VoltServiceProvider::class,
    ExcelServiceProvider::class,
];
