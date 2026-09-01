<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Item;
use App\Models\User;
use Database\Seeders\CategorySeeder;
use Database\Seeders\ItemSeeder;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ItemBorrowingTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RolePermissionSeeder::class);
        $this->seed(CategorySeeder::class);
        $this->seed(ItemSeeder::class);
    }

    public function test_user_can_view_katalog_page(): void
    {
        $user = User::factory()->create();
        $user->assignRole('siswa');

        $response = $this->actingAs($user)->get(route('items.index'));

        $response->assertStatus(200);
        $response->assertSee('Laptop Asus Vivobook #6');
    }

    public function test_user_can_view_borrow_form_for_available_item_6(): void
    {
        $user = User::factory()->create();
        $user->assignRole('siswa');

        $item = Item::find(6);
        $this->assertNotNull($item);
        $this->assertTrue($item->isAvailable());

        $response = $this->actingAs($user)->get(route('items.borrow', $item));

        $response->assertStatus(200);
        $response->assertSee('Laptop Asus Vivobook #6');
        $response->assertSee('LPT-ASUS-006');
    }

    public function test_user_cannot_view_borrow_form_for_unavailable_item(): void
    {
        $user = User::factory()->create();
        $user->assignRole('siswa');

        $item = Item::create([
            'category_id' => Category::first()->id,
            'kode_unik' => 'TEST-UNAVAIL-01',
            'nama_barang' => 'Barang Rusak',
            'status' => 'Rusak',
        ]);

        $response = $this->actingAs($user)->get(route('items.borrow', $item));

        $response->assertStatus(404);
    }

    public function test_user_can_submit_borrowing_request_for_item_6(): void
    {
        Storage::fake('public');

        $user = User::factory()->create();
        $user->assignRole('siswa');

        $item = Item::find(6);

        $file = UploadedFile::fake()->create('evidence.jpg', 100, 'image/jpeg');

        $response = $this->actingAs($user)->post(route('items.borrow.store', $item), [
            'due_at' => now()->addDays(2)->format('Y-m-d H:i:s'),
            'borrower_note' => 'Keperluan praktikum TEFA',
            'borrowing_evidence' => $file,
        ]);

        $response->assertRedirect(route('borrowings.mine'));
        $response->assertSessionHas('success', 'Barang berhasil dipinjam.');

        $this->assertDatabaseHas('borrowings', [
            'borrower_user_id' => $user->id,
            'item_id' => $item->id,
            'asset_id' => null,
            'status' => 'borrowed',
            'borrower_note' => 'Keperluan praktikum TEFA',
        ]);

        $item->refresh();
        $this->assertSame('Dipinjam', $item->status);

        // Verify /borrowings/mine renders item name correctly
        $mineResponse = $this->actingAs($user)->get(route('borrowings.mine'));
        $mineResponse->assertStatus(200);
        $mineResponse->assertSee('Laptop Asus Vivobook #6');
        $mineResponse->assertSee('LPT-ASUS-006');
    }
}
