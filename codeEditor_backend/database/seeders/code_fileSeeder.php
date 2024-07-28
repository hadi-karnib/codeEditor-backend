<?php

namespace Database\Seeders;

use App\Models\code_file;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class code_fileSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        code_file::factory(10)->create();
    }
}
