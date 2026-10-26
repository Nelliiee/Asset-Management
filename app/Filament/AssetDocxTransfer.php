<?php

namespace App\Filament;

use App\Models\Asset;
use App\Models\Brand;
use App\Models\Building;
use App\Models\Category;
use App\Models\Classroom;
use Carbon\CarbonImmutable;
use DOMDocument;
use DOMXPath;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use RuntimeException;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Throwable;
use ZipArchive;

class AssetDocxTransfer
{
    private const MAX_DOCUMENT_XML_BYTES = 10_485_760;

    private const MAX_IMPORT_ROWS = 5000;

    /**
     * @param  Builder<Asset>  $query
     */
    public function download(Builder $query): StreamedResponse
    {
        $path = $this->createDocument($query->with(['category', 'brand', 'classroom', 'building'])->get());

        return response()->streamDownload(function () use ($path): void {
            try {
                if (readfile($path) === false) {
                    throw new RuntimeException('Unable to read the generated asset document.');
                }
            } finally {
                if (file_exists($path) && ! unlink($path)) {
                    throw new RuntimeException('Unable to remove the generated asset document.');
                }
            }
        }, 'assets.docx', [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        ]);
    }

    public function import(string $path): int
    {
        $rows = $this->readRows($path);

        if (count($rows) < 2) {
            throw ValidationException::withMessages([
                'file' => 'The DOCX must contain a header row and at least one asset row.',
            ]);
        }

        if (count($rows) > self::MAX_IMPORT_ROWS + 1) {
            throw ValidationException::withMessages([
                'file' => 'The DOCX can contain no more than '.number_format(self::MAX_IMPORT_ROWS).' assets.',
            ]);
        }

        $headers = array_map(fn (string $header): string => str($header)->trim()->lower()->toString(), array_shift($rows));
        $duplicateHeaders = array_keys(array_filter(
            array_count_values($headers),
            fn (int $count): bool => $count > 1,
        ));

        if ($duplicateHeaders !== []) {
            throw ValidationException::withMessages([
                'file' => 'The DOCX contains duplicate columns: '.implode(', ', $duplicateHeaders).'.',
            ]);
        }

        $requiredHeaders = ['category', 'brand', 'name', 'serial number', 'classroom', 'building', 'status'];
        $missingHeaders = array_diff($requiredHeaders, $headers);

        if ($missingHeaders !== []) {
            throw ValidationException::withMessages([
                'file' => 'The DOCX is missing required columns: '.implode(', ', $missingHeaders).'.',
            ]);
        }

        $columnIndexes = array_flip($headers);
        $categories = $this->indexNames(Category::query()->get(['id', 'name']));
        $brands = $this->indexNames(Brand::query()->get(['id', 'name']));
        $buildings = $this->indexNames(Building::query()->get(['id', 'name']));
        $classrooms = Classroom::query()
            ->with('building:id,name')
            ->get(['id', 'building_id', 'room_number'])
            ->groupBy(fn (Classroom $classroom): string => $this->relationshipKey(
                (string) $classroom->building?->name,
                $classroom->room_number,
            ));

        $assets = [];
        $errors = [];
        $seenSerialNumbers = [];

        foreach ($rows as $index => $row) {
            $rowNumber = $index + 2;

            if (collect($row)->every(fn (string $value): bool => trim($value) === '')) {
                continue;
            }

            $values = [];

            foreach ($requiredHeaders as $header) {
                $values[$header] = trim($row[$columnIndexes[$header]] ?? '');
            }

            foreach (['expiry date'] as $optionalHeader) {
                $values[$optionalHeader] = trim($row[$columnIndexes[$optionalHeader]] ?? '');
            }

            foreach (['category', 'brand', 'name', 'serial number', 'classroom', 'building', 'status'] as $requiredHeader) {
                if ($values[$requiredHeader] === '') {
                    $errors[] = "Row {$rowNumber}: {$requiredHeader} is required.";
                }
            }

            if ($values['category'] !== '' && ! isset($categories[$this->normalizeName($values['category'])])) {
                $errors[] = "Row {$rowNumber}: category \"{$values['category']}\" was not found.";
            }

            if ($values['brand'] !== '' && ! isset($brands[$this->normalizeName($values['brand'])])) {
                $errors[] = "Row {$rowNumber}: brand \"{$values['brand']}\" was not found.";
            }

            if ($values['building'] !== '' && ! isset($buildings[$this->normalizeName($values['building'])])) {
                $errors[] = "Row {$rowNumber}: building \"{$values['building']}\" was not found.";
            }

            $classroomMatches = $classrooms[$this->relationshipKey($values['building'], $values['classroom'])] ?? collect();

            if ($values['classroom'] !== '' && $values['building'] !== '' && $classroomMatches->count() !== 1) {
                $errors[] = "Row {$rowNumber}: classroom \"{$values['classroom']}\" was not found uniquely in building \"{$values['building']}\".";
            }

            $status = $this->normalizeStatus($values['status']);

            if ($values['status'] !== '' && $status === null) {
                $errors[] = "Row {$rowNumber}: status must be Available, In Use, Maintenance, or For Disposal.";
            }

            $serialNumber = $values['serial number'];

            if ($serialNumber !== '') {
                $serialKey = $this->normalizeName($serialNumber);

                if (isset($seenSerialNumbers[$serialKey])) {
                    $errors[] = "Row {$rowNumber}: serial number \"{$serialNumber}\" is duplicated in this DOCX.";
                } elseif (Asset::query()->where('serial_number', $serialNumber)->exists()) {
                    $errors[] = "Row {$rowNumber}: serial number \"{$serialNumber}\" already exists.";
                }

                $seenSerialNumbers[$serialKey] = true;
            }

            $expiryDate = null;

            if ($values['expiry date'] !== '') {
                try {
                    $expiryDate = CarbonImmutable::parse($values['expiry date']);
                } catch (Throwable) {
                    $errors[] = "Row {$rowNumber}: expiry date is invalid.";
                }
            }

            if (
                $values['category'] !== ''
                && isset($categories[$this->normalizeName($values['category'])])
                && $values['brand'] !== ''
                && isset($brands[$this->normalizeName($values['brand'])])
                && $values['building'] !== ''
                && isset($buildings[$this->normalizeName($values['building'])])
                && $classroomMatches->count() === 1
                && $values['name'] !== ''
                && $serialNumber !== ''
                && $status !== null
                && ($values['expiry date'] === '' || $expiryDate !== null)
            ) {
                $assets[] = [
                    'category_id' => $categories[$this->normalizeName($values['category'])]['id'],
                    'brand_id' => $brands[$this->normalizeName($values['brand'])]['id'],
                    'building_id' => $buildings[$this->normalizeName($values['building'])]['id'],
                    'classroom_id' => $classroomMatches->first()->id,
                    'name' => $values['name'],
                    'serial_number' => $serialNumber,
                    'expiry_date' => $expiryDate,
                    'status' => $status,
                ];
            }
        }

        if ($errors !== []) {
            throw ValidationException::withMessages([
                'file' => implode("\n", $errors),
            ]);
        }

        if ($assets === []) {
            throw ValidationException::withMessages([
                'file' => 'The DOCX does not contain any asset rows to import.',
            ]);
        }

        DB::transaction(function () use ($assets): void {
            foreach ($assets as $attributes) {
                Asset::query()->create($attributes);
            }
        });

        return count($assets);
    }

