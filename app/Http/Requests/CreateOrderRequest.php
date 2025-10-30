<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateOrderRequest extends FormRequest
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
            'user_address_id' => 'required|exists:user_addresses,id',
            'delivery_time' => 'required|date',
            'delivery_status' => 'sometimes|string',
            'items' => 'required|array|min:1',
            'items.*.good_id' => 'required|exists:goods,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.good_option_id' => 'nullable|exists:good_options,id'
        ];
    }
}
