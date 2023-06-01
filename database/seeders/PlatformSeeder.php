<?php

namespace Database\Seeders;

use App\Models\Platform;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PlatformSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $types = Platform::PLATFORM_TYPES;
        foreach ($types as $key => $value) {
            DB::table(Platform::retrieveTableName())
                ->insert([
                    'name' => $value['name'],
                    'description' => $value['description'],
                    'logo' => $value['logo']
                ]);
        };
    }
}