    /**
     * @param  iterable<Asset>  $assets
     */
    private function createDocument(iterable $assets): string
    {
        $this->ensureZipSupport();

        $path = tempnam(sys_get_temp_dir(), 'assets-');

        if ($path === false) {
            throw new RuntimeException('Unable to create a temporary asset document.');
        }

        $archive = new ZipArchive;

        if ($archive->open($path, ZipArchive::OVERWRITE) !== true) {
            if (file_exists($path) && ! unlink($path)) {
                throw new RuntimeException('Unable to remove the incomplete asset document.');
            }

            throw new RuntimeException('Unable to create the DOCX archive.');
        }

        $archiveClosed = false;

        try {
            $entries = [
                '[Content_Types].xml' => $this->contentTypesXml(),
                '_rels/.rels' => $this->relationshipsXml(),
                'word/document.xml' => $this->documentXml($assets),
            ];

            foreach ($entries as $name => $contents) {
                if (! $archive->addFromString($name, $contents)) {
                    throw new RuntimeException("Unable to add {$name} to the DOCX archive.");
                }
            }

            $wasClosed = $archive->close();
            $archiveClosed = true;

            if (! $wasClosed) {
                throw new RuntimeException('Unable to finish the DOCX archive.');
            }
        } catch (Throwable $exception) {
            if (! $archiveClosed) {
                $archive->close();
            }

            if (file_exists($path) && ! unlink($path)) {
                throw new RuntimeException('Unable to remove the incomplete asset document.', previous: $exception);
            }

            throw $exception;
        }

        return $path;
    }

