<?php

namespace App\Support;

use App\Models\Orphan;
use App\Models\OrphanFamily;
use App\Models\User;
use Carbon\CarbonImmutable;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Throwable;

/**
 * Staff bulk import of orphan files from a filled Excel/CSV template. Documents are never required;
 * imported files are approved for the team immediately because the data was entered by staff.
 */
final class OrphanImport
{
    public const MAX_ROWS = 500;

    /**
     * Stable column keys used in the template header row (Arabic labels for the team).
     *
     * @var array<string, string>
     */
    public const COLUMNS = [
        'family_group' => 'رمز_الملف',
        'type' => 'نوع_الملف',
        'contact_name' => 'اسم_جهة_الاتصال',
        'contact_relation' => 'صلة_القرابة',
        'contact_phone' => 'هاتف_جهة_الاتصال',
        'father_full_name' => 'اسم_الأب_الكامل',
        'father_death_date' => 'تاريخ_وفاة_الأب',
        'father_death_cause' => 'سبب_وفاة_الأب',
        'mother_full_name' => 'اسم_الأم_الكامل',
        'mother_national_id' => 'هوية_الأم',
        'mother_birth_date' => 'تاريخ_ميلاد_الأم',
        'mother_passport_number' => 'جواز_الأم',
        'mother_residence_area' => 'منطقة_السكن',
        'mother_neighborhood' => 'الحي',
        'mother_nearest_landmark' => 'أقرب_معلم',
        'mother_birth_country' => 'بلد_ميلاد_الأم',
        'mother_birth_place' => 'مكان_ميلاد_الأم',
        'mother_nationality' => 'جنسية_الأم',
        'mother_phone' => 'هاتف_الأم',
        'orphan_full_name' => 'اسم_اليتيم_الكامل',
        'orphan_birth_date' => 'تاريخ_ميلاد_اليتيم',
        'orphan_national_id' => 'هوية_اليتيم',
        'orphan_education_stage' => 'المرحلة_الدراسية',
        'orphan_health_status' => 'الحالة_الصحية',
        'orphan_health_notes' => 'ملاحظات_صحية',
    ];

    /**
     * @return array{families: int, orphans: int}
     */
    public static function import(UploadedFile $file, User $by): array
    {
        $extension = strtolower($file->getClientOriginalExtension() ?: pathinfo($file->getClientOriginalName(), PATHINFO_EXTENSION));

        if (! in_array($extension, ['xlsx', 'csv', 'txt'], true)) {
            throw ValidationException::withMessages(['file' => __('orphans.import.invalid_type')]);
        }

        try {
            $matrix = SimpleSpreadsheet::read($file->getRealPath(), $extension);
        } catch (Throwable $exception) {
            throw ValidationException::withMessages(['file' => __('orphans.import.unreadable')]);
        }

        if ($matrix === []) {
            throw ValidationException::withMessages(['file' => __('orphans.import.empty')]);
        }

        $header = array_shift($matrix);
        $indexes = self::headerIndexes($header);

        if (count($matrix) > self::MAX_ROWS) {
            throw ValidationException::withMessages(['file' => __('orphans.import.too_many_rows', ['max' => self::MAX_ROWS])]);
        }

        $groups = self::groupRows($matrix, $indexes);
        $payloads = [];

        foreach ($groups as $groupKey => $group) {
            $payloads[] = self::validateGroup($groupKey, $group);
        }

        return DB::transaction(function () use ($payloads, $by): array {
            $orphanCount = 0;

            foreach ($payloads as $payload) {
                $family = OrphanFamily::create([
                    ...OrphanFamilyInput::familyAttributes($payload),
                    'source' => OrphanFamily::SOURCE_DASHBOARD,
                    'review_status' => OrphanFamily::REVIEW_APPROVED,
                    'documents' => [],
                    'reviewed_by' => $by->id,
                    'reviewed_at' => now(),
                ]);

                OrphanFamilyInput::syncOrphans($family, $payload['orphans']);
                $orphanCount += count($payload['orphans']);
            }

            return ['families' => count($payloads), 'orphans' => $orphanCount];
        });
    }

