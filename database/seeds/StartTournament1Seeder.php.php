<?php

use Illuminate\Database\Seeder;

class StartTournament1Seeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('stages')->where('id', '=', 1)->update(['status' => 'InProgress']);
    	
        //Insert into pools table
        DB::table('pools')->insert([
        	['name' => 'Group A', 'stage_id' => 1],
        	['name' => 'Group B', 'stage_id' => 1],
        ]);

        //Insert into competitor_pool
        DB::table('pool_members')->insert([
        	['competitor_id' => 1, 'pool_id' => 1,],
        	['competitor_id' => 2, 'pool_id' => 1,],
        	['competitor_id' => 3, 'pool_id' => 1,],
        	['competitor_id' => 4, 'pool_id' => 1,],
        	['competitor_id' => 5, 'pool_id' => 2,],
        	['competitor_id' => 6, 'pool_id' => 2,],
            ['competitor_id' => 7, 'pool_id' => 2,],
            ['competitor_id' => 8, 'pool_id' => 2,],
            ['competitor_id' => 9, 'pool_id' => 2,],
        ]);
    }
}
