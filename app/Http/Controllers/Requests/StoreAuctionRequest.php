<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreAuctionRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'title' => 'required|string|max:1000',
            'image' => 'nullable|string',
            'description' => 'nullable|string',
            'auction_date' => 'nullable|date',
            'condition1' => 'required|string|max:1000',
            'condition2' => 'required|string|max:1000',
            'condition3' => 'required|string|max:1000',
            'condition4' => 'required|string|max:1000',
            'condition5' => 'required|string|max:1000',
            'condition6' => 'required|string|max:1000',
            'condition7' => 'required|string|max:1000',
        ];
    }
}