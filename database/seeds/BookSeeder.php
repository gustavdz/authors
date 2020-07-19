<?php

use Illuminate\Database\Seeder;
use App\Book;

class BookSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $books = array(
            [
                'title'=>'Prueba de Laravel Parte 1',
                'isbn'=>'123123',
                'borrowed_at'=> date(now()),
                'borrowed_to'=>'Xavier',
            ],
            [
                'title'=>'Prueba de Laravel Parte 2',
                'isbn'=>'456456',
                'borrowed_at'=> date(now()),
                'borrowed_to'=>'Natalia',
            ],
        );
        foreach($books as $book){
            Book::create($book);
        }
    }
}
