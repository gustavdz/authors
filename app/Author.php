<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Author extends Model
{
    protected $primaryKey = 'author_id';

    protected $fillable = [
        'name', 'birthday', 'perish_date'
    ];

    protected $hidden = [];

    protected $casts = [
        'perish_date' => 'date',
    ];

    /**
     * Los libros que le pertenecen al autor
     */
    public function books()
    {
        return $this->belongsToMany(Book::class, 'authors_books', 'author_id', 'book_id')
            ->as('published')
            ->withPivot('name')
            ->withTimestamps();
    }
}
