<?php

namespace Database\Seeders;

use App\Models\Animal;
use App\Models\Enclosure;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AnimalSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $enclosures = Enclosure::all();

        foreach ($enclosures as $enclosure) {
            $animals = Animal::factory(rand(1, $enclosure->limit))->create(['is_predator' => fake()->boolean(), 'enclosure_id' => $enclosure->id]);
        }
    }
}
