<?php

namespace App\Http\Controllers;

use App\Book;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Validator;

class BookController extends Controller
{
    public $successStatus = 200;

    //obtener listado de libros (uso de filtro de busqueda opcional)
    public function index(Request $request){

        if ($request->search) {
            $search = $request->search;
        } else {
            $search = "";
        }

        $books = Book::with('authors')
            ->where(function ($query) use ($search) {
                $query->where('title', 'LIKE', '%' . $search . '%');
            })
            ->orWhere(function ($query) use ($search) {
                $query->where('isbn', 'LIKE', '%' . $search . '%');
            })
            ->get();

        return response()->json(
            [
                'objBooks' => $books,
                'status' => 'success',
                'message' => 'Listado de libros'
            ],
            $this->successStatus
        );
    }

    //obtener el libro especifico por id
    public function show($id)
    {
        $book = Book::with('authors')
            ->find($id);
        return response()->json(
            [
                'objBook' => $book,
                'status' => 'success',
                'message' => "libro encontrado correctamente."
            ],
            $this->successStatus
        );
    }

    //crear un nuevo libro
    public function store(Request $request)
    {
        try{
            DB::beginTransaction();
            $validator = Validator::make($request->all(), [
                'title' => 'required|string|max:150',
                'isbn' => 'required|string|max:150',
                'borrowed_at' => 'date|before_or_equal:'.date(now()),
                'borrowed_to' => 'string|max:150',
            ]);
            if ($validator->fails()) {
                return response()->json(['message' => $validator->errors(),'status'=>'error'], 401);
            }
            $input = $request->all();
            $book = Book::create($input);
            DB::commit();
            $message = 'Libro creado correctamente!';
            return response()->json(['message' => $message, 'status'=>'success', 'objBook' => $book], $this->successStatus);
        }catch (\Exception $err){
            DB::rollBack();
            return response()->json(
                [
                    'status' => 'error',
                    'results' => $err->getMessage()
                ], 400);
        }
    }

    //actualización de los campos del libro
    public function update(Request $request, $id)
    {
        try {
            DB::beginTransaction();
            $book = Book::find($id);
            if (!$book) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'El libro que deseas actualizar no existe.'
                ], 404);
            }
            if ($validations = $this->validateRequest($request)) {
                return $validations;
            }
            $book_res = $this->updateRecord($request, $book);
            DB::commit();
            return response()->json([
                'message' => 'El libro se actualizó correctamente.',
                'status' => 'success',
                'objBook' => $book_res
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
            'title' => 'required|string|max:150',
            'isbn' => 'required|string|max:150',
            'borrowed_at' => 'date|before_or_equal:'.date(now()),
            'borrowed_to' => 'string|max:150',
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
    protected function updateRecord(Request $request, Book $book)
    {
        $book->update($request->all());
        $book->fresh();
        return $book;
    }

    //eliminación del libro
    public function destroy(Request $request, $id)
    {
        $book = Book::find($id);
        if (!$book) {
            return response()->json([
                'status' => 'error',
                'message' => 'El libro que deseas eliminar no existe.',
            ], 404);
        }
        $book->delete();
        return response()->json([
            'status' => 'success',
            'message' => 'El libro se eliminó correctamente.',
            'objBook' => $book
        ], $this->successStatus);
    }

    //prestar un libro
    public function borrow(Request $request, $id)
    {
        try {
            DB::beginTransaction();
            $book = Book::find($id);
            if (!$book) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'El libro que deseas prestar no existe.'
                ], 404);
            }
            $validator = Validator::make($request->all(), [
                'borrowed_at' => 'date|before_or_equal:'.date(now()),
                'borrowed_to' => 'required|string|max:150',
            ]);
            if ($validator->fails()) {
                return response()->json(['message' => $validator->errors(),'status'=>'error'], 401);
            }
            $book->fill($request->only(['borrowed_at', 'borrowed_to']));
            $book->save();
            DB::commit();
            return response()->json([
                'message' => 'Se realizó el préstamo correctamente.',
                'status' => 'success',
                'objBook' => $book
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
}
