<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $path = database_path('data/categories.json');
        $json = file_get_contents($path);
        $data = json_decode($json, true);

        // Use the correct connection for disabling foreign key checks
        DB::connection('landlord')->statement('SET FOREIGN_KEY_CHECKS=0;');

        // Truncate the table on the correct connection
        DB::connection('landlord')->table('categories')->truncate();

        // Insert data using the model
        Category::on('landlord')->insert($data);

        // Re-enable foreign key checks on the correct connection
        DB::connection('landlord')->statement('SET FOREIGN_KEY_CHECKS=1;');
    }
}
