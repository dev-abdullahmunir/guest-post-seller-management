<?php

namespace Modules\SellerManagement\Http\Controllers;

use App\Http\Controllers\Controller;
use Modules\Shared\Models\Seller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class SellerController extends Controller
{
    /**
     * Render the Sellers Blade view.
     */
    public function renderView($viewFactory)
    {
        try {
            $sellers = Seller::latest()->get()->toArray();
        } catch (\Exception $e) {
            $sellers = [];
        }

        return $viewFactory->make('sellers.index', [
            'sellers' => $sellers
        ]);
    }

    /**
     * Display a listing of sellers (API / JSON).
     */
    public function index(Request $request): JsonResponse
    {
        $sellers = Seller::latest()->get();

        return response()->json([
            'status' => 'success',
            'data' => $sellers
        ]);
    }

    /**
     * Store a newly created seller.
     */
    public function store(array $body): array
    {
        if (empty(trim($body['name'] ?? ''))) {
            http_response_code(422);
            return ['status' => 'error', 'message' => 'Seller Name is required.'];
        }

        $seller = Seller::create([
            'name' => trim($body['name']),
            'email' => trim($body['email'] ?? null) ?: null,
            'phone' => trim($body['phone'] ?? null) ?: null,
            'company_name' => trim($body['company_name'] ?? null) ?: null,
            'country' => trim($body['country'] ?? null) ?: null,
            'notes' => trim($body['notes'] ?? null) ?: null,
        ]);

        return [
            'status' => 'success',
            'message' => 'Seller added successfully!',
            'data' => $seller
        ];
    }

    /**
     * Display the specified seller.
     */
    public function show($id): array
    {
        $seller = Seller::findOrFail($id);
        return [
            'status' => 'success',
            'data' => $seller
        ];
    }

    /**
     * Update the specified seller.
     */
    public function update($id, array $body): array
    {
        if (empty(trim($body['name'] ?? ''))) {
            http_response_code(422);
            return ['status' => 'error', 'message' => 'Seller Name is required.'];
        }

        $seller = Seller::findOrFail($id);
        $seller->update([
            'name' => trim($body['name']),
            'email' => trim($body['email'] ?? null) ?: null,
            'phone' => trim($body['phone'] ?? null) ?: null,
            'company_name' => trim($body['company_name'] ?? null) ?: null,
            'country' => trim($body['country'] ?? null) ?: null,
            'notes' => trim($body['notes'] ?? null) ?: null,
        ]);

        return [
            'status' => 'success',
            'message' => 'Seller updated successfully!',
            'data' => $seller
        ];
    }

    /**
     * Remove the specified seller (Soft delete).
     */
    public function destroy($id): array
    {
        $seller = Seller::findOrFail($id);
        $seller->delete();

        return [
            'status' => 'success',
            'message' => 'Seller deleted successfully.'
        ];
    }
}
