<?php

namespace App\Support;

/**
 * Static sample data for the Artivo prototype (Laboratory Activity 2).
 * No database is used: everything the views display comes from this class.
 */
class SampleData
{
    public const CATEGORIES = ['Illustration', 'Photography', 'Pixel Art', 'Vectors'];

    public const GENRES = ['Cartoon', 'Anime', 'Chibi', 'Minimalist'];

    /** File type and resolution shown on the detail page, per category. */
    private const FORMATS = [
        'Illustration' => ['PNG, PSD', '4000 x 4000 px'],
        'Photography' => ['JPG, RAW', '6000 x 4000 px'],
        'Pixel Art' => ['PNG, ASE', '1024 x 1024 px'],
        'Vectors' => ['SVG, AI', 'Scalable vector'],
    ];

    /** @return array<int, array<string, mixed>> keyed by artwork id */
    public static function artworks(): array
    {
        $rows = [
            [1, 'Neon Koi Pond', 'Mika Tanaka', 18, 'Illustration', 'Anime', '48 MB', 'Glowing koi drift through a neon-lit night pond. Includes the flattened PNG and a layered PSD.'],
            [2, 'Mountain Mist at Dawn', 'Lena Ortiz', 15, 'Photography', 'Minimalist', '62 MB', 'A quiet ridge line wrapped in morning fog, shot in soft natural light. Delivered as JPG and RAW.'],
            [3, 'Pixel Forest Camp', 'Dev Pillai', 8, 'Pixel Art', 'Cartoon', '3 MB', 'A cozy campsite scene drawn in a 32-colour palette. Includes the PNG and the Aseprite source.'],
            [4, 'Sakura Chibi Set', 'Hana Park', 12, 'Illustration', 'Chibi', '36 MB', 'Six chibi characters among falling cherry blossoms. Each character comes as its own layer.'],
            [5, 'Abstract Wave Pack', 'Noor Haddad', 10, 'Vectors', 'Minimalist', '5 MB', 'Ten flowing wave shapes in clean vector form, ready for posters, covers and web banners.'],
            [6, 'City Rooftop Cat', 'Mika Tanaka', 24, 'Illustration', 'Cartoon', '52 MB', 'A sleepy cat watching the skyline at dusk. Includes the PNG and the layered PSD with a separate sky.'],
            [7, 'Retro Arcade Tiles', 'Dev Pillai', 9, 'Pixel Art', 'Cartoon', '2 MB', 'A tileset of arcade cabinets, floors and neon signs for 2D game prototypes.'],
            [8, 'Golden Hour Streets', 'Lena Ortiz', 14, 'Photography', 'Minimalist', '58 MB', 'Empty city streets lit by warm evening light. Delivered as JPG and RAW.'],
            [9, 'Moonlit Samurai', 'Hana Park', 20, 'Illustration', 'Anime', '44 MB', 'A lone swordsman under a full moon, painted in a cinematic anime style with a layered PSD.'],
            [10, 'Geometric Fox Icons', 'Noor Haddad', 7, 'Vectors', 'Minimalist', '2 MB', 'A set of twelve fox icons built from simple geometric shapes, in SVG and AI formats.'],
            [11, 'Tiny Bakery Chibis', 'Hana Park', 11, 'Illustration', 'Chibi', '31 MB', 'Chibi bakers, breads and pastries in a bright, cheerful palette.'],
            [12, 'Cyber Alley Sprites', 'Dev Pillai', 13, 'Pixel Art', 'Anime', '4 MB', 'Animated sprite sheets for a rainy cyberpunk alley, with idle and walk cycles.'],
        ];

        $artworks = [];
        foreach ($rows as [$id, $title, $artist, $price, $category, $genre, $size, $description]) {
            [$fileType, $resolution] = self::FORMATS[$category];
            $artworks[$id] = [
                'id' => $id,
                'title' => $title,
                'artist' => $artist,
                'price' => $price,
                'category' => $category,
                'genre' => $genre,
                'file_type' => $fileType,
                'resolution' => $resolution,
                'file_size' => $size,
                'description' => $description,
                'rating' => 4.3 + ($id % 5) / 10,
                'reviews' => 12 + $id * 3,
            ];
        }

        return $artworks;
    }

