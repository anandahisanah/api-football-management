<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Player extends Model
{
    use HasUuid;
    use SoftDeletes;

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
