<?php

namespace Modules\SellerManagement\Database\seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SellerDatabaseSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('sellers')->insertOrIgnore([
            [
                'id' => 1,
                'name' => 'Ahmed Hassan',
                'email' => 'ahmed@techoutreach.com',
                'phone' => '+971501234567',
                'company_name' => 'Tech Outreach Media',
                'country' => 'United Arab Emirates',
                'notes' => 'Primary vendor for TechCrunch, Forbes, & Entrepreneur contributor accounts.',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'id' => 2,
                'name' => 'David Miller',
                'email' => 'david@millerpublishing.co.uk',
                'phone' => '+442079460912',
                'company_name' => 'Miller Publishing Group',
                'country' => 'United Kingdom',
                'notes' => 'High DR UK & European lifestyle and business blogs with fast turnaround.',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'id' => 3,
                'name' => 'Elena Rostova',
                'email' => 'elena@highdrmedia.net',
                'phone' => '+16465550188',
                'company_name' => 'High-DR Media Net',
                'country' => 'United States',
                'notes' => 'SaaS and Crypto webmaster. Direct editor access on 40+ verified domains.',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
        ]);
    }
}
