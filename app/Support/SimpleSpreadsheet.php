<?php

namespace App\Support;

use RuntimeException;
use ZipArchive;

/**
 * Minimal CSV / XLSX helpers so the orphans import works without a spreadsheet package.
 * XLSX cells are written as inline strings so Arabic text and ID numbers stay intact in Excel.
 */
final class SimpleSpreadsheet
{
    /**
     * @param  list<string>  $headers
     * @param  list<list<string>>  $rows
     */
    public static function xlsxBytes(array $headers, array $rows): string
    {
        $sheetRows = array_merge([$headers], $rows);
        $sheetXml = self::sheetXml($sheetRows);
        $tmp = tempnam(sys_get_temp_dir(), 'xlsx');

        if ($tmp === false) {
            throw new RuntimeException('Could not create a temporary file for the spreadsheet.');
        }

        $zip = new ZipArchive;

        if ($zip->open($tmp, ZipArchive::OVERWRITE) !== true) {
            @unlink($tmp);
            throw new RuntimeException('Could not build the Excel file.');
        }

        $zip->addFromString('[Content_Types].xml', <<<'XML'
<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types">
  <Default Extension="rels" ContentType="application/vnd.openxmlformats-package.relationships+xml"/>
  <Default Extension="xml" ContentType="application/xml"/>
  <Override PartName="/xl/workbook.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet.main+xml"/>
  <Override PartName="/xl/worksheets/sheet1.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.worksheet+xml"/>
</Types>
XML);
        $zip->addFromString('_rels/.rels', <<<'XML'
<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">
  <Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument" Target="xl/workbook.xml"/>
</Relationships>
XML);
        $zip->addFromString('xl/workbook.xml', <<<'XML'
<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<workbook xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships">
  <sheets><sheet name="orphans" sheetId="1" r:id="rId1"/></sheets>
</workbook>
XML);
        $zip->addFromString('xl/_rels/workbook.xml.rels', <<<'XML'
<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">
  <Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/worksheet" Target="worksheets/sheet1.xml"/>
</Relationships>
XML);
        $zip->addFromString('xl/worksheets/sheet1.xml', $sheetXml);
        $zip->close();

        $bytes = (string) file_get_contents($tmp);
        @unlink($tmp);

        return $bytes;
    }

    /**
     * @return list<list<string>>
     */
    public static function read(string $path, string $extension): array
    {
        return match (strtolower($extension)) {
            'csv', 'txt' => self::readCsv($path),
            'xlsx' => self::readXlsx($path),
            default => throw new RuntimeException('Unsupported spreadsheet type.'),
        };
    }

    /**
     * @return list<list<string>>
     */
    public static function readCsv(string $path): array
    {
        $handle = fopen($path, 'rb');

        if ($handle === false) {
            throw new RuntimeException('Could not open the CSV file.');
        }

        $bom = fread($handle, 3);

        if ($bom !== "\xEF\xBB\xBF") {
            rewind($handle);
        }

        $rows = [];

        while (($row = fgetcsv($handle)) !== false) {
            if (self::rowIsEmpty($row)) {
                continue;
            }

            $rows[] = array_map(fn ($cell): string => self::normalizeCell((string) $cell), $row);
        }

        fclose($handle);

        return $rows;
    }

