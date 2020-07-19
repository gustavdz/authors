<?php

use Illuminate\Database\Seeder;
use App\Author;

class AuthorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $authors = array(
            [
                'name'=>'Daniel D',
                'birthday'=>'19910305',
                'perish_date'=> '20180916',
            ],
            [
                'name'=>'Nayibe S',
                'birthday'=>'19950522',
            ],
            [
                'name'=>'Angel Z',
                'birthday'=>'19750409',
                'perish_date'=> '20070512',
            ],
        );
        foreach($authors as $author){
            Author::create($author);
        }
    }
}