    /**
     * Excel bytes for the empty template with two worked examples the team can replace.
     */
    public static function templateBytes(): string
    {
        $headers = array_values(self::COLUMNS);

        return SimpleSpreadsheet::xlsxBytes($headers, [
            self::exampleOrphanRow('F1', 'أحمد محمد سالم أبو عيد', '2015-06-01', '412345678'),
            self::exampleOrphanRow('F1', 'سلمى محمد سالم أبو عيد', '2017-03-20', '423456789'),
            self::exampleWidowRow('F2', 'يوسف محمد سالم النجار', '2016-04-12', '434567890'),
        ]);
    }

    /**
     * @return list<string>
     */
    public static function allowedTypeLabels(): array
    {
        return array_values(self::typeMap());
    }

    /**
     * @return list<string>
     */
    public static function allowedEducationLabels(): array
    {
        return array_map(fn (string $key): string => __('orphans.education.'.$key), Orphan::EDUCATION_STAGES);
    }

    /**
     * @return list<string>
     */
    public static function allowedHealthLabels(): array
    {
        return array_map(fn (string $key): string => __('orphans.health.'.$key), Orphan::HEALTH_STATUSES);
    }

    /**
     * @return list<string>
     */
    public static function allowedResidenceLabels(): array
    {
        return array_map(fn (string $key): string => __('orphans.area.'.$key), OrphanFamilyInput::residenceAreaKeys());
    }

    /**
     * @param  list<string>  $header
     * @return array<string, int>
     */
    private static function headerIndexes(array $header): array
    {
        $normalized = array_map(fn (string $cell): string => self::normalizeHeader($cell), $header);
        $indexes = [];

        foreach (self::COLUMNS as $key => $label) {
            $position = array_search(self::normalizeHeader($label), $normalized, true);
            $english = array_search($key, $normalized, true);

            if ($position === false && $english === false) {
                throw ValidationException::withMessages([
                    'file' => __('orphans.import.missing_column', ['column' => $label]),
                ]);
            }

            $indexes[$key] = (int) ($position !== false ? $position : $english);
        }

        return $indexes;
    }

    /**
     * @param  list<list<string>>  $matrix
     * @param  array<string, int>  $indexes
     * @return array<string, list<array{line: int, values: array<string, string>}>>
     */
    private static function groupRows(array $matrix, array $indexes): array
    {
        $groups = [];

        foreach ($matrix as $offset => $row) {
            $line = $offset + 2;
            $values = [];

            foreach ($indexes as $key => $index) {
                $values[$key] = self::cell($row, $index);
            }

            if (self::isBlankRow($values)) {
                continue;
            }

            $groupKey = $values['family_group'];

            if ($groupKey === '') {
                throw ValidationException::withMessages([
                    'file' => __('orphans.import.row_error', ['line' => $line, 'message' => __('orphans.import.family_group_required')]),
                ]);
            }

            $groups[$groupKey][] = ['line' => $line, 'values' => $values];
        }

        if ($groups === []) {
            throw ValidationException::withMessages(['file' => __('orphans.import.empty')]);
        }

        return $groups;
    }

