<?php

namespace Database\Seeders;

// ! use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class KeluhanPelangganSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        \App\Models\KeluhanPelanggan::factory(50)->create();
    }
}


