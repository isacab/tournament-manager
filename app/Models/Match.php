<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Match extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'matches';

    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['comment', 'state', 'round', 'home_id', 'away_id', 'winner_id', 'pool_id'];

    public function result()
    {
        return $this->hasMany('App\Models\Result');
    }

    public function pool()
    {
        return $this->belongsTo('App\Models\Pool');
    }

    public function bracketMatch()
    {
        return $this->hasOne('App\Models\BracketMatch');
    }

    public function loser()
    {
        if(!$this->home_id && !$this->away_id)
            return null;

        if($this->winner_id == $this->home_id)
            return $this->away_id;
        else if($this->winner_id = $this->away_id)
            return $this->home_id;
        else
            return null;
    }

    /**
     * Returns true if the match has invalid ids both for home and away
     * 
     * @return boolean
     */
    public function isEmpty()
    {
        return $this->home_id == null && $this->away_id == null;
    }

    /**
     * Returns true if the match has valid ids for both home and away
     * 
     * @return boolean
     */
    public function isFull()
    {
        return $this->home_id != null && $this->away_id != null;
    }

    /**
     * Returns true if the match has one valid and one invalid id
     * 
     * @return boolean
     */
    public function isHalfFull()
    {
        return !$this->isEmpty() && !$this->isFull();
    }

}
