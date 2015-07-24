<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BracketMatch extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'bracket_matches';

    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['match_id', 'winner_match_id', 'winner_match_venue', 'loser_match_id', 'loser_match_venue'];

    public function winnerMatch()
    {
        return $this->hasOne('App\Models\Match', 'id', 'winner_match_id');
    }

    public function loserMatch()
    {
        return $this->hasOne('App\Models\Match', 'id', 'loser_match_id');
    }

}
