<?php

namespace App\Http\Requests\Api\Game;

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
            // fk
            'home_team_id' => 'required|string|uuid',
            'away_team_id' => 'required|string|uuid',
            // column
            'location' => 'required',
            'datetime' => 'required|date_format:Y-m-d H:i:s',
        ];
    }

    public function attributes(): array
    {
        return [
            // fk
            'home_team_id' => 'Home Team Id',
            'away_team_id' => 'Away Team Id',
            // column
            'location' => 'Location',
            'datetime' => 'Datetime',
        ];
    }
}
