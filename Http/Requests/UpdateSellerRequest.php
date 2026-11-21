<?php

namespace Modules\SellerManagement\Http\Requests;

use App\Http\Requests\FormRequest;

class UpdateSellerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'sometimes|required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:50',
            'company_name' => 'nullable|string|max:255',
            'country' => 'nullable|string|max:100',
            'notes' => 'nullable|string',
        ];
    }
}
