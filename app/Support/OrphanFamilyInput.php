<?php

namespace App\Support;

use App\Models\Orphan;
use App\Models\OrphanFamily;
use GdImage;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * Validation and saving shared by the public orphan registration form and the dashboard.
 * Attachments live on the private "local" disk and are only served to signed-in staff.
 */
final class OrphanFamilyInput
{
    public const DOCUMENT_MAX_KB = 1024;

    public const DOCUMENT_DISK = 'local';

    public const PHOTO_MAX_KB = 2048;

    public const PHOTO_MAX_EDGE = 480;

    private const IMAGE_MAX_EDGE = 1600;

    private const IMAGE_QUALITY = 72;

    private const PHOTO_QUALITY = 70;

    /**
     * Decoding is skipped above this size so one oversized scan cannot exhaust PHP's memory limit.
     */
    private const IMAGE_MAX_DECODE_PIXELS = 12_000_000;

    private const CONTACT_FIELDS = ['contact_name', 'contact_relation', 'contact_phone'];

    private const MOTHER_FIELDS = [
        'mother_full_name', 'mother_national_id', 'mother_birth_date', 'mother_passport_number', 'mother_residence_area',
        'mother_neighborhood', 'mother_nearest_landmark', 'mother_birth_country', 'mother_birth_place', 'mother_nationality', 'mother_phone',
    ];

    private const ORPHAN_FIELDS = ['full_name', 'birth_date', 'national_id', 'education_stage', 'health_status', 'health_notes'];

