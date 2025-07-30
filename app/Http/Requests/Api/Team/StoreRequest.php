<?php

namespace App\Http\Requests\Api\Team;

use Illuminate\Foundation\Http\FormRequest;

class StoreRequest extends FormRequest
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
            'name' => 'required',
            'logo_url' => 'required',
            'founding_year' => 'required',
            'address' => 'required',
            'city' => 'required',
        ];
    }

    public function attributes(): array
    {
        return [
            'name' => 'Name',
            'logo_url' => 'Logo URL',
            'founding_year' => 'Founding Year',
            'address' => 'Address',
            'city' => 'City',
        ];
    }
}
