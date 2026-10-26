<?php

namespace App\Filament\Exports;

use App\Models\Report;
use Illuminate\Database\Eloquent\Builder;
use RuntimeException;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Throwable;
use ZipArchive;

class ReportDocxExporter
{
    /**
     * @param  Builder<Report>  $query
     */
    public function download(Builder $query): StreamedResponse
    {
        $path = $this->createDocument($query->with(['asset', 'reporter'])->get());

        return response()->streamDownload(function () use ($path): void {
            try {
                if (readfile($path) === false) {
                    throw new RuntimeException('Unable to read the generated report document.');
                }
            } finally {
                if (file_exists($path) && ! unlink($path)) {
                    throw new RuntimeException('Unable to remove the generated report document.');
                }
            }
        }, 'reports.docx', [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        ]);
    }

    /**
     * @param  iterable<Report>  $reports
     */
    private function createDocument(iterable $reports): string
    {
        if (! class_exists(ZipArchive::class)) {
            throw new RuntimeException('The PHP zip extension is required to export reports as DOCX.');
        }

        $path = tempnam(sys_get_temp_dir(), 'reports-');

        if ($path === false) {
            throw new RuntimeException('Unable to create a temporary report document.');
        }

        $archive = new ZipArchive;
        $openResult = $archive->open($path, ZipArchive::OVERWRITE);

        if ($openResult !== true) {
            if (file_exists($path) && ! unlink($path)) {
                throw new RuntimeException('Unable to remove the incomplete report document.');
            }

            throw new RuntimeException('Unable to create the DOCX archive.');
        }

        $archiveClosed = false;

        try {
            $entries = [
                '[Content_Types].xml' => $this->contentTypesXml(),
                '_rels/.rels' => $this->relationshipsXml(),
                'word/document.xml' => $this->documentXml($reports),
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
                throw new RuntimeException('Unable to remove the incomplete report document.', previous: $exception);
            }

            throw $exception;
        }

        return $path;
    }

    /**
     * @param  iterable<Report>  $reports
     */
    private function documentXml(iterable $reports): string
    {
        $headers = ['ID', 'Asset', 'Reporter', 'Description', 'Status', 'Created At', 'Updated At'];
        $rows = '<w:tr>'.implode('', array_map(
            fn (string $header): string => $this->tableCell($header, true),
            $headers,
        )).'</w:tr>';

        foreach ($reports as $report) {
            $rows .= '<w:tr>'.implode('', array_map(
                fn (string $value): string => $this->tableCell($value),
                [
                    (string) $report->getKey(),
                    (string) ($report->asset?->name ?? ''),
                    (string) ($report->reporter?->name ?? ''),
                    (string) ($report->description ?? ''),
                    str($report->status)->replace('_', ' ')->title()->toString(),
                    $report->created_at?->format('Y-m-d H:i:s') ?? '',
                    $report->updated_at?->format('Y-m-d H:i:s') ?? '',
                ],
            )).'</w:tr>';
        }

        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            .'<w:document xmlns:w="http://schemas.openxmlformats.org/wordprocessingml/2006/main">'
            .'<w:body>'
            .$this->paragraph('Reports', true, 32)
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

    private function escapeXmlText(string $value): string
    {
        $value = preg_replace(
            '/[^\x{9}\x{A}\x{D}\x{20}-\x{D7FF}\x{E000}-\x{FFFD}\x{10000}-\x{10FFFF}]/u',
            '',
            $value,
        );

        if ($value === null) {
            throw new RuntimeException('Unable to encode report text in the DOCX document.');
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
}
