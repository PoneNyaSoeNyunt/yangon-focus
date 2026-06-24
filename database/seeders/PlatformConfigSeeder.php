<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PlatformConfigSeeder extends Seeder
{
    public function run(): void
    {
        $configs = [
            ['key' => 'monthly_subscription_fee', 'value' => '5000'],
        ];

        foreach ($configs as $config) {
            // Check if this config key already exists in the table
            $exists = DB::table('platform_configs')
                        ->where('key', $config['key'])
                        ->exists();

            // Only insert if it is missing entirely
            if (!$exists) {
                DB::table('platform_configs')->insert([
                    'key'        => $config['key'],
                    'value'      => $config['value'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }
}
