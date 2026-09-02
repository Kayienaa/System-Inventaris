<?php

namespace Tests\Feature;

use App\Models\Asset;
use App\Models\AuditLog;
use App\Models\User;
use App\Services\AuditLogService;
use Database\Seeders\AssetCategorySeeder;
use Database\Seeders\AssetSeeder;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuditLogTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolePermissionSeeder::class);
        $this->seed(AssetCategorySeeder::class);
        $this->seed(AssetSeeder::class);
    }

    public function test_non_admin_cannot_access_audit_logs_panel(): void
    {
        $siswa = User::factory()->create();
        $siswa->assignRole('siswa');

        $response = $this->actingAs($siswa)->get(route('admin.audit-logs.index'));
        $response->assertStatus(403);
    }

    public function test_admin_can_view_and_filter_audit_logs(): void
    {
        $admin = User::factory()->create(['name' => 'Super Admin']);
        $admin->assignRole('admin');

        $asset = Asset::first();

        $auditService = app(AuditLogService::class);
        $auditService->record(
            $admin,
            'asset.updated',
            $asset,
            ['name' => 'Old Name'],
            ['name' => 'New Name'],
            ['ip_address' => '127.0.0.1', 'user_agent' => 'Mozilla/5.0']
        );

        $response = $this->actingAs($admin)->get(route('admin.audit-logs.index'));

        $response->assertStatus(200);
        $response->assertSee('Audit Log Sistem');
        $response->assertSee('asset.updated');
        $response->assertSee('Super Admin');
        $response->assertSee('127.0.0.1');

        // Test filter search
        $searchResponse = $this->actingAs($admin)->get(route('admin.audit-logs.index', ['search' => 'asset.updated']));
        $searchResponse->assertStatus(200);
        $searchResponse->assertSee('asset.updated');
    }
}
