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
    protected $fillable = ['name', 'type', 'status', 'doubleMeatings', 'thirdPrize'];

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

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
     * Create pools and matches and set status to "InProgress".
     *
     * @var array $data     example: array(
     *                                     array(
     *                                         "name" => "Group A",
     *                                         "competitors" => array(1,2,3,null,4,5,6,7)
     *                                     ),
     *                               )
     * @return  boolean
     */
    public function start($data)
    {
        $scheduler = $this->getScheduler();

        if(!$scheduler || empty($data))
            return false;

        foreach ($data as $dataLine) 
        {
            $competitors = array();

            if(isset($dataLine['competitors']))
                $competitors = $dataLine['competitors'];

            $pool = $this->pools()->create($dataLine);

            $pool->competitors()->attach($competitors);

            $scheduler->createMatches(
                $pool->id, $competitors, ['thirdPrize' => $this->thirdPrize, 'doubleMeatings' => $this->doubleMeatings]
            );
        }

        $this->status = 'InProgress';

        $this->save();

        return true;
    }

    /**
     * Delete pools and matches and set status to "NotStarted".
     */
    public function reset()
    {
        $this->pools()->delete();

        $this->status = 'NotStarted';

        $this->save();
    }
    
    /**
     * Set status to "Finished".
     */
    public function finalize()
    {
        $this->status = 'Finished';

        $this->save();
    }

    /**
     * Set status to "InProgress".
     */
    public function resume()
    {
        $this->status = 'InProgress';

        $this->save();
    }

    /**
     * Get the scheduling class for this stage.
     */
    public function getScheduler()
    {
        if(!$this->type)
            return null;

        $schedulingClass = 'App\TournamentManager\MatchCreators\\'. $this->type. 'Creator';

        return new $schedulingClass();
    }
}
