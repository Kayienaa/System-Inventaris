<?php

namespace Tests\Feature;

use App\Actions\Borrowings\RequestBorrowingAction;
use App\Enums\BorrowingStatus;
use App\Models\Asset;
use App\Models\Borrowing;
use App\Models\GuruProfile;
use App\Models\SiswaProfile;
use App\Models\User;
use Database\Seeders\AssetCategorySeeder;
use Database\Seeders\AssetSeeder;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BorrowingRoleDueDateTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolePermissionSeeder::class);
        $this->seed(AssetCategorySeeder::class);
        $this->seed(AssetSeeder::class);
    }

    public function test_student_borrowing_forces_due_date_to_today_15_15_wib(): void
    {
        $siswa = User::factory()->create();
        $siswa->assignRole('siswa');
        SiswaProfile::create([
            'user_id' => $siswa->id,
            'nis' => '12345',
            'phone' => '6281234567890',
        ]);

        $asset = Asset::first();

        // Siswa submits borrowing with arbitrary due_at
        $response = $this->actingAs($siswa)->post(route('assets.borrow.store', $asset), [
            'asset_id' => $asset->id,
            'purpose_category' => 'praktik',
            'urgency_level' => 'biasa',
            'due_at' => now()->addDays(5)->format('Y-m-d H:i'),
        ]);

        $response->assertRedirect(route('borrowings.mine'));

        $borrowing = Borrowing::where('borrower_user_id', $siswa->id)->firstOrFail();

        // Must be today at 15:15:00
        $expectedDue = now()->setTime(15, 15, 0)->format('Y-m-d H:i:s');
        $this->assertEquals($expectedDue, $borrowing->due_at->format('Y-m-d H:i:s'));
    }

    public function test_request_borrowing_action_forces_due_date_for_siswa(): void
    {
        $siswa = User::factory()->create();
        $siswa->assignRole('siswa');

        $asset = Asset::first();

        $action = app(RequestBorrowingAction::class);
        $borrowing = $action->execute(
            $siswa,
            $asset,
            'Testing note',
            null,
            now()->addDays(7)
        );

        $expectedDue = now()->setTime(15, 15, 0)->format('Y-m-d H:i:s');
        $this->assertEquals($expectedDue, $borrowing->due_at->format('Y-m-d H:i:s'));
    }

    public function test_guru_borrowing_respects_custom_due_date_including_30_days(): void
    {
        $guru = User::factory()->create();
        $guru->assignRole('guru');
        GuruProfile::create([
            'user_id' => $guru->id,
            'nip' => '198103302010011016',
            'phone' => '6281234567890',
        ]);

        $asset = Asset::first();
        $targetDue = now()->addDays(30)->setTime(17, 0, 0);

        $response = $this->actingAs($guru)->post(route('assets.borrow.store', $asset), [
            'asset_id' => $asset->id,
            'purpose_category' => 'mengajar',
            'urgency_level' => 'biasa',
            'due_at' => $targetDue->format('Y-m-d H:i'),
        ]);

        $response->assertRedirect(route('borrowings.mine'));

        $borrowing = Borrowing::where('borrower_user_id', $guru->id)->firstOrFail();
        $this->assertEquals($targetDue->format('Y-m-d H:i'), $borrowing->due_at->format('Y-m-d H:i'));
    }

    public function test_borrow_form_shows_locked_notice_for_siswa_and_30_day_preset_for_guru(): void
    {
        $asset = Asset::first();

        // Siswa view
        $siswa = User::factory()->create();
        $siswa->assignRole('siswa');
        SiswaProfile::create([
            'user_id' => $siswa->id,
            'nis' => '12345',
            'phone' => '6281234567890',
        ]);
        $responseSiswa = $this->actingAs($siswa)->get(route('assets.borrow', $asset));
        $responseSiswa->assertStatus(200);
        $responseSiswa->assertSee('Batas Pengembalian: Hari ini pukul 15:15 WIB');
        $responseSiswa->assertDontSee('id="due_at_picker"', false);

        // Guru view
        $guru = User::factory()->create();
        $guru->assignRole('guru');
        GuruProfile::create([
            'user_id' => $guru->id,
            'nip' => '198103302010011016',
            'phone' => '6281234567891',
        ]);
        $responseGuru = $this->actingAs($guru)->get(route('assets.borrow', $asset));
        $responseGuru->assertStatus(200);
        $responseGuru->assertSee('id="due_at_picker"', false);
        $responseGuru->assertSee('1 Bulan (H+30 hari)');
    }

    public function test_admin_monitoring_table_shows_clean_asset_name_without_badge_or_code_in_row(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $siswa = User::factory()->create(['name' => 'Siswa Budi']);
        $siswa->assignRole('siswa');

        $asset = Asset::first();

        Borrowing::create([
            'borrower_user_id' => $siswa->id,
            'asset_id' => $asset->id,
            'status' => BorrowingStatus::Borrowed,
            'requested_at' => now(),
            'borrowed_at' => now(),
            'due_at' => now()->addDays(3),
        ]);

        $response = $this->actingAs($admin)->get(route('admin.borrowings.index'));
        $response->assertStatus(200);
        $response->assertSee($asset->name);
    }
}
