<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Game extends Model
{
    use SoftDeletes;
    use HasUuids;

    protected $fillable = [
        // fk
        'home_team_id',
        'away_team_id',
        // column
        'location',
        'datetime',
    ];

    public function home_team()
    {
        return $this->belongsTo(Team::class, 'home_team_id', 'id');
    }

    public function away_team()
    {
        return $this->belongsTo(Team::class, 'away_team_id', 'id');
    }

    public function scores()
    {
        return $this->hasMany(Score::class);
    }
}
