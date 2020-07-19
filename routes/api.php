<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

//Protected routes
Route::group(['middleware' => ['auth:api','verified']], function () {
    Route::post('/logout', 'Auth\ApiAuthController@logout')->name('logout.api');

    //authors CRUD
    Route::get('/authors', 'AuthorController@index')->name('author.index.api');
    Route::get('/author/{id}', 'AuthorController@show')->name('author.show.api');
    Route::post('/author', 'AuthorController@store')->name('author.create.api');
    Route::put('/author/{id}', 'AuthorController@update')->name('author.update.api');
    Route::delete('/author/{id}', 'AuthorController@destroy')->name('author.destroy.api');
    //other authors routes
    Route::post('/author/publish', 'AuthorController@publish')->name('author.publish.api');

    //books CRUD
    Route::get('/books', 'BookController@index')->name('book.index.api');
    Route::get('/book/{id}', 'BookController@show')->name('book.show.api');
    Route::post('/book', 'BookController@store')->name('book.create.api');
    Route::put('/book/{id}', 'BookController@update')->name('book.update.api');
    Route::delete('/book/{id}', 'BookController@destroy')->name('book.destroy.api');
    //other books routes
    Route::patch('/book/{id}/borrow', 'BookController@borrow')->name('book.borrow.api');

});

//Public routes
Route::group(['middleware' => []], function () {
    Route::post('/login', 'Auth\ApiAuthController@login')->name('login.api');
    Route::post('/register','Auth\ApiAuthController@register')->name('register.api');

});
