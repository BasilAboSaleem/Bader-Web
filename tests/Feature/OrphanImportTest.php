<?php

namespace Tests\Feature;

use App\Models\Orphan;
use App\Models\OrphanFamily;
use App\Models\User;
use App\Support\OrphanImport;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class OrphanImportTest extends TestCase
{
    use RefreshDatabase;

    public function test_staff_can_download_the_excel_template(): void
    {
        $admin = User::factory()->create();

        $this->actingAs($admin)->get(route('dashboard.orphans.import.template'))
            ->assertOk()
            ->assertHeader('content-type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    }

    public function test_importing_the_template_creates_approved_files_without_documents(): void
    {
        $admin = User::factory()->create();
        $file = $this->uploadedXlsx(OrphanImport::templateBytes());

        $this->actingAs($admin)->post(route('dashboard.orphans.import.store'), ['file' => $file])
            ->assertRedirect(route('dashboard.orphans.index', ['status' => OrphanFamily::REVIEW_APPROVED]))
            ->assertSessionHas('status');

        $this->assertSame(2, OrphanFamily::count());
        $this->assertSame(3, Orphan::count());

        $orphanFile = OrphanFamily::where('type', OrphanFamily::TYPE_ORPHAN)->sole();
        $this->assertTrue($orphanFile->isApproved());
        $this->assertSame(OrphanFamily::SOURCE_DASHBOARD, $orphanFile->source);
        $this->assertSame([], $orphanFile->documents ?? []);
        $this->assertSame($admin->id, $orphanFile->reviewed_by);
        $this->assertSame(2, $orphanFile->orphans()->count());

        $widowFile = OrphanFamily::where('type', OrphanFamily::TYPE_WIDOW_FAMILY)->sole();
        $this->assertSame('فاطمة أحمد حسن النجار', $widowFile->mother_full_name);
        $this->assertSame('nuseirat_camp', $widowFile->mother_residence_area);
        $this->assertSame(1, $widowFile->orphans()->count());
    }

    public function test_csv_import_groups_siblings_and_rejects_bad_rows(): void
    {
        $admin = User::factory()->create();
        $headers = array_values(OrphanImport::COLUMNS);
        $good = $this->csvUpload([
            $headers,
            $this->orphanCsvRow('A1', 'أحمد محمد', '2015-01-01', '412345678'),
            $this->orphanCsvRow('A1', 'سلمى محمد', '2017-01-01', '423456789'),
        ]);

        $this->actingAs($admin)->post(route('dashboard.orphans.import.store'), ['file' => $good])
            ->assertSessionHasNoErrors();

        $this->assertSame(1, OrphanFamily::count());
        $this->assertSame(2, Orphan::count());

        $bad = $this->csvUpload([
            $headers,
            $this->orphanCsvRow('B1', 'عمر محمد', '2015-01-01', '12'),
        ]);

        $this->actingAs($admin)->post(route('dashboard.orphans.import.store'), ['file' => $bad])
            ->assertSessionHasErrors('file');

        $this->assertSame(1, OrphanFamily::count());
    }

    public function test_viewers_cannot_import_orphans(): void
    {
        $viewer = User::factory()->create(['role' => User::ROLE_VIEWER]);

        $this->actingAs($viewer)->get(route('dashboard.orphans.import'))->assertForbidden();
        $this->actingAs($viewer)->post(route('dashboard.orphans.import.store'), [
            'file' => $this->uploadedXlsx(OrphanImport::templateBytes()),
        ])->assertForbidden();
    }

    private function uploadedXlsx(string $bytes): UploadedFile
    {
        $path = tempnam(sys_get_temp_dir(), 'orphans').'.xlsx';
        file_put_contents($path, $bytes);

        return new UploadedFile($path, 'orphans.xlsx', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', null, true);
    }

    /**
     * @param  list<list<string>>  $rows
     */
    private function csvUpload(array $rows): UploadedFile
    {
        $path = tempnam(sys_get_temp_dir(), 'orphans').'.csv';
        $handle = fopen($path, 'wb');
        fwrite($handle, "\xEF\xBB\xBF");

        foreach ($rows as $row) {
            fputcsv($handle, $row);
        }

        fclose($handle);

        return new UploadedFile($path, 'orphans.csv', 'text/csv', null, true);
    }

    /**
     * @return list<string>
     */
    private function orphanCsvRow(string $group, string $name, string $birth, string $id): array
    {
        $row = array_fill_keys(array_keys(OrphanImport::COLUMNS), '');
        $row['family_group'] = $group;
        $row['type'] = 'يتيم';
        $row['contact_name'] = 'عبد الله سالم';
        $row['contact_relation'] = 'العم';
        $row['contact_phone'] = '0599123456';
        $row['father_full_name'] = 'محمد سالم';
        $row['father_death_date'] = '2024-02-10';
        $row['father_death_cause'] = 'استشهاد';
        $row['orphan_full_name'] = $name;
        $row['orphan_birth_date'] = $birth;
        $row['orphan_national_id'] = $id;
        $row['orphan_education_stage'] = 'ابتدائي';
        $row['orphan_health_status'] = 'سليم';

        return array_values($row);
    }
}
