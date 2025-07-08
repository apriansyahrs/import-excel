<?php

namespace Apriansyahrs\ImportExcel\Tests;

use Apriansyahrs\ImportExcel\ImportExcelServiceProvider;
use Filament\FilamentServiceProvider;
use Illuminate\Database\Eloquent\Factories\Factory;
use Livewire\LivewireServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;

class TestCase extends Orchestra
{
    protected function setUp(): void
    {
        parent::setUp();

        Factory::guessFactoryNamesUsing(
            fn (string $modelName) => 'Apriansyahrs\\ImportExcel\\Database\\Factories\\'.class_basename($modelName).'Factory'
        );
    }

    protected function getPackageProviders($app)
    {
        return [
            LivewireServiceProvider::class,
            FilamentServiceProvider::class,
            ImportExcelServiceProvider::class,
        ];
    }

    public function getEnvironmentSetUp($app)
    {
        config()->set('database.default', 'testing');
        config()->set('database.connections.testing', [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
        ]);

        // Set up Filament configuration
        config()->set('filament.default_filesystem_disk', 'local');
        config()->set('app.key', 'base64:'.base64_encode(random_bytes(32)));

        // Run the migrations
        $migration = include __DIR__.'/../database/migrations/2025_05_20_121526_create_imports_table.php';
        $migration->up();

        $migration = include __DIR__.'/../database/migrations/2025_05_20_121527_create_failed_import_rows_table.php';
        $migration->up();

        // Create users table for testing
        $app['db']->connection()->getSchemaBuilder()->create('users', function ($table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('password');
            $table->timestamps();
        });
    }
}