    /**
     * @return array<string, list<mixed>>
     */
    public static function rules(bool $documentsRequired): array
    {
        $orphanOnly = 'exclude_unless:type,'.OrphanFamily::TYPE_ORPHAN;
        $widowOnly = 'exclude_unless:type,'.OrphanFamily::TYPE_WIDOW_FAMILY;
        $documentRule = [$documentsRequired ? 'required' : 'nullable', 'file', 'mimes:jpg,jpeg,png,webp,pdf', 'max:'.self::DOCUMENT_MAX_KB];

        return [
            'type' => ['required', Rule::in(OrphanFamily::TYPES)],
            'contact_name' => [$orphanOnly, 'required', 'string', 'max:255'],
            'contact_relation' => [$orphanOnly, 'required', 'string', 'max:100'],
            'contact_phone' => [$orphanOnly, 'required', 'string', 'max:30'],
            'father_full_name' => ['required', 'string', 'max:255'],
            'father_death_date' => ['required', 'date', 'before_or_equal:today'],
            'father_death_cause' => ['required', 'string', 'max:255'],
            'mother_full_name' => [$widowOnly, 'required', 'string', 'max:255'],
            'mother_national_id' => [$widowOnly, 'required', 'digits:9'],
            'mother_birth_date' => [$widowOnly, 'required', 'date', 'before:today'],
            'mother_passport_number' => [$widowOnly, 'nullable', 'string', 'max:30'],
            'mother_residence_area' => [$widowOnly, 'required', Rule::in(self::residenceAreaKeys())],
            'mother_neighborhood' => [$widowOnly, 'required', 'string', 'max:255'],
            'mother_nearest_landmark' => [$widowOnly, 'required', 'string', 'max:255'],
            'mother_birth_country' => [$widowOnly, 'required', 'string', 'max:100'],
            'mother_birth_place' => [$widowOnly, 'required', 'string', 'max:255'],
            'mother_nationality' => [$widowOnly, 'required', 'string', 'max:100'],
            'mother_phone' => [$widowOnly, 'required', 'string', 'max:30'],
            'orphans' => ['required', 'array', 'min:1', 'max:'.OrphanFamily::MAX_ORPHANS],
            'orphans.*.id' => ['nullable', 'integer'],
            'orphans.*.full_name' => ['required', 'string', 'max:255'],
            'orphans.*.birth_date' => ['required', 'date', 'before_or_equal:today'],
            'orphans.*.national_id' => ['required', 'digits:9'],
            'orphans.*.education_stage' => ['required', Rule::in(Orphan::EDUCATION_STAGES)],
            'orphans.*.health_status' => ['required', Rule::in(Orphan::HEALTH_STATUSES)],
            'orphans.*.health_notes' => ['nullable', 'string', 'max:500'],
            'orphans.*.photo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:'.self::PHOTO_MAX_KB],
            'documents' => [$documentsRequired ? 'required' : 'nullable', 'array'],
            ...collect(OrphanFamily::DOCUMENTS)->mapWithKeys(fn (string $document): array => ['documents.'.$document => $documentRule])->all(),
        ];
    }

    /**
     * Public self-update: type and mother ID stay as stored, and the three certificates must be uploaded again.
     *
     * @return array<string, list<mixed>>
     */
    public static function publicUpdateRules(OrphanFamily $family): array
    {
        $rules = self::rules(documentsRequired: true);
        $rules['type'] = ['required', Rule::in([$family->type])];

        if ($family->type === OrphanFamily::TYPE_WIDOW_FAMILY && filled($family->mother_national_id)) {
            $rules['mother_national_id'] = ['required', 'digits:9', Rule::in([$family->mother_national_id])];
        }

        return $rules;
    }

    /**
     * Readable field names for validation messages.
     *
     * @return array<string, string>
     */
    public static function attributes(): array
    {
        $fields = ['type', ...self::CONTACT_FIELDS, 'father_full_name', 'father_death_date', 'father_death_cause', ...self::MOTHER_FIELDS];
        $attributes = collect($fields)->mapWithKeys(fn (string $field): array => [$field => __('orphans.field.'.$field)]);

        foreach (self::ORPHAN_FIELDS as $field) {
            $attributes['orphans.*.'.$field] = __('orphans.field.orphan_'.$field);
        }

        $attributes['orphans.*.photo'] = __('orphans.field.orphan_photo');

        foreach (OrphanFamily::DOCUMENTS as $document) {
            $attributes['documents.'.$document] = __('orphans.document.'.$document);
        }

        return $attributes->all();
    }

    /**
     * Family columns from validated input; fields that do not apply to the chosen type are cleared.
     *
     * @param  array<string, mixed>  $validated
     * @return array<string, mixed>
     */
    public static function familyAttributes(array $validated): array
    {
        return [
            ...array_fill_keys([...self::CONTACT_FIELDS, ...self::MOTHER_FIELDS], null),
            ...Arr::except($validated, ['orphans', 'documents']),
        ];
    }

    /**
     * Update the listed orphans, add new ones and drop those removed from the form.
     *
     * @param  array<int|string, array<string, mixed>>  $rows
     */
    public static function syncOrphans(OrphanFamily $family, array $rows): void
    {
        $keptIds = [];

        foreach ($rows as $row) {
            $attributes = Arr::only($row, self::ORPHAN_FIELDS) + ['health_notes' => null];
            $orphan = isset($row['id']) ? Orphan::where('orphan_family_id', $family->id)->find($row['id']) : null;

            if ($orphan) {
                $orphan->update($attributes);
            } else {
                $orphan = $family->orphans()->create($attributes);
            }

            self::storePortrait($orphan, $row['photo'] ?? null, $family);
            $keptIds[] = $orphan->id;
        }

        $removed = Orphan::query()
            ->where('orphan_family_id', $family->id)
            ->whereNotIn('id', $keptIds)
            ->get();

        foreach ($removed as $removedOrphan) {
            self::deleteStoredFile($removedOrphan->photo);
        }

        Orphan::query()->whereKey($removed->modelKeys())->delete();
    }

    /**
     * Save uploaded attachments, replacing any earlier file of the same kind.
     */
    public static function storeDocuments(Request $request, OrphanFamily $family): void
    {
        $documents = $family->documents ?? [];

        foreach (OrphanFamily::DOCUMENTS as $document) {
            $file = $request->file('documents.'.$document);

            if (! $file instanceof UploadedFile) {
                continue;
            }

            $previous = $documents[$document] ?? null;
            $documents[$document] = self::storeDocument($file, 'orphan-documents/'.$family->id);

            if ($previous) {
                Storage::disk(self::DOCUMENT_DISK)->delete($previous);
            }
        }

        $family->update(['documents' => $documents]);
    }

    public static function deleteDocuments(OrphanFamily $family): void
    {
        $disk = Storage::disk(self::DOCUMENT_DISK);
        $disk->deleteDirectory('orphan-documents/'.$family->id);
        $disk->deleteDirectory('orphan-photos/'.$family->id);
    }

    /**
     * Stream a private file inline. The host disables fpassthru, which Laravel's Storage::response() uses.
     *
     * @param  array<string, string>  $headers
     */
    public static function inlineResponse(string $path, string $name, array $headers = []): StreamedResponse
    {
        $disk = Storage::disk(self::DOCUMENT_DISK);
        $response = new StreamedResponse;
        $headers['Content-Type'] ??= $disk->mimeType($path) ?: 'application/octet-stream';
        $headers['Content-Length'] ??= (string) $disk->size($path);

        if (! array_key_exists('Content-Disposition', $headers)) {
            $headers['Content-Disposition'] = $response->headers->makeDisposition('inline', $name, Str::ascii($name));
        }

        $response->headers->replace($headers);
        $response->setCallback(function () use ($disk, $path): void {
            $stream = $disk->readStream($path);

            if (! is_resource($stream)) {
                return;
            }

            try {
                while (! feof($stream)) {
                    $chunk = fread($stream, 8192);

                    if ($chunk === false) {
                        break;
                    }

                    echo $chunk;
                }
            } finally {
                fclose($stream);
            }
        });

        return $response;
    }

    /**
     * @return list<string>
     */
    public static function residenceAreaKeys(): array
    {
        return array_merge(...array_values(config('bader.residence_areas')));
    }

    /**
     * Replace the portrait only when a new image was uploaded. The stored file is a small JPEG.
     */
    private static function storePortrait(Orphan $orphan, mixed $upload, OrphanFamily $family): void
    {
        if (! $upload instanceof UploadedFile) {
            return;
        }

        $previous = $orphan->photo;
        $path = self::storeCompressedImage($upload, 'orphan-photos/'.$family->id, self::PHOTO_MAX_EDGE, self::PHOTO_QUALITY);

        if ($path === null) {
            throw ValidationException::withMessages(['orphans' => __('orphans.photo_unreadable')]);
        }

        $orphan->update(['photo' => $path]);

        if ($previous && $previous !== $path) {
            self::deleteStoredFile($previous);
        }
    }

    private static function deleteStoredFile(?string $path): void
    {
        if (filled($path)) {
            Storage::disk(self::DOCUMENT_DISK)->delete($path);
        }
    }

    private static function storeDocument(UploadedFile $file, string $directory): string
    {
        if ($file->getMimeType() === 'application/pdf') {
            return $file->store($directory, self::DOCUMENT_DISK);
        }

        return self::storeCompressedImage($file, $directory, self::IMAGE_MAX_EDGE, self::IMAGE_QUALITY)
            ?? $file->store($directory, self::DOCUMENT_DISK);
    }

    /**
     * Re-encode a photo as a compressed JPEG, which also strips anything hidden in the file.
     */
    private static function storeCompressedImage(UploadedFile $file, string $directory, int $maxEdge, int $quality): ?string
    {
        $dimensions = @getimagesize($file->getRealPath());
        $canDecode = $dimensions && $dimensions[0] * $dimensions[1] <= self::IMAGE_MAX_DECODE_PIXELS;
        $image = $canDecode ? @imagecreatefromstring((string) file_get_contents($file->getRealPath())) : false;

        if (! $image instanceof GdImage) {
            return null;
        }

        $image = self::applyExifOrientation($image, $file);
        $width = imagesx($image);
        $height = imagesy($image);
        $scale = min(1, $maxEdge / max($width, $height));
        $targetWidth = max(1, (int) round($width * $scale));
        $targetHeight = max(1, (int) round($height * $scale));

        $canvas = imagecreatetruecolor($targetWidth, $targetHeight);
        imagefill($canvas, 0, 0, (int) imagecolorallocate($canvas, 255, 255, 255));
        imagecopyresampled($canvas, $image, 0, 0, 0, 0, $targetWidth, $targetHeight, $width, $height);

        ob_start();
        imagejpeg($canvas, null, $quality);
        $jpeg = (string) ob_get_clean();

        $path = $directory.'/'.Str::random(40).'.jpg';
        Storage::disk(self::DOCUMENT_DISK)->put($path, $jpeg);

        return $path;
    }

    private static function applyExifOrientation(GdImage $image, UploadedFile $file): GdImage
    {
        if (! function_exists('exif_read_data') || ! in_array($file->getMimeType(), ['image/jpeg', 'image/jpg'], true)) {
            return $image;
        }

        $orientation = (int) (@exif_read_data($file->getRealPath())['Orientation'] ?? 1);
        $rotated = match ($orientation) {
            3 => imagerotate($image, 180, 0),
            6 => imagerotate($image, -90, 0),
            8 => imagerotate($image, 90, 0),
            default => $image,
        };

        return $rotated instanceof GdImage ? $rotated : $image;
    }
}
