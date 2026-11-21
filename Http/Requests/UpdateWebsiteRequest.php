<?php

namespace Modules\SellerManagement\Http\Requests;

use App\Http\Requests\FormRequest;

class UpdateWebsiteRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'vendor_id' => 'sometimes|required|exists:sellers,id',
            'website_name' => 'sometimes|required|string|max:255',
            'website_url' => 'sometimes|required|string|max:255',
            'niche' => 'sometimes|required|string|max:100',
            'domain_authority' => 'nullable|integer|min:0|max:100',
            'domain_rating' => 'nullable|integer|min:0|max:100',
            'traffic' => 'nullable|integer|min:0',
            'vendor_price' => 'sometimes|required|numeric|min:0',
            'turnaround_days' => 'nullable|integer|min:1',
            'status' => 'nullable|in:active,inactive,paused',
            'notes' => 'nullable|string',
        ];
    }
}
