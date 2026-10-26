<?php

namespace Tests\Feature;

use App\Filament\Exports\ReportDocxExporter;
use App\Models\Asset;
use App\Models\Building;
use App\Models\Classroom;
use App\Models\Report;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use ZipArchive;

class ReportDocxExportTest extends TestCase
{
    use RefreshDatabase;

    public function test_report_export_downloads_a_docx_containing_report_data(): void
    {
        $building = Building::factory()->create();
        $classroom = Classroom::factory()->create([
            'building_id' => $building->id,
            'floor' => 1,
        ]);
        $asset = Asset::factory()->create([
            'building_id' => $building->id,
            'classroom_id' => $classroom->id,
            'name' => 'Test laptop',
        ]);
        $reporter = User::factory()->create(['name' => 'Test reporter']);

        $report = Report::factory()->create([
            'asset_id' => $asset->id,
            'reporter_id' => $reporter->id,
            'description' => "Broken screen <urgent>\nNeeds repair",
            'status' => 'in_progress',
        ]);

        $response = app(ReportDocxExporter::class)->download(Report::query());
        $this->assertSame(
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            $response->headers->get('Content-Type'),
        );
        $this->assertSame('attachment; filename=reports.docx', $response->headers->get('Content-Disposition'));

        ob_start();
        $response->getCallback()();
        $document = ob_get_clean();
        $path = tempnam(sys_get_temp_dir(), 'reports-test-');
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
        $this->assertStringContainsString('Reports', $xml);
        $this->assertStringContainsString('Test laptop', $xml);
        $this->assertStringContainsString('Test reporter', $xml);
        $this->assertStringContainsString((string) $report->id, $xml);
        $this->assertStringContainsString('Broken screen &lt;urgent&gt;', $xml);
        $this->assertStringContainsString('Needs repair', $xml);
        $this->assertStringContainsString('In Progress', $xml);
    }
}
