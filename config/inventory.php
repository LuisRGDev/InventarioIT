<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Warranty Warning Days
    |--------------------------------------------------------------------------
    |
    | Number of days before warranty expiration to show a warning.
    |
    */
    'warranty_warning_days' => (int) env('INVENTORY_WARRANTY_WARNING_DAYS', 30),

    /*
    |--------------------------------------------------------------------------
    | Computer Category Slugs
    |--------------------------------------------------------------------------
    |
    | Slugs that identify a device category as a computer type.
    |
    */
    'computer_slugs' => ['portatil', 'desktop'],

    /*
    |--------------------------------------------------------------------------
    | Smartphone Category Slug
    |--------------------------------------------------------------------------
    |
    | Slug that identifies a device category as a smartphone type.
    |
    */
    'smartphone_slug' => 'smartphone',

    /*
    |--------------------------------------------------------------------------
    | Import Row Limit
    |--------------------------------------------------------------------------
    |
    | Maximum number of rows allowed in a single import file.
    |
    */
    'import_max_rows' => (int) env('INVENTORY_IMPORT_MAX_ROWS', 5000),

    /*
    |--------------------------------------------------------------------------
    | Temp File Cleanup (Hours)
    |--------------------------------------------------------------------------
    |
    | Hours after which temp files (e.g., carta responsiva) are deleted.
    |
    */
    'temp_file_max_age_hours' => (int) env('INVENTORY_TEMP_FILE_MAX_AGE_HOURS', 1),

];
