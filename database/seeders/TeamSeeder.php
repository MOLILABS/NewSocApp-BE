<?php

namespace Database\Seeders;

use App\Models\Team;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TeamSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $platform = file(storage_path() . "/resource/teams.txt", FILE_IGNORE_NEW_LINES);
        $platform = collect($platform)->map(function ($item) {
            $separate = explode(":", $item);
            return [
                'name' => $separate[0],
                'description' => $separate[1],
            ];
        })->toArray();
        DB::table(Team::retrieveTableName())
            ->insert($platform);
    }
}
