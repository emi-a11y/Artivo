<?php

namespace Database\Seeders;

use App\Models\Artwork;
use App\Models\Category;
use App\Models\Genre;
use App\Models\Order;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

/**
 * A few starting records so the site is not empty. Safe to run more than once.
 * Every account below uses the password: password
 */
class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $account = fn (string $name, string $email, string $role) => User::firstOrCreate(
            ['email' => $email],
            ['name' => $name, 'password' => Hash::make('password'), 'role' => $role, 'status' => 'Active']
        );

        $account('Artivo Admin', 'admin@artivo.test', 'Admin');
        $artists = [
            'mika' => $account('Mika Tanaka', 'mika@artivo.test', 'Artist'),
            'hana' => $account('Hana Park', 'hana@artivo.test', 'Artist'),
            'dev' => $account('Dev Pillai', 'dev@artivo.test', 'Artist'),
        ];
        $buyers = [
            $account('Maria Santos', 'maria@artivo.test', 'Buyer'),
            $account('John Reyes', 'john@artivo.test', 'Buyer'),
            $account('Aira Cruz', 'aira@artivo.test', 'Buyer'),
        ];

        $categories = [];
        foreach (['Illustration', 'Photography', 'Pixel Art', 'Vectors'] as $name) {
            $categories[$name] = Category::firstOrCreate(['name' => $name]);
        }
        $genres = [];
        foreach (['Cartoon', 'Anime', 'Chibi', 'Minimalist'] as $name) {
            $genres[$name] = Genre::firstOrCreate(['name' => $name]);
        }

        if (Artwork::exists()) {
            return; // already seeded
        }

        $formats = [
            'Illustration' => ['PNG, PSD', '4000 x 4000 px'],
            'Photography' => ['JPG, RAW', '6000 x 4000 px'],
            'Pixel Art' => ['PNG, ASE', '1024 x 1024 px'],
            'Vectors' => ['SVG, AI', 'Scalable vector'],
        ];

        // title, artist, price, category, genre, size, rating, reviews, description
        $rows = [
            ['Neon Koi Pond', 'mika', 18, 'Illustration', 'Anime', '48 MB', 4.8, 42, 'Glowing koi drift through a neon-lit night pond. Includes the flattened PNG and a layered PSD.'],
            ['City Rooftop Cat', 'mika', 24, 'Illustration', 'Cartoon', '52 MB', 4.6, 31, 'A sleepy cat watching the skyline at dusk. Includes the PNG and the layered PSD with a separate sky.'],
            ['Sakura Chibi Set', 'hana', 12, 'Illustration', 'Chibi', '36 MB', 4.7, 27, 'Six chibi characters among falling cherry blossoms. Each character comes as its own layer.'],
            ['Moonlit Samurai', 'hana', 20, 'Illustration', 'Anime', '44 MB', 4.9, 55, 'A lone swordsman under a full moon, painted in a cinematic anime style with a layered PSD.'],
            ['Pixel Forest Camp', 'dev', 8, 'Pixel Art', 'Cartoon', '3 MB', 4.5, 19, 'A cozy campsite scene drawn in a 32-colour palette. Includes the PNG and the Aseprite source.'],
            ['Retro Arcade Tiles', 'dev', 9, 'Pixel Art', 'Cartoon', '2 MB', 4.4, 14, 'A tileset of arcade cabinets, floors and neon signs for 2D game prototypes.'],
            ['Abstract Wave Pack', 'dev', 10, 'Vectors', 'Minimalist', '5 MB', 4.3, 12, 'Ten flowing wave shapes in clean vector form, ready for posters, covers and web banners.'],
            ['Mountain Mist at Dawn', 'mika', 15, 'Photography', 'Minimalist', '62 MB', 4.6, 23, 'A quiet ridge line wrapped in morning fog, shot in soft natural light. Delivered as JPG and RAW.'],
        ];

        $art = [];
        foreach ($rows as $i => [$title, $artist, $price, $category, $genre, $size, $rating, $reviews, $description]) {
            [$fileType, $resolution] = $formats[$category];
            $art[$i + 1] = Artwork::create([
                'title' => $title,
                'artist_id' => $artists[$artist]->id,
                'category_id' => $categories[$category]->id,
                'genre_id' => $genres[$genre]->id,
                'price' => $price,
                'file_type' => $fileType,
                'resolution' => $resolution,
                'file_size' => $size,
                'description' => $description,
                'status' => 'Published',
                'rating' => $rating,
                'reviews' => $reviews,
            ]);
        }

        // Four sample orders spread over the last months (feeds the dashboard chart).
        $licence = 'Personal license';
        Order::place($buyers[0], [['artwork' => $art[4], 'license' => $licence]], 'Paid', now()->subMonths(4)->day(12));
        Order::place($buyers[1], [['artwork' => $art[2], 'license' => $licence], ['artwork' => $art[3], 'license' => $licence]], 'Paid', now()->subMonths(2)->day(8));
        Order::place($buyers[2], [['artwork' => $art[1], 'license' => 'Commercial license']], 'Refunded', now()->subMonth()->day(20));
        Order::place($buyers[0], [['artwork' => $art[4], 'license' => $licence], ['artwork' => $art[5], 'license' => $licence]], 'Pending', now()->subDays(2));
    }
}
