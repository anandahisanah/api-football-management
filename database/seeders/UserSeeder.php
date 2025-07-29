<?php

namespace Database\Seeders;

use App\Models\User;
use Faker\Factory as Faker;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $this->truncateModel();
        $this->createDefaultUser();
        $this->createUser();
    }

    function truncateModel()
    {
        User::query()->delete();
    }

    function createDefaultUser()
    {
        User::create([
            'name' => 'Fullname Admin',
            'email' => 'admin@admin.com',
            'password' => bcrypt('adminadmin'),
        ]);
    }

    function createUser()
    {
        $faker = Faker::create();

        $total = 10;

        $this->command->getOutput()->progressStart($total);

        for ($i = 1; $i <= $total; $i++) {
            User::create([
                'name' => $faker->name,
                'email' => $faker->email(),
                'password' => bcrypt('adminadmin'),
            ]);

            // progress bar
            $this->command->getOutput()->progressAdvance();
        }

        $this->command->getOutput()->progressFinish();

        $this->command->info("\n User seeding completed!");
    }
}
