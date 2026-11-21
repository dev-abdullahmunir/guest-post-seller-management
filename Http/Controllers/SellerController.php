<?php

namespace Modules\SellerManagement\Http\Controllers;

use App\Http\Controllers\Controller;
use Modules\SellerManagement\Models\Seller;
use Modules\SellerManagement\Http\Requests\StoreSellerRequest;
use Modules\SellerManagement\Http\Requests\UpdateSellerRequest;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Response;

class SellerController extends Controller
{
    /**
     * Display a listing of sellers/vendors.
     */
    public function index(Request $request): JsonResponse
    {
        $query = Seller::query();

        if ($request->has('search')) {
            $search = $request->get('search');
            $query->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('company_name', 'like', "%{$search}%");
        }

        $sellers = $query->latest()->paginate(15);

        return Response::json([
            'status' => 'success',
            'data' => $sellers
        ]);
    }

    /**
     * Store a newly created seller.
     */
    public function store(StoreSellerRequest $request): JsonResponse
    {
        $seller = Seller::create($request->validated());

        return Response::json([
            'status' => 'success',
            'message' => 'Seller / Vendor created successfully.',
            'data' => $seller
        ], 201);
    }

    /**
     * Display the specified seller.
     */
    public function show($id): JsonResponse
    {
        $seller = Seller::findOrFail($id);

        return Response::json([
            'status' => 'success',
            'data' => $seller
        ]);
    }

    /**
     * Update the specified seller.
     */
    public function update(UpdateSellerRequest $request, $id): JsonResponse
    {
        $seller = Seller::findOrFail($id);
        $seller->update($request->validated());

        return Response::json([
            'status' => 'success',
            'message' => 'Seller / Vendor updated successfully.',
            'data' => $seller
        ]);
    }

    /**
     * Remove the specified seller (Soft delete).
     */
    public function destroy($id): JsonResponse
    {
        $seller = Seller::findOrFail($id);
        $seller->delete();

        return Response::json([
            'status' => 'success',
            'message' => 'Seller / Vendor soft-deleted successfully.'
        ]);
    }
}