    /**
     * @param  list<array{line: int, values: array<string, string>}>  $group
     * @return array<string, mixed>
     */
    private static function validateGroup(string $groupKey, array $group): array
    {
        if (count($group) > OrphanFamily::MAX_ORPHANS) {
            throw ValidationException::withMessages([
                'file' => __('orphans.import.group_too_many', ['group' => $groupKey, 'max' => OrphanFamily::MAX_ORPHANS]),
            ]);
        }

        $first = $group[0]['values'];
        $type = self::mapType($first['type'], $group[0]['line']);
        $familyFields = self::familyFieldKeys($type);

        foreach ($group as $entry) {
            foreach ($familyFields as $field) {
                if ($entry['values'][$field] !== $first[$field]) {
                    throw ValidationException::withMessages([
                        'file' => __('orphans.import.group_mismatch', [
                            'group' => $groupKey,
                            'line' => $entry['line'],
                            'column' => self::COLUMNS[$field],
                        ]),
                    ]);
                }
            }

            if (self::mapType($entry['values']['type'], $entry['line']) !== $type) {
                throw ValidationException::withMessages([
                    'file' => __('orphans.import.group_mismatch', [
                        'group' => $groupKey,
                        'line' => $entry['line'],
                        'column' => self::COLUMNS['type'],
                    ]),
                ]);
            }
        }

        $payload = [
            'type' => $type,
            'father_full_name' => $first['father_full_name'],
            'father_death_date' => self::parseDate($first['father_death_date'], $group[0]['line'], self::COLUMNS['father_death_date']),
            'father_death_cause' => $first['father_death_cause'],
            'orphans' => [],
        ];

        if ($type === OrphanFamily::TYPE_ORPHAN) {
            $payload['contact_name'] = $first['contact_name'];
            $payload['contact_relation'] = $first['contact_relation'];
            $payload['contact_phone'] = $first['contact_phone'];
        } else {
            $payload['mother_full_name'] = $first['mother_full_name'];
            $payload['mother_national_id'] = self::nationalId($first['mother_national_id'], $group[0]['line'], self::COLUMNS['mother_national_id']);
            $payload['mother_birth_date'] = self::parseDate($first['mother_birth_date'], $group[0]['line'], self::COLUMNS['mother_birth_date']);
            $payload['mother_passport_number'] = $first['mother_passport_number'] ?: null;
            $payload['mother_residence_area'] = self::mapResidence($first['mother_residence_area'], $group[0]['line']);
            $payload['mother_neighborhood'] = $first['mother_neighborhood'];
            $payload['mother_nearest_landmark'] = $first['mother_nearest_landmark'];
            $payload['mother_birth_country'] = $first['mother_birth_country'];
            $payload['mother_birth_place'] = $first['mother_birth_place'];
            $payload['mother_nationality'] = $first['mother_nationality'];
            $payload['mother_phone'] = $first['mother_phone'];
        }

        $seenIds = [];

        foreach ($group as $entry) {
            $values = $entry['values'];
            $nationalId = self::nationalId($values['orphan_national_id'], $entry['line'], self::COLUMNS['orphan_national_id']);

            if (isset($seenIds[$nationalId])) {
                throw ValidationException::withMessages([
                    'file' => __('orphans.import.row_error', [
                        'line' => $entry['line'],
                        'message' => __('orphans.import.duplicate_orphan_id', ['id' => $nationalId]),
                    ]),
                ]);
            }

            $seenIds[$nationalId] = true;
            $payload['orphans'][] = [
                'full_name' => $values['orphan_full_name'],
                'birth_date' => self::parseDate($values['orphan_birth_date'], $entry['line'], self::COLUMNS['orphan_birth_date']),
                'national_id' => $nationalId,
                'education_stage' => self::mapEducation($values['orphan_education_stage'], $entry['line']),
                'health_status' => self::mapHealth($values['orphan_health_status'], $entry['line']),
                'health_notes' => $values['orphan_health_notes'] !== '' ? $values['orphan_health_notes'] : null,
            ];
        }

        $validator = Validator::make($payload, OrphanFamilyInput::rules(documentsRequired: false), [], OrphanFamilyInput::attributes());

        if ($validator->fails()) {
            $message = $validator->errors()->first();

            throw ValidationException::withMessages([
                'file' => __('orphans.import.group_error', ['group' => $groupKey, 'message' => $message]),
            ]);
        }

        return $validator->validated();
    }

