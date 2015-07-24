<?php

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

        $this->call(CreateTournament1Seeder::class);

        $this->call(StartTournament1Seeder::class);

        $this->call(CreateResultsForTournament1Seeder::class);

        $this->call(CreateTournament1Seeder::class);

        // factory(App\Models\Tournament::class, 1)->create();


        Model::reguard();
    }
}
