<?php

use Illuminate\Database\Seeder;

class Start2Tournament1Seeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('stages')->where('id', '=', 1)->update(['status' => 'Finished']);
    	DB::table('stages')->where('id', '=', 2)->update(['status' => 'InProgress']);

        //Insert into pools table
        DB::table('pools')->insert(
        	['name' => 'Brackets', 'stage_id' => 2]
        );

        //Insert into competitor_pool
        DB::table('pool_members')->insert([
        	['competitor_id' => 1, 	   'pool_id' => 3,],
        	['competitor_id' => 2,     'pool_id' => 3,],
        	['competitor_id' => 3, 	   'pool_id' => 3,],
        	['competitor_id' => 4,     'pool_id' => 3,],
            ['competitor_id' => 5,     'pool_id' => 3,],
            ['competitor_id' => 6,     'pool_id' => 3,],
            ['competitor_id' => null,  'pool_id' => 3,],
            ['competitor_id' => 7,     'pool_id' => 3,],
        ]);
    }
}
