<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Score extends Model
{
    use SoftDeletes;
    use HasUuids;

    protected $fillable = [
        // fk
        'game_id',
        'team_id',
        'player_id',
        // column
        'datetime',
    ];

    public function game()
    {
        return $this->belongsTo(related: Game::class);
    }

    public function team()
    {
        return $this->belongsTo(Team::class);
    }

    public function player()
    {
        return $this->belongsTo(Player::class);
    }
}
