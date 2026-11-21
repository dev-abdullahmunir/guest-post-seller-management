<?php

namespace Modules\SellerManagement\Http\Controllers;

use App\Http\Controllers\Controller;
use Modules\SellerManagement\Models\Website;
use Modules\SellerManagement\Http\Requests\StoreWebsiteRequest;
use Modules\SellerManagement\Http\Requests\UpdateWebsiteRequest;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Response;

class WebsiteController extends Controller
{
    /**
     * Display a listing of websites inventory.
     */
    public function index(Request $request): JsonResponse
    {
        $query = Website::with('vendor');

        if ($request->has('search')) {
            $search = $request->get('search');
            $query->where('website_name', 'like', "%{$search}%")
                  ->orWhere('website_url', 'like', "%{$search}%")
                  ->orWhere('niche', 'like', "%{$search}%");
        }

        if ($request->has('vendor_id')) {
            $query->where('vendor_id', $request->get('vendor_id'));
        }

        if ($request->has('niche')) {
            $query->where('niche', $request->get('niche'));
        }

        $websites = $query->latest()->paginate(15);

        return Response::json([
            'status' => 'success',
            'data' => $websites
        ]);
    }

    /**
     * Store a newly created website in inventory.
     */
    public function store(StoreWebsiteRequest $request): JsonResponse
    {
        $website = Website::create($request->validated());

        return Response::json([
            'status' => 'success',
            'message' => 'Website added to inventory successfully.',
            'data' => $website->load('vendor')
        ], 201);
    }

    /**
     * Display the specified website.
     */
    public function show($id): JsonResponse
    {
        $website = Website::with('vendor')->findOrFail($id);

        return Response::json([
            'status' => 'success',
            'data' => $website
        ]);
    }

    /**
     * Update the specified website.
     */
    public function update(UpdateWebsiteRequest $request, $id): JsonResponse
    {
        $website = Website::findOrFail($id);
        $website->update($request->validated());

        return Response::json([
            'status' => 'success',
            'message' => 'Website updated successfully.',
            'data' => $website->load('vendor')
        ]);
    }

    /**
     * Remove the specified website (Soft delete).
     */
    public function destroy($id): JsonResponse
    {
        $website = Website::findOrFail($id);
        $website->delete();

        return Response::json([
            'status' => 'success',
            'message' => 'Website removed from inventory.'
        ]);
    }
}
