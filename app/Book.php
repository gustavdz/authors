<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Book extends Model
{
    protected $primaryKey = 'book_id';

    protected $fillable = [
        'title', 'isbn', 'borrowed_at', 'borrowed_to'
    ];

    protected $hidden = [];

    protected $casts = [
        'borrowed_at' => 'datetime',
    ];

    /**
     * Los autores que le hicieron el libro
     */
    public function authors()
    {
        return $this->belongsToMany(Author::class, 'authors_books', 'book_id', 'author_id')
            ->as('published')
            ->withPivot('name')
            ->withTimestamps();
    }
}
