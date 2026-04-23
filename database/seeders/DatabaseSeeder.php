<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Créer l'utilisateur admin
        $user = \App\Models\User::updateOrCreate(
            ['email' => 'admin@test.com'],
            [
                'name' => 'Admin',
                'password' => bcrypt('password'),
            ]
        );

        // 2. Créer 3 Auteurs
        $a1 = \App\Models\Author::create(['first_name' => 'Albert', 'last_name' => 'Camus', 'nationality' => 'Française']);
        $a2 = \App\Models\Author::create(['first_name' => 'Victor', 'last_name' => 'Hugo', 'nationality' => 'Française']);
        $a3 = \App\Models\Author::create(['first_name' => 'Fyodor', 'last_name' => 'Dostoevsky', 'nationality' => 'Russe']);

        // 3. Créer 5 Livres
        $b1 = \App\Models\Book::create(['title' => "L'Étranger", 'isbn' => '97801', 'year' => 1942, 'author_id' => $a1->id, 'available' => false]);
        $b2 = \App\Models\Book::create(['title' => "La Peste", 'isbn' => '97802', 'year' => 1947, 'author_id' => $a1->id, 'available' => true]);
        $b3 = \App\Models\Book::create(['title' => "Les Misérables", 'isbn' => '97803', 'year' => 1862, 'author_id' => $a2->id, 'available' => false]);
        $b4 = \App\Models\Book::create(['title' => "Notre-Dame de Paris", 'isbn' => '97804', 'year' => 1831, 'author_id' => $a2->id, 'available' => true]);
        $b5 = \App\Models\Book::create(['title' => "Crime et Châtiment", 'isbn' => '97805', 'year' => 1866, 'author_id' => $a3->id, 'available' => true]);

        // 4. Créer 2 Emprunts
        \App\Models\Borrow::create(['user_id' => $user->id, 'book_id' => $b1->id, 'borrowed_at' => now()]);
        \App\Models\Borrow::create(['user_id' => $user->id, 'book_id' => $b3->id, 'borrowed_at' => now()]);
    }
}