    /**
     * @return list<list<string>>
     */
    public static function readXlsx(string $path): array
    {
        $zip = new ZipArchive;

        if ($zip->open($path) !== true) {
            throw new RuntimeException('Could not open the Excel file.');
        }

        $sheet = $zip->getFromName('xl/worksheets/sheet1.xml');
        $shared = $zip->getFromName('xl/sharedStrings.xml');
        $zip->close();

        if ($sheet === false) {
            throw new RuntimeException('The Excel file has no first worksheet.');
        }

        $sharedStrings = $shared === false ? [] : self::sharedStrings($shared);
        $rows = [];
        $xml = simplexml_load_string($sheet);

        if ($xml === false) {
            throw new RuntimeException('The Excel worksheet could not be read.');
        }

        $xml->registerXPathNamespace('m', 'http://schemas.openxmlformats.org/spreadsheetml/2006/main');

        foreach ($xml->sheetData->row ?? [] as $row) {
            $cells = [];
            $maxIndex = -1;

            foreach ($row->c as $cell) {
                $ref = (string) $cell['r'];
                $index = self::columnIndex(preg_replace('/\d+/', '', $ref) ?: 'A');
                $maxIndex = max($maxIndex, $index);
                $cells[$index] = self::cellValue($cell, $sharedStrings);
            }

            if ($maxIndex < 0) {
                continue;
            }

            $line = [];

            for ($i = 0; $i <= $maxIndex; $i++) {
                $line[] = $cells[$i] ?? '';
            }

            if (! self::rowIsEmpty($line)) {
                $rows[] = $line;
            }
        }

        return $rows;
    }

    /**
     * @param  list<list<string>>  $rows
     */
    private static function sheetXml(array $rows): string
    {
        $xml = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            .'<worksheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main"><sheetData>';

        foreach ($rows as $rowIndex => $row) {
            $r = $rowIndex + 1;
            $xml .= '<row r="'.$r.'">';

            foreach ($row as $columnIndex => $value) {
                $ref = self::columnLetter($columnIndex).$r;
                $text = htmlspecialchars((string) $value, ENT_XML1 | ENT_QUOTES, 'UTF-8');
                $xml .= '<c r="'.$ref.'" t="inlineStr"><is><t>'.$text.'</t></is></c>';
            }

            $xml .= '</row>';
        }

        return $xml.'</sheetData></worksheet>';
    }

    /**
     * @return list<string>
     */
    private static function sharedStrings(string $xml): array
    {
        $document = simplexml_load_string($xml);

        if ($document === false) {
            return [];
        }

        $strings = [];

        foreach ($document->si as $item) {
            if (isset($item->t)) {
                $strings[] = (string) $item->t;
            } else {
                $parts = [];

                foreach ($item->r as $run) {
                    $parts[] = (string) $run->t;
                }

                $strings[] = implode('', $parts);
            }
        }

        return $strings;
    }

    /**
     * @param  list<string>  $sharedStrings
     */
    private static function cellValue(object $cell, array $sharedStrings): string
    {
        $type = (string) ($cell['t'] ?? '');

        if ($type === 'inlineStr') {
            return self::normalizeCell((string) ($cell->is->t ?? ''));
        }

        if ($type === 's') {
            $index = (int) ($cell->v ?? -1);

            return self::normalizeCell($sharedStrings[$index] ?? '');
        }

        $raw = (string) ($cell->v ?? '');

        if ($raw !== '' && is_numeric($raw) && str_contains($raw, '.')) {
            $asFloat = (float) $raw;

            if (abs($asFloat - round($asFloat)) < 0.00001) {
                return self::normalizeCell((string) (int) round($asFloat));
            }
        }

        return self::normalizeCell($raw);
    }

    private static function normalizeCell(string $value): string
    {
        return trim(str_replace("\u{00A0}", ' ', $value));
    }

    /**
     * @param  list<string|null>  $row
     */
    private static function rowIsEmpty(array $row): bool
    {
        foreach ($row as $cell) {
            if (trim((string) $cell) !== '') {
                return false;
            }
        }

        return true;
    }

    private static function columnLetter(int $index): string
    {
        $letter = '';
        $index++;

        while ($index > 0) {
            $index--;
            $letter = chr(65 + ($index % 26)).$letter;
            $index = intdiv($index, 26);
        }

        return $letter;
    }

    private static function columnIndex(string $letters): int
    {
        $index = 0;

        foreach (str_split(strtoupper($letters)) as $char) {
            $index = ($index * 26) + (ord($char) - 64);
        }

        return $index - 1;
    }
}
