<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Borrow;
use App\Models\Book;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class BorrowController extends Controller
{
    // GET /api/v1/borrows (Liste des emprunts en cours)
    public function index()
    {
        $borrows = Borrow::with('book')->whereNull('returned_at')->get();
        return response()->json([
            'success' => true,
            'data' => $borrows,
            'message' => 'Liste des emprunts en cours'
        ]);
    }

    // POST /api/v1/borrows (Enregistrer un emprunt)
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'book_id' => 'required|exists:books,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $book = Book::find($request->book_id);

        if (!$book->available) {
            return response()->json([
                'success' => false,
                'message' => 'Ce livre n\'est pas disponible pour le moment'
            ], 422); 
        }

        $borrow = Borrow::create([
            'user_id' => $request->user()->id,
            'book_id' => $request->book_id,
            'borrowed_at' => now(),
            'returned_at' => null,
        ]);

        $book->update(['available' => false]);

        return response()->json([
            'success' => true,
            'data' => $borrow->load('book'),
            'message' => 'Emprunt enregistré'
        ], 201);
    }

    // PATCH /api/v1/borrows/{id}/return 
    public function returnBook($id)
    {
        $borrow = Borrow::find($id);

        if (!$borrow || $borrow->returned_at) {
            return response()->json(['success' => false, 'message' => 'Emprunt introuvable ou déjà rendu'], 404);
        }

        // Marquer comme rendu
        $borrow->update(['returned_at' => now()]);

        // Remettre le livre en disponible
        $book = Book::find($borrow->book_id);
        $book->update(['available' => true]);

        return response()->json([
            'success' => true,
            'data' => $borrow,
            'message' => 'Livre rendu avec succès et remis en inventaire'
        ], 200);
    }
}