    /**
     * @return list<string>
     */
    private static function familyFieldKeys(string $type): array
    {
        $shared = ['type', 'father_full_name', 'father_death_date', 'father_death_cause'];

        return $type === OrphanFamily::TYPE_ORPHAN
            ? [...$shared, 'contact_name', 'contact_relation', 'contact_phone']
            : [...$shared, 'mother_full_name', 'mother_national_id', 'mother_birth_date', 'mother_passport_number', 'mother_residence_area', 'mother_neighborhood', 'mother_nearest_landmark', 'mother_birth_country', 'mother_birth_place', 'mother_nationality', 'mother_phone'];
    }

    /**
     * @param  array<string, string>  $values
     */
    private static function isBlankRow(array $values): bool
    {
        foreach ($values as $value) {
            if ($value !== '') {
                return false;
            }
        }

        return true;
    }

    /**
     * @param  list<string>  $row
     */
    private static function cell(array $row, int $index): string
    {
        return trim((string) ($row[$index] ?? ''));
    }

    private static function normalizeHeader(string $header): string
    {
        return str_replace([' ', "\u{00A0}"], '_', trim($header));
    }

    private static function mapType(string $value, int $line): string
    {
        $key = self::lookup(self::typeMap(), $value);

        if ($key === null) {
            throw ValidationException::withMessages([
                'file' => __('orphans.import.row_error', [
                    'line' => $line,
                    'message' => __('orphans.import.invalid_type_value', ['value' => $value]),
                ]),
            ]);
        }

        return $key;
    }

    private static function mapEducation(string $value, int $line): string
    {
        $map = [];

        foreach (Orphan::EDUCATION_STAGES as $key) {
            $map[$key] = __('orphans.education.'.$key);
        }

        $key = self::lookup($map, $value);

        if ($key === null) {
            throw ValidationException::withMessages([
                'file' => __('orphans.import.row_error', [
                    'line' => $line,
                    'message' => __('orphans.import.invalid_education', ['value' => $value]),
                ]),
            ]);
        }

        return $key;
    }

    private static function mapHealth(string $value, int $line): string
    {
        $map = [];

        foreach (Orphan::HEALTH_STATUSES as $key) {
            $map[$key] = __('orphans.health.'.$key);
        }

        $key = self::lookup($map, $value);

        if ($key === null) {
            throw ValidationException::withMessages([
                'file' => __('orphans.import.row_error', [
                    'line' => $line,
                    'message' => __('orphans.import.invalid_health', ['value' => $value]),
                ]),
            ]);
        }

        return $key;
    }

    private static function mapResidence(string $value, int $line): string
    {
        $map = [];

        foreach (OrphanFamilyInput::residenceAreaKeys() as $key) {
            $map[$key] = __('orphans.area.'.$key);
        }

        $key = self::lookup($map, $value);

        if ($key === null) {
            throw ValidationException::withMessages([
                'file' => __('orphans.import.row_error', [
                    'line' => $line,
                    'message' => __('orphans.import.invalid_area', ['value' => $value]),
                ]),
            ]);
        }

        return $key;
    }

    /**
     * @param  array<string, string>  $map  key => Arabic label
     */
    private static function lookup(array $map, string $value): ?string
    {
        $value = trim($value);

        if ($value === '') {
            return null;
        }

        if (array_key_exists($value, $map)) {
            return $value;
        }

        foreach ($map as $key => $label) {
            if ($label === $value || self::normalizeHeader($label) === self::normalizeHeader($value)) {
                return $key;
            }
        }

        return null;
    }

    /**
     * @return array<string, string>
     */
    private static function typeMap(): array
    {
        return [
            OrphanFamily::TYPE_ORPHAN => 'يتيم',
            OrphanFamily::TYPE_WIDOW_FAMILY => 'أسرة_أرملة',
        ];
    }

