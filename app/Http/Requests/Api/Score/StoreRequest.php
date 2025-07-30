<?php

namespace App\Http\Requests\Api\Score;

use App\Models\Game;
use Carbon\Carbon;
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
            'game_id'   => [
                'required',
                'string',
                'uuid',
                Rule::exists('games', 'id')
            ],
            'team_id'   => [
                'required',
                'string',
                'uuid',
                Rule::exists('teams', 'id'),
                function ($attribute, $value, $fail) {
                    $game = Game::findOrFail($this->game_id);

                    // check whether the given team_id is the home_team_id or away_team_id of the game
                    if ($value !== $game->home_team_id && $value !== $game->away_team_id) {
                        $fail('The selected team does not participate in this game.');
                    }
                },
            ],
            'player_id' => [
                'required',
                'string',
                'uuid',
                Rule::exists('players', 'id'),
                Rule::exists('players', 'id')->where(function ($query) {
                    $query->where('team_id', $this->team_id);
                })
            ],
            'datetime'  => [
                'required',
                'date_format:Y-m-d H:i:s',
                // unique
                Rule::unique('scores')->where(function ($query) {
                    return $query->where('game_id', $this->game_id);
                }),
                function ($attribute, $value, $fail) {
                    $game = Game::findOrFail($this->game_id);

                    $scoreDateTime = Carbon::parse($value);
                    $gameStartTime = Carbon::parse($game->datetime);

                    // final score time limit (game start time + 100 minutes)
                    $gameEndTimeLimit = $gameStartTime->copy()->addMinutes(100);

                    // scoring time must be after the game start time.
                    if ($scoreDateTime->lte($gameStartTime)) { // Less than or equal
                        $fail('The scoring time must be after the game start time.');
                    }

                    // the scoring time must not be more than 100 minutes after the game start time.
                    if ($scoreDateTime->gt($gameEndTimeLimit)) { // Greater than
                        $fail('The scoring time must not be more than 100 minutes after the game start time.');
                    }
                },
            ],
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
