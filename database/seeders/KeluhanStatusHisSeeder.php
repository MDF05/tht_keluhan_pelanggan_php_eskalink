<?php

namespace Database\Seeders;

// ! use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class KeluhanStatusHisSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        \App\Models\KeluhanStatusHis::factory(50)->create();
    }
}


