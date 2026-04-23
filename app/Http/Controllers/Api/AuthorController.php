<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Author;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AuthorController extends Controller
{
    // GET /api/v1/authors
    public function index()
    {
        $authors = Author::all();
        return response()->json([
            'success' => true,
            'data' => $authors,
            'message' => 'Liste des auteurs récupérée'
        ], 200);
    }

    // POST /api/v1/authors 
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string',
            'last_name' => 'required|string',
            'nationality' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $author = Author::create($request->all());
        return response()->json(['success' => true, 'data' => $author, 'message' => 'Auteur créé'], 201);
    }

    // GET /api/v1/authors/{id} (Détails + ses livres)
    public function show($id)
    {
        $author = Author::with('books')->find($id);

        if (!$author) {
            return response()->json(['success' => false, 'message' => 'Auteur non trouvé'], 404);
        }

        return response()->json(['success' => true, 'data' => $author], 200);
    }

    // PUT /api/v1/authors/{id}
    public function update(Request $request, $id)
    {
        $author = Author::find($id);
        if (!$author) return response()->json(['success' => false, 'message' => 'Introuvable'], 404);

        $author->update($request->all());
        return response()->json(['success' => true, 'data' => $author, 'message' => 'Auteur mis à jour'], 200);
    }

    // DELETE /api/v1/authors/{id}
    public function destroy($id)
    {
        $author = Author::find($id);
        if (!$author) return response()->json(['success' => false, 'message' => 'Introuvable'], 404);

        $author->delete();
        return response()->json(['success' => true, 'message' => 'Auteur supprimé'], 204);
    }
}