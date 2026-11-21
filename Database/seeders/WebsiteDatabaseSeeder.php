<?php

namespace Modules\SellerManagement\Database\seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class WebsiteDatabaseSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('websites')->insertOrIgnore([
            [
                'id' => 1,
                'vendor_id' => 1,
                'website_name' => 'TechCrunch Outreach Contributor',
                'website_url' => 'https://techcrunch.com',
                'niche' => 'Technology & Startups',
                'domain_authority' => 93,
                'domain_rating' => 92,
                'traffic' => 12500000,
                'vendor_price' => 350.00,
                'turnaround_days' => 4,
                'status' => 'active',
                'notes' => 'Dofollow editorial link. Requires well-researched 1000+ words tech article.',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'id' => 2,
                'vendor_id' => 1,
                'website_name' => 'VentureBeat Contributor Network',
                'website_url' => 'https://venturebeat.com',
                'niche' => 'AI & Enterprise Tech',
                'domain_authority' => 91,
                'domain_rating' => 89,
                'traffic' => 8200000,
                'vendor_price' => 280.00,
                'turnaround_days' => 3,
                'status' => 'active',
                'notes' => 'Fast turnaround. Accepts AI, cloud computing, and cybersecurity anchors.',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'id' => 3,
                'vendor_id' => 2,
                'website_name' => 'London Business Review Net',
                'website_url' => 'https://londonbusinessreview.co.uk',
                'niche' => 'Finance & Business',
                'domain_authority' => 65,
                'domain_rating' => 68,
                'traffic' => 450000,
                'vendor_price' => 75.00,
                'turnaround_days' => 2,
                'status' => 'active',
                'notes' => 'High engagement UK corporate blog. Fast indexing within 24 hours.',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'id' => 4,
                'vendor_id' => 3,
                'website_name' => 'CoinWorld Insider',
                'website_url' => 'https://coinworldinsider.net',
                'niche' => 'Crypto & Web3',
                'domain_authority' => 58,
                'domain_rating' => 61,
                'traffic' => 320000,
                'vendor_price' => 90.00,
                'turnaround_days' => 2,
                'status' => 'active',
                'notes' => 'Web3, crypto, DeFi news portal. Google News approved.',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'id' => 5,
                'vendor_id' => 3,
                'website_name' => 'SaaS Flow Growth Magazine',
                'website_url' => 'https://saasflowmagazine.io',
                'niche' => 'SaaS & Marketing',
                'domain_authority' => 54,
                'domain_rating' => 56,
                'traffic' => 180000,
                'vendor_price' => 60.00,
                'turnaround_days' => 2,
                'status' => 'active',
                'notes' => 'Great for marketing software, CRM, and productivity tool link building.',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
        ]);
    }
}
