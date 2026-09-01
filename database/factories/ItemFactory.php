<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Item;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Item>
 */
class ItemFactory extends Factory
{
    protected $model = Item::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'category_id' => Category::factory(),
            'kode_unik' => strtoupper(fake()->unique()->bothify('ITEM-###-???')),
            'nama_barang' => fake()->words(3, true),
            'merk' => fake()->randomElement(['Asus', 'Acer', 'Lenovo', 'HP', 'Dell', 'Samsung', 'Canon', 'Sony', 'Epson']),
            'lokasi_ruangan' => fake()->randomElement(['Ruang TEFA 1', 'Ruang TEFA 2', 'Studio TEFA', 'Laboratorium Multimedia']),
            'status' => fake()->randomElement(['Tersedia', 'Dipinjam', 'Maintenance', 'Rusak']),
            'gambar' => null,
        ];
    }
}
