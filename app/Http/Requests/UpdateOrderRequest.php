<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateOrderRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'user_address_id' => 'sometimes|required|ts:user_addresses,id',
            'delivery_time' => 'sometimes|required|date',
            'delivery_status' => 'sometimes|required|string',
            'items' => 'sometimes|required|array|min:1',
            'items.*.good_id' => 'sometimes|required|exists:goods,id',
            'items.*.quantity' => 'sometimes|required|integer|min:1',
            'items.*.good_option_id' => 'sometimes|nullable|exists:good_options,id'
        ];
    }
}