    /**
     * @param  iterable<Asset>  $assets
     */
    private function documentXml(iterable $assets): string
    {
        $headers = ['Asset Code', 'Category', 'Brand', 'Name', 'Serial Number', 'Classroom', 'Building', 'Status', 'Expiry Date'];
        $rows = '<w:tr>'.implode('', array_map(
            fn (string $header): string => $this->tableCell($header, true),
            $headers,
        )).'</w:tr>';

        foreach ($assets as $asset) {
            $rows .= '<w:tr>'.implode('', array_map(
                fn (string $value): string => $this->tableCell($value),
                [
                    (string) $asset->asset_code,
                    (string) ($asset->category?->name ?? ''),
                    (string) ($asset->brand?->name ?? ''),
                    (string) $asset->name,
                    (string) $asset->serial_number,
                    (string) ($asset->classroom?->room_number ?? ''),
                    (string) ($asset->building?->name ?? ''),
                    str($asset->status)->replace('_', ' ')->title()->toString(),
                    $asset->expiry_date?->format('Y-m-d') ?? '',
                ],
            )).'</w:tr>';
        }

        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            .'<w:document xmlns:w="http://schemas.openxmlformats.org/wordprocessingml/2006/main">'
            .'<w:body>'.$this->paragraph('Assets', true, 32)
            .'<w:tbl><w:tblPr><w:tblW w:w="0" w:type="auto"/>'
            .'<w:tblBorders>'
            .'<w:top w:val="single" w:sz="4" w:space="0" w:color="B7C9D6"/>'
            .'<w:left w:val="single" w:sz="4" w:space="0" w:color="B7C9D6"/>'
            .'<w:bottom w:val="single" w:sz="4" w:space="0" w:color="B7C9D6"/>'
            .'<w:right w:val="single" w:sz="4" w:space="0" w:color="B7C9D6"/>'
            .'<w:insideH w:val="single" w:sz="4" w:space="0" w:color="B7C9D6"/>'
            .'<w:insideV w:val="single" w:sz="4" w:space="0" w:color="B7C9D6"/>'
            .'</w:tblBorders></w:tblPr>'.$rows.'</w:tbl>'
            .'<w:sectPr><w:pgSz w:w="15840" w:h="12240" w:orient="landscape"/>'
            .'<w:pgMar w:top="720" w:right="720" w:bottom="720" w:left="720"/></w:sectPr>'
            .'</w:body></w:document>';
    }

    private function tableCell(string $value, bool $bold = false): string
    {
        return '<w:tc><w:tcPr><w:tcW w:w="0" w:type="auto"/></w:tcPr>'
            .$this->paragraph($value, $bold)
            .'</w:tc>';
    }

    private function paragraph(string $value, bool $bold = false, ?int $fontSize = null): string
    {
        $runs = [];
        $lines = explode("\n", str_replace(["\r\n", "\r"], "\n", mb_scrub($value, 'UTF-8')));

        foreach ($lines as $index => $line) {
            if ($index > 0) {
                $runs[] = '<w:r><w:br/></w:r>';
            }

            $properties = ($bold ? '<w:b/>' : '').($fontSize ? '<w:sz w:val="'.$fontSize.'"/>' : '');
            $runs[] = '<w:r>'.($properties ? '<w:rPr>'.$properties.'</w:rPr>' : '')
                .'<w:t xml:space="preserve">'.$this->escapeXmlText($line).'</w:t></w:r>';
        }

        return '<w:p>'.implode('', $runs).'</w:p>';
    }

