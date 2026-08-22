<?php

namespace App\Services;

use App\Support\SpreadsheetSanitizer;
use ZipArchive;

class AmiXlsxExportService
{
    public function download(array $sheets, string $filename): string
    {
        $path = tempnam(sys_get_temp_dir(), 'ami_xlsx_');
        $zip = new ZipArchive();
        $zip->open($path, ZipArchive::CREATE | ZipArchive::OVERWRITE);

        $sheetFiles = [];
        foreach ($sheets as $index => $sheet) {
            $sheetFiles[] = 'worksheets/sheet'.($index + 1).'.xml';
        }

        $zip->addFromString('[Content_Types].xml', $this->contentTypes($sheetFiles));
        $zip->addFromString('_rels/.rels', $this->rels());
        $zip->addFromString('docProps/app.xml', $this->appProps($sheets));
        $zip->addFromString('docProps/core.xml', $this->coreProps());
        $zip->addFromString('xl/workbook.xml', $this->workbookXml($sheets));
        $zip->addFromString('xl/_rels/workbook.xml.rels', $this->workbookRels($sheetFiles));

        foreach ($sheets as $index => $sheet) {
            $zip->addFromString('xl/'.$sheetFiles[$index], $this->sheetXml($sheet['rows']));
        }

        $zip->close();

        $output = storage_path('app/'.basename($filename));
        copy($path, $output);
        unlink($path);

        return $output;
    }

    public function sanitizeRows(array $rows): array
    {
        return array_map(function (array $row) {
            return array_map(fn ($value) => is_string($value) ? SpreadsheetSanitizer::text($value) : $value, $row);
        }, $rows);
    }

    public function sanitizeValue(mixed $value): mixed
    {
        return is_string($value) ? SpreadsheetSanitizer::text($value) : $value;
    }

    protected function contentTypes(array $sheetFiles): string
    {
        $overrides = '';
        foreach ($sheetFiles as $index => $file) {
            $overrides .= '<Override PartName="/xl/'.$file.'" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.worksheet+xml"/>';
        }

        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?><Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types"><Default Extension="rels" ContentType="application/vnd.openxmlformats-package.relationships+xml"/><Default Extension="xml" ContentType="application/xml"/>'.$overrides.'<Override PartName="/xl/workbook.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet.main+xml"/><Override PartName="/docProps/core.xml" ContentType="application/vnd.openxmlformats-package.core-properties+xml"/><Override PartName="/docProps/app.xml" ContentType="application/vnd.openxmlformats-officedocument.extended-properties+xml"/></Types>';
    }

    protected function rels(): string
    {
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?><Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships"><Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument" Target="xl/workbook.xml"/></Relationships>';
    }

    protected function appProps(array $sheets): string
    {
        $tabs = '';
        foreach ($sheets as $sheet) {
            $tabs .= '<vt:lpstr>'.e($sheet['name']).'</vt:lpstr>';
        }
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?><Properties xmlns="http://schemas.openxmlformats.org/officeDocument/2006/extended-properties" xmlns:vt="http://schemas.openxmlformats.org/officeDocument/2006/docPropsVTypes"><Application>Nuist AMI</Application><HeadingPairs><vt:vector size="2" baseType="variant"><vt:variant><vt:lpstr>Worksheets</vt:lpstr></vt:variant><vt:variant><vt:i4>'.count($sheets).'</vt:i4></vt:variant></vt:vector></HeadingPairs><TitlesOfParts><vt:vector size="'.count($sheets).'" baseType="lpstr">'.$tabs.'</vt:vector></TitlesOfParts></Properties>';
    }

    protected function coreProps(): string
    {
        $now = now()->toIso8601String();
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?><cp:coreProperties xmlns:cp="http://schemas.openxmlformats.org/package/2006/metadata/core-properties" xmlns:dc="http://purl.org/dc/elements/1.1/" xmlns:dcterms="http://purl.org/dc/terms/" xmlns:dcmitype="http://purl.org/dc/dcmitype/" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"><dc:creator>Nuist AMI</dc:creator><cp:lastModifiedBy>Nuist AMI</cp:lastModifiedBy><dcterms:created xsi:type="dcterms:W3CDTF">'.$now.'</dcterms:created><dcterms:modified xsi:type="dcterms:W3CDTF">'.$now.'</dcterms:modified></cp:coreProperties>';
    }

    protected function workbookXml(array $sheets): string
    {
        $sheetXml = '';
        foreach ($sheets as $index => $sheet) {
            $sheetXml .= '<sheet name="'.e($sheet['name']).'" sheetId="'.($index + 1).'" r:id="rId'.($index + 1).'"/>';
        }
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?><workbook xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships"><sheets>'.$sheetXml.'</sheets></workbook>';
    }

    protected function workbookRels(array $sheetFiles): string
    {
        $rels = '';
        foreach ($sheetFiles as $index => $file) {
            $rels .= '<Relationship Id="rId'.($index + 1).'" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/worksheet" Target="'.$file.'"/>';
        }
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?><Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">'.$rels.'</Relationships>';
    }

    protected function sheetXml(array $rows): string
    {
        $xml = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?><worksheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main"><sheetData>';
        foreach ($rows as $rowIndex => $row) {
            $xml .= '<row r="'.($rowIndex + 1).'">';
            foreach (array_values($row) as $colIndex => $value) {
                $cellRef = $this->columnLetter($colIndex + 1).($rowIndex + 1);
                if (is_int($value) || is_float($value)) {
                    $xml .= '<c r="'.$cellRef.'"><v>'.$value.'</v></c>';
                } else {
                    $xml .= '<c r="'.$cellRef.'" t="inlineStr"><is><t>'.e(SpreadsheetSanitizer::text((string) $value)).'</t></is></c>';
                }
            }
            $xml .= '</row>';
        }
        return $xml.'</sheetData></worksheet>';
    }

    protected function columnLetter(int $index): string
    {
        $letter = '';
        while ($index > 0) {
            $index--;
            $letter = chr(65 + ($index % 26)).$letter;
            $index = intdiv($index, 26);
        }
        return $letter;
    }
}
