<?php

namespace Database\Seeders;

use App\Models\Animal;
use App\Models\Enclosure;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class EnclosureSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::all();

        foreach ($users as $user) {
            $tmpUsers = $users->where('id', '!=', $user->id)->random(5);

            Enclosure::factory()
                ->hasAttached($tmpUsers)
                ->create();
        }
    }
}
