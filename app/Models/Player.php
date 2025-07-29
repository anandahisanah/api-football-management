<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Player extends Model
{
    use SoftDeletes;
    use HasUuids;

    protected $fillable = [
        // fk
        'team_id',
        // column
        'name',
        'body_height',
        'body_weight',
        'position',
        'back_number',
    ];

    public function team()
    {
        return $this->belongsTo(Team::class);
    }

    public function scores()
    {
        return $this->hasMany(Score::class);
    }
}