    private static function parseDate(string $value, int $line, string $column): string
    {
        $value = trim($value);

        if ($value === '') {
            throw ValidationException::withMessages([
                'file' => __('orphans.import.row_error', [
                    'line' => $line,
                    'message' => __('orphans.import.date_required', ['column' => $column]),
                ]),
            ]);
        }

        if (is_numeric($value)) {
            $serial = (int) round((float) $value);

            try {
                return CarbonImmutable::create(1899, 12, 30)->addDays($serial)->toDateString();
            } catch (Throwable) {
                // Fall through to string parsing.
            }
        }

        foreach (['Y-m-d', 'd/m/Y', 'd-m-Y', 'Y/m/d'] as $format) {
            try {
                $date = CarbonImmutable::createFromFormat('!'.$format, $value);

                if ($date !== false) {
                    return $date->toDateString();
                }
            } catch (Throwable) {
                continue;
            }
        }

        try {
            return CarbonImmutable::parse($value)->toDateString();
        } catch (Throwable) {
            throw ValidationException::withMessages([
                'file' => __('orphans.import.row_error', [
                    'line' => $line,
                    'message' => __('orphans.import.invalid_date', ['column' => $column, 'value' => $value]),
                ]),
            ]);
        }
    }

    private static function nationalId(string $value, int $line, string $column): string
    {
        $digits = preg_replace('/\D+/', '', $value) ?? '';

        if (strlen($digits) === 8) {
            $digits = '0'.$digits;
        }

        if (! preg_match('/^\d{9}$/', $digits)) {
            throw ValidationException::withMessages([
                'file' => __('orphans.import.row_error', [
                    'line' => $line,
                    'message' => __('orphans.import.invalid_national_id', ['column' => $column, 'value' => $value]),
                ]),
            ]);
        }

        return $digits;
    }

    /**
     * @return list<string>
     */
    private static function exampleOrphanRow(string $group, string $name, string $birth, string $id): array
    {
        $row = array_fill_keys(array_keys(self::COLUMNS), '');
        $row['family_group'] = $group;
        $row['type'] = 'يتيم';
        $row['contact_name'] = 'عبد الله سالم أبو عيد';
        $row['contact_relation'] = 'العم';
        $row['contact_phone'] = '0599123456';
        $row['father_full_name'] = 'محمد سالم خليل أبو عيد';
        $row['father_death_date'] = '2024-02-10';
        $row['father_death_cause'] = 'استشهد في قصف';
        $row['orphan_full_name'] = $name;
        $row['orphan_birth_date'] = $birth;
        $row['orphan_national_id'] = $id;
        $row['orphan_education_stage'] = __('orphans.education.primary');
        $row['orphan_health_status'] = __('orphans.health.healthy');

        return array_values($row);
    }

    /**
     * @return list<string>
     */
    private static function exampleWidowRow(string $group, string $name, string $birth, string $id): array
    {
        $row = array_fill_keys(array_keys(self::COLUMNS), '');
        $row['family_group'] = $group;
        $row['type'] = 'أسرة_أرملة';
        $row['father_full_name'] = 'محمد سالم خليل النجار';
        $row['father_death_date'] = '2023-11-01';
        $row['father_death_cause'] = 'استشهد في قصف';
        $row['mother_full_name'] = 'فاطمة أحمد حسن النجار';
        $row['mother_national_id'] = '401234567';
        $row['mother_birth_date'] = '1990-04-12';
        $row['mother_residence_area'] = __('orphans.area.nuseirat_camp');
        $row['mother_neighborhood'] = 'بلوك 5';
        $row['mother_nearest_landmark'] = 'مسجد القسام';
        $row['mother_birth_country'] = 'فلسطين';
        $row['mother_birth_place'] = 'غزة';
        $row['mother_nationality'] = 'فلسطينية';
        $row['mother_phone'] = '0597123456';
        $row['orphan_full_name'] = $name;
        $row['orphan_birth_date'] = $birth;
        $row['orphan_national_id'] = $id;
        $row['orphan_education_stage'] = __('orphans.education.primary');
        $row['orphan_health_status'] = __('orphans.health.healthy');

        return array_values($row);
    }
}
