<?php

namespace App\Http\Requests\Api\Player;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

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
            'team_id' => ['required', 'string', 'uuid', Rule::exists('teams', 'id')],
            // column
            'name' => ['required', 'string', 'max:255'],
            'body_height' => ['required', 'integer'],
            'body_weight' => ['required', 'integer'],
            'position' => ['required', 'string'],
            'back_number' => [
                'required',
                'integer',
                'min:1',
                'max:99',
                // unique
                Rule::unique('players')->where(function ($query) {
                    return $query->where('team_id', $this->team_id);
                }),
            ],
        ];
    }

    public function attributes(): array
    {
        return [
            // fk
            'team_id' => 'Team Id',
            // column
            'name' => 'Name',
            'body_height' => 'Body Height',
            'body_weight' => 'Body Weight',
            'position' => 'Position',
            'back_number' => 'Back Number',
        ];
    }
}
