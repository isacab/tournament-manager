<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Stage extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name', 'type', 'meetings', 'thirdPrize'];

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'stages';

    public function tournament()
    {
    	return $this->belongsTo('App\Models\Tournament');
    }

    public function pools()
    {
        return $this->hasMany('App\Models\Pool');
    }

    /**
     * Get the scheduling class for this stage.
     */
    public function getScheduler()
    {
        if(!$this->type)
            return null;

        $scheduler = 'App\TournamentManager\Schedulers\\'. $this->type. 'Scheduler';

        return new $scheduler();
    }
}

