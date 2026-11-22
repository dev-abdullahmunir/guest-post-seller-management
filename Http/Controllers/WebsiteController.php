<?php

namespace Modules\SellerManagement\Http\Controllers;

use App\Http\Controllers\Controller;
use Modules\Shared\Models\Website;
use Modules\Shared\Models\Seller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class WebsiteController extends Controller
{
    /**
     * Render the Websites Inventory Blade view.
     */
    public function renderView($viewFactory)
    {
        try {
            $websites = Website::with('vendor')->latest()->get()->toArray();
            $sellers = Seller::orderBy('name')->get()->toArray();
        } catch (\Exception $e) {
            $websites = [];
            $sellers = [];
        }

        return $viewFactory->make('websites.index', [
            'websites' => $websites,
            'sellers' => $sellers,
        ]);
    }

    /**
     * Display a listing of websites (API / JSON).
     */
    public function index(Request $request): JsonResponse
    {
        $websites = Website::with('vendor')->latest()->get();

        return response()->json([
            'status' => 'success',
            'data' => $websites
        ]);
    }

    /**
     * Store a newly created website.
     */
    public function store(array $body): array
    {
        if (empty(trim($body['website_name'] ?? '')) || empty(trim($body['website_url'] ?? '')) || empty($body['vendor_id'])) {
            http_response_code(422);
            return ['status' => 'error', 'message' => 'Website Name, URL, and Vendor are required.'];
        }

        $website = Website::create([
            'vendor_id' => (int)$body['vendor_id'],
            'website_name' => trim($body['website_name']),
            'website_url' => trim($body['website_url']),
            'niche' => trim($body['niche'] ?? 'General'),
            'domain_authority' => !empty($body['domain_authority']) ? (int)$body['domain_authority'] : null,
            'domain_rating' => !empty($body['domain_rating']) ? (int)$body['domain_rating'] : null,
            'traffic' => !empty($body['traffic']) ? (int)$body['traffic'] : null,
            'vendor_price' => (float)($body['vendor_price'] ?? 0),
            'turnaround_days' => !empty($body['turnaround_days']) ? (int)$body['turnaround_days'] : null,
            'status' => $body['status'] ?? 'active',
            'notes' => trim($body['notes'] ?? null) ?: null,
        ]);

        return [
            'status' => 'success',
            'message' => 'Website added to inventory successfully!',
            'data' => $website->load('vendor')
        ];
    }

    /**
     * Display the specified website.
     */
    public function show($id): array
    {
        $website = Website::with('vendor')->findOrFail($id);
        return [
            'status' => 'success',
            'data' => $website
        ];
    }

    /**
     * Update the specified website.
     */
    public function update($id, array $body): array
    {
        $website = Website::findOrFail($id);

        $website->update([
            'vendor_id' => !empty($body['vendor_id']) ? (int)$body['vendor_id'] : $website->vendor_id,
            'website_name' => !empty($body['website_name']) ? trim($body['website_name']) : $website->website_name,
            'website_url' => !empty($body['website_url']) ? trim($body['website_url']) : $website->website_url,
            'niche' => !empty($body['niche']) ? trim($body['niche']) : $website->niche,
            'domain_authority' => array_key_exists('domain_authority', $body) ? (!empty($body['domain_authority']) ? (int)$body['domain_authority'] : null) : $website->domain_authority,
            'domain_rating' => array_key_exists('domain_rating', $body) ? (!empty($body['domain_rating']) ? (int)$body['domain_rating'] : null) : $website->domain_rating,
            'traffic' => array_key_exists('traffic', $body) ? (!empty($body['traffic']) ? (int)$body['traffic'] : null) : $website->traffic,
            'vendor_price' => isset($body['vendor_price']) ? (float)$body['vendor_price'] : $website->vendor_price,
            'turnaround_days' => array_key_exists('turnaround_days', $body) ? (!empty($body['turnaround_days']) ? (int)$body['turnaround_days'] : null) : $website->turnaround_days,
            'status' => $body['status'] ?? $website->status,
            'notes' => array_key_exists('notes', $body) ? (trim($body['notes'] ?? null) ?: null) : $website->notes,
        ]);

        return [
            'status' => 'success',
            'message' => 'Website inventory updated successfully!',
            'data' => $website->fresh()->load('vendor')
        ];
    }

    /**
     * Remove the specified website.
     */
    public function destroy($id): array
    {
        $website = Website::findOrFail($id);
        $website->delete();

        return [
            'status' => 'success',
            'message' => 'Website removed from inventory successfully.'
        ];
    }

    /**
     * Bulk import websites from Excel/CSV parsed payload.
     */
    public function bulkImport(array $body): array
    {
        $websites = $body['websites'] ?? [];
        if (!is_array($websites) || empty($websites)) {
            http_response_code(422);
            return ['status' => 'error', 'message' => 'No website data provided for import.'];
        }

        $defaultVendorId = !empty($body['vendor_id']) ? (int)$body['vendor_id'] : null;
        if (!$defaultVendorId) {
            $firstVendor = Seller::first();
            $defaultVendorId = $firstVendor ? $firstVendor->id : null;
        }

        if (!$defaultVendorId) {
            http_response_code(422);
            return ['status' => 'error', 'message' => 'No active vendor found. Please register at least one vendor first.'];
        }

        $duplicateAction = $body['duplicate_action'] ?? 'skip'; // 'update' or 'skip'

        $imported = 0;
        $updated = 0;
        $skipped = 0;

        foreach ($websites as $row) {
            $rawUrl = trim($row['website_url'] ?? $row['url'] ?? $row['domain'] ?? '');
            if (empty($rawUrl)) {
                $skipped++;
                continue;
            }

            // Clean domain and URL
            $cleanDomain = preg_replace('#^https?://#i', '', $rawUrl);
            $cleanDomain = preg_replace('#^www\.#i', '', $cleanDomain);
            $cleanDomain = rtrim($cleanDomain, '/');

            $name = trim($row['website_name'] ?? '');
            if (empty($name)) {
                $name = ucfirst(explode('.', $cleanDomain)[0] ?? $cleanDomain);
            }

            $formattedUrl = (strpos($rawUrl, 'http://') === 0 || strpos($rawUrl, 'https://') === 0) 
                ? $rawUrl 
                : 'https://' . $cleanDomain;

            $vendorId = !empty($row['vendor_id']) ? (int)$row['vendor_id'] : $defaultVendorId;
            $niche = trim($row['niche'] ?? 'General') ?: 'General';
            $da = isset($row['domain_authority']) && is_numeric($row['domain_authority']) ? (int)$row['domain_authority'] : null;
            $dr = isset($row['domain_rating']) && is_numeric($row['domain_rating']) ? (int)$row['domain_rating'] : null;
            $traffic = isset($row['traffic']) && is_numeric($row['traffic']) ? (int)$row['traffic'] : null;
            $price = isset($row['vendor_price']) && is_numeric($row['vendor_price']) ? (float)$row['vendor_price'] : 0.0;
            $tat = isset($row['turnaround_days']) && is_numeric($row['turnaround_days']) ? (int)$row['turnaround_days'] : 3;
            $notes = trim($row['notes'] ?? '') ?: null;

            // Search for existing website by clean domain or URL
            $existing = Website::where('website_url', $formattedUrl)
                ->orWhere('website_url', 'like', '%' . $cleanDomain . '%')
                ->first();

            if ($existing) {
                if ($duplicateAction === 'update') {
                    $existing->update([
                        'website_name' => $name,
                        'niche' => $niche,
                        'domain_authority' => $da !== null ? $da : $existing->domain_authority,
                        'domain_rating' => $dr !== null ? $dr : $existing->domain_rating,
                        'traffic' => $traffic !== null ? $traffic : $existing->traffic,
                        'vendor_price' => $price > 0 ? $price : $existing->vendor_price,
                        'turnaround_days' => $tat ?: $existing->turnaround_days,
                        'notes' => $notes ?: $existing->notes,
                    ]);
                    $updated++;
                } else {
                    $skipped++;
                }
            } else {
                Website::create([
                    'vendor_id' => $vendorId,
                    'website_name' => $name,
                    'website_url' => $formattedUrl,
                    'niche' => $niche,
                    'domain_authority' => $da,
                    'domain_rating' => $dr,
                    'traffic' => $traffic,
                    'vendor_price' => $price,
                    'turnaround_days' => $tat,
                    'status' => 'active',
                    'notes' => $notes,
                ]);
                $imported++;
            }
        }

        return [
            'status' => 'success',
            'message' => "Bulk import completed! {$imported} added, {$updated} updated, {$skipped} skipped.",
            'imported' => $imported,
            'updated' => $updated,
            'skipped' => $skipped,
            'total' => count($websites),
        ];
    }

    /**
     * Download Sample CSV Template.
     */
    public function sampleTemplate()
    {
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename=websites_inventory_template.csv');

        $output = fopen('php://output', 'w');
        fputcsv($output, [
            'Website Name',
            'Website URL',
            'Niche',
            'Domain Authority (DA)',
            'Domain Rating (DR)',
            'Monthly Traffic',
            'Vendor Cost Price',
            'Turnaround Days (TAT)',
            'Notes'
        ]);

        fputcsv($output, [
            'TechCrunch Media',
            'https://techcrunch.com',
            'Technology & AI',
            '93',
            '92',
            '12500000',
            '350.00',
            '4',
            'High authority tech outlet. Do-follow link included.'
        ]);

        fputcsv($output, [
            'Forbes Finance Network',
            'https://forbes.com',
            'Finance & Business',
            '95',
            '94',
            '25000000',
            '450.00',
            '3',
            'Editorial review takes 48 hours.'
        ]);

        fputcsv($output, [
            'Healthline Magazine',
            'https://healthline.com',
            'Health & Wellness',
            '88',
            '87',
            '8000000',
            '220.00',
            '5',
            'No casino or adult content allowed.'
        ]);

        fclose($output);
        exit;
    }
}
