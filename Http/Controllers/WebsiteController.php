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
}
