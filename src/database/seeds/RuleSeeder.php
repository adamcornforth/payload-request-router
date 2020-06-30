<?php

use Illuminate\Database\Seeder;
use \Illuminate\Support\Facades\DB;

class RuleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('rules')->insert([
            'microservice' => 'http://service_a',
            'rule' => 'payload is not null',
        ]);

        DB::table('rules')->insert([
            'microservice' => 'http://service_a',
            'rule' => 'payload->\'campaign\'->>\'name\' != \'Campaign B\'',
        ]);

        DB::table('rules')->insert([
            'microservice' => 'http://service_c',
            'rule' => 'payload is not null',
        ]);

        DB::table('rules')->insert([
            'microservice' => 'http://service_b',
            'rule' => 'payload->\'query_type\'->>\'title\' == \'SALE MADE\'',
        ]);
    }
}
