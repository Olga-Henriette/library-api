<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Book;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class BookController extends Controller
{
    // GET /api/v1/books (avec filtre optionnel ?available=true)
    public function index(Request $request)
    {
        $query = Book::with('author');

        // Application du filtre si présent dans l'URL
        if ($request->has('available')) {
            $isAvailable = $request->query('available') === 'true';
            $query->where('available', $isAvailable);
        }

        $books = $query->get();

        return response()->json([
            'success' => true,
            'data' => $books,
            'message' => 'Liste des livres récupérée'
        ], 200);
    }

    // POST /api/v1/books
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string',
            'isbn' => 'required|string|unique:books',
            'year' => 'required|integer',
            'author_id' => 'required|exists:authors,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation échouée',
                'errors' => $validator->errors()
            ], 422);
        }

        $book = Book::create($request->all());
        
        return response()->json([
            'success' => true,
            'data' => $book->load('author'),
            'message' => 'Livre ajouté avec succès'
        ], 201);
    }

    // GET /api/v1/books/{id}
    public function show($id)
    {
        $book = Book::with('author')->find($id);

        if (!$book) {
            return response()->json(['success' => false, 'message' => 'Livre non trouvé'], 404);
        }

        return response()->json(['success' => true, 'data' => $book], 200);
    }

    // PUT /api/v1/books/{id}
    public function update(Request $request, $id)
    {
        $book = Book::find($id);
        if (!$book) return response()->json(['success' => false, 'message' => 'Livre non trouvé'], 404);

        $book->update($request->all());
        return response()->json(['success' => true, 'data' => $book->load('author')], 200);
    }

    // DELETE /api/v1/books/{id}
    public function destroy($id)
    {
        $book = Book::find($id);
        if (!$book) return response()->json(['success' => false, 'message' => 'Livre non trouvé'], 404);

        $book->delete();
        return response()->json(['success' => true, 'message' => 'Livre supprimé'], 204);
    }
}