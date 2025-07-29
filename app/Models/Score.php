<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Score extends Model
{
    use HasUuid;
    use SoftDeletes;

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
