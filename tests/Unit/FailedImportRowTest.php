<?php

namespace Apriansyahrs\ImportExcel\Tests\Unit;

use Apriansyahrs\ImportExcel\Models\FailedImportRow;
use Apriansyahrs\ImportExcel\Models\Import;
use Apriansyahrs\ImportExcel\Tests\Importers\TestUserImporter;
use Apriansyahrs\ImportExcel\Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class FailedImportRowTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_create_a_failed_import_row()
    {
        $import = Import::create([
            'file_name' => 'test.xlsx',
            'file_path' => '/tmp/test.xlsx',
            'importer' => TestUserImporter::class,
            'total_rows' => 10,
        ]);

        $failedRow = FailedImportRow::create([
            'import_id' => $import->id,
            'data' => ['name' => 'John', 'email' => 'invalid-email'],
            'validation_errors' => ['email' => ['The email must be a valid email address.']],
            'error' => 'Validation failed',
        ]);

        $this->assertInstanceOf(FailedImportRow::class, $failedRow);
        $this->assertEquals($import->id, $failedRow->import_id);
        $this->assertEquals(['name' => 'John', 'email' => 'invalid-email'], $failedRow->data);
        $this->assertEquals(['email' => ['The email must be a valid email address.']], $failedRow->validation_errors);
        $this->assertEquals('Validation failed', $failedRow->error);
    }

    /** @test */
    public function it_belongs_to_an_import()
    {
        $import = Import::create([
            'file_name' => 'test.xlsx',
            'file_path' => '/tmp/test.xlsx',
            'importer' => TestUserImporter::class,
            'total_rows' => 10,
        ]);

        $failedRow = FailedImportRow::create([
            'import_id' => $import->id,
            'data' => ['name' => 'John', 'email' => 'invalid-email'],
            'validation_errors' => ['email' => ['The email must be a valid email address.']],
            'error' => 'Validation failed',
        ]);

        $this->assertInstanceOf(Import::class, $failedRow->import);
        $this->assertEquals($import->id, $failedRow->import->id);
    }

    /** @test */
    public function it_casts_data_and_validation_errors_to_arrays()
    {
        $import = Import::create([
            'file_name' => 'test.xlsx',
            'file_path' => '/tmp/test.xlsx',
            'importer' => TestUserImporter::class,
            'total_rows' => 10,
        ]);

        $failedRow = FailedImportRow::create([
            'import_id' => $import->id,
            'data' => ['name' => 'John', 'email' => 'invalid-email'],
            'validation_errors' => ['email' => ['The email must be a valid email address.']],
            'error' => 'Validation failed',
        ]);

        $this->assertIsArray($failedRow->data);
        $this->assertIsArray($failedRow->validation_errors);
    }

    /** @test */
    public function it_can_handle_null_validation_errors()
    {
        $import = Import::create([
            'file_name' => 'test.xlsx',
            'file_path' => '/tmp/test.xlsx',
            'importer' => TestUserImporter::class,
            'total_rows' => 10,
        ]);

        $failedRow = FailedImportRow::create([
            'import_id' => $import->id,
            'data' => ['name' => 'John', 'email' => 'john@example.com'],
            'validation_errors' => null,
            'error' => 'Database error',
        ]);

        $this->assertNull($failedRow->validation_errors);
        $this->assertEquals('Database error', $failedRow->error);
    }
}
