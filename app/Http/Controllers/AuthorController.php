<?php

namespace App\Http\Controllers;

use App\Author;
use App\Book;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Validator;

class AuthorController extends Controller
{
    public $successStatus = 200;

    //obtener listado de autores (uso de filtro de busqueda opcional)
    public function index(Request $request){

        if ($request->search) {
            $search = $request->search;
        } else {
            $search = "";
        }

        $authors = Author::with('books')
            ->where(function ($query) use ($search) {
                $query->where('name', 'LIKE', '%' . $search . '%');
            })->get();

        return response()->json(
            [
                'objAuthors' => $authors,
                'status' => 'success',
                'message' => 'Listado de autores'
            ],
            $this->successStatus
        );
    }

    //obtener el autor especifico por id
    public function show($id)
    {
        $author = Author::with('books')
            ->find($id);
        return response()->json(
            [
                'objAuthor' => $author,
                'status' => 'success',
                'message' => "Autor encontrado correctamente."
            ],
            $this->successStatus
        );
    }

    //crear un nuevo autor
    public function store(Request $request)
    {
        try{
            DB::beginTransaction();
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:150',
                'birthday' => 'date|before_or_equal:'.date(now()),
                'perish_date' => 'date|before_or_equal:'.date(now()),
            ]);
            if ($validator->fails()) {
                return response()->json(['message' => $validator->errors(),'status'=>'error'], 401);
            }
            $input = $request->all();
            $author = Author::create($input);
            DB::commit();
            $message = 'Autor creado correctamente!';
            return response()->json(['message' => $message, 'status'=>'success', 'objAuthor' => $author], $this->successStatus);
        }catch (\Exception $err){
            DB::rollBack();
            return response()->json(
                [
                    'status' => 'error',
                    'results' => $err->getMessage()
                ], 400);
        }
    }

    //actualización de los campos del autor
    public function update(Request $request, $id)
    {
        try {
            DB::beginTransaction();
            $author = Author::find($id);
            if (!$author) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'El autor que deseas actualizar no existe.'
                ], 404);
            }
            if ($validations = $this->validateRequest($request)) {
                return $validations;
            }
            $author_res = $this->updateRecord($request, $author);
            DB::commit();
            return response()->json([
                'message' => 'El autor se actualizó correctamente',
                'status' => 'success',
                'objAuthor' => $author_res
            ], $this->successStatus);
        } catch(\Exception $err) {
            DB::rollBack();
            return response()->json(
                [
                    'status' => 'error',
                    'results' => $err->getMessage()
                ], 400);
        }
    }
    //validación de campos recibidos
    protected function validateRequest(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:150',
            'birthday' => 'date|before_or_equal:'.date(now()),
            'perish_date' => 'date|before_or_equal:'.date(now()),
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Algunos campos no son válidos.',
                'errors' => $validator->errors(),
            ], 401);
        }
    }
    //funcion para actualizar valores en la base de datos
    protected function updateRecord(Request $request, Author $author)
    {
        $author->update($request->all());
        $author->fresh();
        return $author;
    }

    //eliminación del autor
    public function destroy(Request $request, $id)
    {
        $author = Author::find($id);
        if (!$author) {
            return response()->json([
                'status' => 'error',
                'message' => 'El autor que deseas eliminar no existe.',
            ], 404);
        }
        $author->delete();
        return response()->json([
            'status' => 'success',
            'message' => 'El autor se eliminó correctamente.',
            'objAuthor' => $author
        ], $this->successStatus);
    }

    //publicar un libro
    public function publish(Request $request)
    {
        DB::beginTransaction();
        try {
            $author = Author::find($request->author_id);
            if (!$author) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'El autor que desea publicar un libro no existe.'
                ], 404);
            }
            $book = Book::find($request->book_id);
            if (!$book) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'El libro que desea publicar no existe.'
                ], 404);
            }
            $author->books()->attach($request->book_id,['name'=>$request->name]);
            $author = Author::with('books')->find($request->author_id);
            DB::commit();
            return response()->json([
                'status' => 'success',
                'message' => 'El autor publicó un libro correctamente.',
                'objAuthor' => $author
            ], $this->successStatus);
        } catch (\Exception $e) {
            //if there is an error/exception in the above code before commit, it'll rollback
            DB::rollBack();
            return response()->json(
                [
                    'status' => 'error',
                    'message' => $e->getMessage()
                ], 400);
        }
    }
}