    public static function find(int $id): ?array
    {
        return self::artworks()[$id] ?? null;
    }

    /** @return array<int, array{name: string, count: int}> */
    public static function artists(): array
    {
        return [
            ['name' => 'Mika Tanaka', 'count' => 48],
            ['name' => 'Lena Ortiz', 'count' => 36],
            ['name' => 'Dev Pillai', 'count' => 62],
            ['name' => 'Hana Park', 'count' => 41],
            ['name' => 'Noor Haddad', 'count' => 29],
        ];
    }

    public static function home(): array
    {
        $all = array_values(self::artworks());

        return [
            'categories' => self::CATEGORIES,
            'featured' => array_slice($all, 0, 5),
            'newArrivals' => array_slice($all, 7, 5),
            'artists' => self::artists(),
        ];
    }

    public static function browse(int $page = 1): array
    {
        $page = max(1, min(10, $page));

        $pages = [
            ['label' => '<', 'page' => max(1, $page - 1), 'active' => false, 'disabled' => $page === 1, 'aria' => 'Previous page'],
            ['label' => '1', 'page' => 1, 'active' => $page === 1, 'disabled' => false, 'aria' => 'Page 1'],
            ['label' => '2', 'page' => 2, 'active' => $page === 2, 'disabled' => false, 'aria' => 'Page 2'],
            ['label' => '3', 'page' => 3, 'active' => $page === 3, 'disabled' => false, 'aria' => 'Page 3'],
            ['label' => '...', 'page' => null, 'active' => false, 'disabled' => true, 'aria' => 'More pages'],
            ['label' => '10', 'page' => 10, 'active' => $page === 10, 'disabled' => false, 'aria' => 'Page 10'],
            ['label' => '>', 'page' => min(10, $page + 1), 'active' => false, 'disabled' => $page === 10, 'aria' => 'Next page'],
        ];

        return [
            'categories' => self::CATEGORIES,
            'genres' => self::GENRES,
            'artworks' => array_values(self::artworks()),
            'total' => 128,
            'pages' => $pages,
        ];
    }

    public static function artwork(int $id): ?array
    {
        $art = self::find($id);
        if ($art === null) {
            return null;
        }

        $more = array_values(array_filter(
            self::artworks(),
            fn ($item) => $item['id'] !== $id
        ));
        // Same artist first, then the rest, so the row always has five cards.
        usort($more, fn ($a, $b) => ($b['artist'] === $art['artist']) <=> ($a['artist'] === $art['artist']));

        return [
            'art' => $art,
            'more' => array_slice($more, 0, 5),
            'licenses' => ['Personal use', 'Commercial use'],
        ];
    }

    public static function cart(): array
    {
        $items = [];
        foreach ([[6, 'Personal license'], [4, 'Personal license']] as [$id, $license]) {
            $items[] = self::find($id) + ['license' => $license];
        }

        $subtotal = array_sum(array_column($items, 'price'));
        $fee = 1;

        return [
            'items' => $items,
            'subtotal' => $subtotal,
            'fee' => $fee,
            'total' => $subtotal + $fee,
        ];
    }

    /** Pages that are linked from the wireframes but are not part of this activity. */
    public static function page(string $slug): ?array
    {
        $pages = [
            'artists' => 'Artists',
            'sell' => 'Sell your art',
            'about' => 'About Artivo',
            'help' => 'Help Center',
            'terms' => 'Terms of Use',
            'privacy' => 'Privacy Policy',
        ];

        return isset($pages[$slug]) ? ['title' => $pages[$slug]] : null;
    }
}
