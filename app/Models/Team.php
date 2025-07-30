<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Team extends Model
{
    use SoftDeletes;
    use HasUuids;

    protected $fillable = [
        // column
        'name',
        'logo_url',
        'founding_year',
        'address',
        'city',
    ];

    public function players()
    {
        return $this->hasMany(Player::class);
    }

    public function home_games()
    {
        return $this->hasMany(Game::class, 'home_team_id', 'id');
    }

    public function away_games()
    {
        return $this->hasMany(Game::class, 'away_team_id', 'id');
    }

    public function scores()
    {
        return $this->hasMany(Score::class);
    }
}
