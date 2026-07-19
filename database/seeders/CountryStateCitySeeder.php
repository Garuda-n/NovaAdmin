<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CountryStateCitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Schema::disableForeignKeyConstraints();

        DB::table('cities')->truncate();
        DB::table('states')->truncate();
        DB::table('countries')->truncate();

        $now = now()->toDateTimeString();
        $dataPath = database_path('seeders/data');

        // 1. Countries
        $countriesFile = $dataPath . '/countries.json';
        if (file_exists($countriesFile)) {
            $countries = json_decode(file_get_contents($countriesFile), true);
            if (!empty($countries)) {
                $rows = array_map(function ($item) use ($now) {
                    return array_merge($item, [
                        'created_at' => $now,
                        'updated_at' => $now,
                    ]);
                }, $countries);

                foreach (array_chunk($rows, 1000) as $chunk) {
                    DB::table('countries')->insert($chunk);
                }
            }
        }

        // 2. States
        $statesFile = $dataPath . '/states.json';
        if (file_exists($statesFile)) {
            $states = json_decode(file_get_contents($statesFile), true);
            if (!empty($states)) {
                $rows = array_map(function ($item) use ($now) {
                    return array_merge($item, [
                        'created_at' => $now,
                        'updated_at' => $now,
                    ]);
                }, $states);

                foreach (array_chunk($rows, 1000) as $chunk) {
                    DB::table('states')->insert($chunk);
                }
            }
        }

        // 3. Cities
        $citiesFile = $dataPath . '/cities.json';
        if (file_exists($citiesFile)) {
            $cities = json_decode(file_get_contents($citiesFile), true);
            if (!empty($cities)) {
                $rows = array_map(function ($item) use ($now) {
                    return array_merge($item, [
                        'created_at' => $now,
                        'updated_at' => $now,
                    ]);
                }, $cities);

                foreach (array_chunk($rows, 1000) as $chunk) {
                    DB::table('cities')->insert($chunk);
                }
            }
        }

        Schema::enableForeignKeyConstraints();
    }
}
