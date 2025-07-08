<?php

declare(strict_types=1);

use Apriansyahrs\ImportExcel\Tests\TestCase;

uses(
    TestCase::class,
    // Illuminate\Foundation\Testing\RefreshDatabase::class,
)->in('Feature');

uses(TestCase::class)->in('Unit');

/*
expect()->extend('toBeOne', function () {
    return $this->toBe(1);
});
*/

/*
|--------------------------------------------------------------------------
| Functions
|--------------------------------------------------------------------------
|
| While Pest is very powerful out-of-the-box, you may have some testing code
| specific to your project that you don't want to repeat in every file.
| Here you can also expose helpers as global functions to help you to
| reduce the amount of code duplication.
|
*/
