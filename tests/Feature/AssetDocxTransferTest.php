<?php

namespace Tests\Feature;

use App\Filament\AssetDocxTransfer;
use App\Models\Asset;
use App\Models\Brand;
use App\Models\Building;
use App\Models\Category;
use App\Models\Classroom;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;
use ZipArchive;

class AssetDocxTransferTest extends TestCase
{
    use RefreshDatabase;

    public function test_asset_export_downloads_a_docx_with_asset_data(): void
    {
        [$category, $brand, $building, $classroom] = $this->createRelationships();
        Asset::factory()->create([
            'category_id' => $category->id,
            'brand_id' => $brand->id,
            'building_id' => $building->id,
            'classroom_id' => $classroom->id,
            'name' => 'Test laptop',
            'serial_number' => 'SN-TEST-001',
            'status' => 'in_use',
        ]);

        $response = app(AssetDocxTransfer::class)->download(Asset::query());

        $this->assertSame(
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            $response->headers->get('Content-Type'),
        );
        $this->assertSame('attachment; filename=assets.docx', $response->headers->get('Content-Disposition'));

        ob_start();
        $response->getCallback()();
        $document = ob_get_clean();
        $path = tempnam(sys_get_temp_dir(), 'assets-test-');
        $this->assertNotFalse($path);
        file_put_contents($path, $document);

        try {
            $archive = new ZipArchive;
            $this->assertTrue($archive->open($path));
            $xml = $archive->getFromName('word/document.xml');
            $archive->close();
        } finally {
            unlink($path);
        }

        $this->assertIsString($xml);
        $xmlDocument = new \DOMDocument;
        $this->assertTrue($xmlDocument->loadXML($xml));
        $this->assertStringContainsString('Test laptop', $xml);
        $this->assertStringContainsString('SN-TEST-001', $xml);
        $this->assertStringContainsString($category->name, $xml);
        $this->assertStringContainsString($brand->name, $xml);
        $this->assertStringContainsString($classroom->room_number, $xml);
        $this->assertStringContainsString($building->name, $xml);
        $this->assertStringContainsString('In Use', $xml);
    }

    public function test_asset_import_reads_docx_table_rows_and_creates_assets(): void
    {
        [$category, $brand, $building, $classroom] = $this->createRelationships();
        $path = $this->createDocx([
            ['Category', 'Brand', 'Name', 'Serial Number', 'Classroom', 'Building', 'Status', 'Expiry Date'],
            [$category->name, $brand->name, 'Imported laptop', 'SN-IMPORT-001', $classroom->room_number, $building->name, 'In Use', '2027-12-31'],
        ]);

        try {
            $count = app(AssetDocxTransfer::class)->import($path);
        } finally {
            unlink($path);
        }

        $asset = Asset::query()->where('serial_number', 'SN-IMPORT-001')->firstOrFail();

        $this->assertSame(1, $count);
        $this->assertSame('Imported laptop', $asset->name);
        $this->assertSame($category->id, $asset->category_id);
        $this->assertSame($brand->id, $asset->brand_id);
        $this->assertSame($classroom->id, $asset->classroom_id);
        $this->assertSame($building->id, $asset->building_id);
        $this->assertSame('in_use', $asset->status);
        $this->assertSame('2027-12-31', $asset->expiry_date->toDateString());
    }

    public function test_invalid_asset_docx_rows_are_rejected_without_partial_import(): void
    {
        $path = $this->createDocx([
            ['Category', 'Brand', 'Name', 'Serial Number', 'Classroom', 'Building', 'Status', 'Expiry Date'],
            ['Missing category', 'Missing brand', 'Invalid asset', 'SN-INVALID-001', 'Unknown room', 'Unknown building', 'Unknown status', ''],
        ]);

        try {
            try {
                app(AssetDocxTransfer::class)->import($path);
                $this->fail('Invalid asset rows should not be imported.');
            } catch (ValidationException $exception) {
                $this->assertStringContainsString('category', $exception->errors()['file'][0]);
                $this->assertStringContainsString('status', $exception->errors()['file'][0]);
            }
        } finally {
            unlink($path);
        }

        $this->assertDatabaseMissing('assets', ['serial_number' => 'SN-INVALID-001']);
    }

    /**
     * @return array{Category, Brand, Building, Classroom}
     */
    private function createRelationships(): array
    {
        $category = Category::factory()->create(['name' => 'Computers']);
        $brand = Brand::factory()->create(['name' => 'Acme']);
        $building = Building::factory()->create(['name' => 'Main Building']);
        $classroom = Classroom::factory()->create([
            'building_id' => $building->id,
            'room_number' => 'A-101',
            'floor' => 1,
        ]);

        return [$category, $brand, $building, $classroom];
    }

    /**
     * @param  array<int, array<int, string>>  $rows
     */
    private function createDocx(array $rows): string
    {
        $xmlRows = '';

        foreach ($rows as $row) {
            $xmlRows .= '<w:tr>';

            foreach ($row as $cell) {
                $xmlRows .= '<w:tc><w:p><w:r><w:t xml:space="preserve">'
                    .htmlspecialchars($cell, ENT_QUOTES | ENT_XML1, 'UTF-8')
                    .'</w:t></w:r></w:p></w:tc>';
            }

            $xmlRows .= '</w:tr>';
        }

        $document = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            .'<w:document xmlns:w="http://schemas.openxmlformats.org/wordprocessingml/2006/main">'
            .'<w:body><w:tbl>'.$xmlRows.'</w:tbl></w:body></w:document>';
        $path = tempnam(sys_get_temp_dir(), 'assets-import-test-');
        $this->assertNotFalse($path);

        $archive = new ZipArchive;
        $this->assertTrue($archive->open($path, ZipArchive::OVERWRITE));
        $this->assertTrue($archive->addFromString('word/document.xml', $document));
        $this->assertTrue($archive->close());

        return $path;
    }
}