    private function readRows(string $path): array
    {
        $this->ensureZipSupport();

        if (! is_file($path)) {
            throw ValidationException::withMessages(['file' => 'The uploaded DOCX file could not be found.']);
        }

        $archive = new ZipArchive;

        if ($archive->open($path) !== true) {
            throw ValidationException::withMessages(['file' => 'The uploaded file is not a valid DOCX document.']);
        }

        try {
            $documentIndex = $archive->locateName('word/document.xml');

            if ($documentIndex === false) {
                throw ValidationException::withMessages(['file' => 'The uploaded DOCX does not contain a Word document.']);
            }

            $documentInfo = $archive->statIndex($documentIndex);

            if (($documentInfo['size'] ?? self::MAX_DOCUMENT_XML_BYTES + 1) > self::MAX_DOCUMENT_XML_BYTES) {
                throw ValidationException::withMessages(['file' => 'The DOCX document content is too large to import.']);
            }

            $xml = $archive->getFromIndex($documentIndex);

            if ($xml === false) {
                throw ValidationException::withMessages(['file' => 'Unable to read the contents of the DOCX document.']);
            }
        } finally {
            $archive->close();
        }

        $document = new DOMDocument;
        $previousUseErrors = libxml_use_internal_errors(true);

        try {
            if (! $document->loadXML($xml, LIBXML_NONET | LIBXML_NOBLANKS)) {
                throw ValidationException::withMessages(['file' => 'The DOCX contains invalid Word document XML.']);
            }
        } finally {
            libxml_clear_errors();
            libxml_use_internal_errors($previousUseErrors);
        }

        $xpath = new DOMXPath($document);
        $xpath->registerNamespace('w', 'http://schemas.openxmlformats.org/wordprocessingml/2006/main');
        $tableRows = $xpath->query('//w:tbl[1]/w:tr');

        if ($tableRows === false || $tableRows->length === 0) {
            throw ValidationException::withMessages(['file' => 'The DOCX must contain an asset table.']);
        }

        $rows = [];

        foreach ($tableRows as $tableRow) {
            $cells = $xpath->query('./w:tc', $tableRow);
            $values = [];

            if ($cells === false) {
                continue;
            }

            foreach ($cells as $cell) {
                $textNodes = $xpath->query('.//w:t', $cell);
                $text = '';

                if ($textNodes !== false) {
                    foreach ($textNodes as $textNode) {
                        $text .= $textNode->textContent;
                    }
                }

                $values[] = $text;
            }

            $rows[] = $values;
        }

        return $rows;
    }

    /**
     * @param  iterable<Category|Brand|Building>  $records
     * @return array<string, array{id: int, name: string}>
     */
    private function indexNames(iterable $records): array
    {
        $indexed = [];

        foreach ($records as $record) {
            $key = $this->normalizeName($record->name);

            if (isset($indexed[$key])) {
                $indexed[$key] = null;

                continue;
            }

            $indexed[$key] = ['id' => $record->id, 'name' => $record->name];
        }

        return array_filter($indexed);
    }

    private function relationshipKey(string $building, string $classroom): string
    {
        return $this->normalizeName($building).'|'.$this->normalizeName($classroom);
    }

    private function normalizeName(string $value): string
    {
        return mb_strtolower(trim($value));
    }

    private function normalizeStatus(string $status): ?string
    {
        $normalized = str($status)->trim()->lower()->replace([' ', '-'], '_')->toString();

        return in_array($normalized, ['available', 'in_use', 'maintenance', 'for_disposal'], true)
            ? $normalized
            : null;
    }

    private function escapeXmlText(string $value): string
    {
        $value = preg_replace(
            '/[^\x{9}\x{A}\x{D}\x{20}-\x{D7FF}\x{E000}-\x{FFFD}\x{10000}-\x{10FFFF}]/u',
            '',
            $value,
        );

        if ($value === null) {
            throw new RuntimeException('Unable to encode asset text in the DOCX document.');
        }

        return htmlspecialchars($value, ENT_QUOTES | ENT_XML1, 'UTF-8');
    }

    private function contentTypesXml(): string
    {
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            .'<Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types">'
            .'<Default Extension="rels" ContentType="application/vnd.openxmlformats-package.relationships+xml"/>'
            .'<Default Extension="xml" ContentType="application/xml"/>'
            .'<Override PartName="/word/document.xml" ContentType="application/vnd.openxmlformats-officedocument.wordprocessingml.document.main+xml"/>'
            .'</Types>';
    }

    private function relationshipsXml(): string
    {
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            .'<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">'
            .'<Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument" Target="word/document.xml"/>'
            .'</Relationships>';
    }

    private function ensureZipSupport(): void
    {
        if (! class_exists(ZipArchive::class)) {
            throw new RuntimeException('The PHP zip extension is required to import and export DOCX files.');
        }
    }
}
