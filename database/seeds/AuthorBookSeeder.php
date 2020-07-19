<?php

use Illuminate\Database\Seeder;
use App\Author;

class AuthorBookSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $publications = array(
            [
                'author_id'=>'1',
                'book_id'=>'2',
                'name'=> 'simple',
            ],
            [
                'author_id'=>'2',
                'book_id'=>'1',
                'name'=> 'multiple',
            ],
            [
                'author_id'=>'3',
                'book_id'=>'1',
                'name'=> 'multiple',
            ],

        );
        foreach($publications as $publication){
            $author = Author::find($publication['author_id']);
            $author->books()->attach($publication['book_id'],['name'=>$publication['name']]);
        }
    }
}
