<?php

namespace App\Support;

/**
 * Static sample data for the Artivo admin prototype (Laboratory Activity 2).
 * No database is used. Artwork names, artists, categories and genres come from
 * SampleData so the admin side always matches the storefront.
 */
class AdminData
{
    public const ORDER_STATUSES = ['Pending', 'Paid', 'Refunded'];

    public const ARTWORK_STATUSES = ['Published', 'Pending', 'Draft'];

    /** @return array<int, array<string, mixed>> */
    private static function allOrders(): array
    {
        return [
            ['id' => 'ORD-1006', 'customer' => 'Maria Santos', 'items' => 2, 'total' => 37.00, 'date' => 'Oct 14, 2026', 'status' => 'Paid'],
            ['id' => 'ORD-1005', 'customer' => 'John Reyes', 'items' => 1, 'total' => 24.00, 'date' => 'Oct 14, 2026', 'status' => 'Pending'],
            ['id' => 'ORD-1004', 'customer' => 'Aira Cruz', 'items' => 3, 'total' => 45.00, 'date' => 'Oct 13, 2026', 'status' => 'Paid'],
            ['id' => 'ORD-1003', 'customer' => 'Paolo Dizon', 'items' => 1, 'total' => 12.00, 'date' => 'Oct 12, 2026', 'status' => 'Refunded'],
            ['id' => 'ORD-1002', 'customer' => 'Bea Lim', 'items' => 2, 'total' => 30.00, 'date' => 'Oct 11, 2026', 'status' => 'Paid'],
            ['id' => 'ORD-1001', 'customer' => 'Carlo Mendoza', 'items' => 1, 'total' => 8.00, 'date' => 'Oct 10, 2026', 'status' => 'Paid'],
        ];
    }

    /** @return array<int, array<string, mixed>> */
    private static function allUsers(): array
    {
        return [
            ['name' => 'Mika Tanaka', 'email' => 'mika.tanaka@example.com', 'role' => 'Artist', 'joined' => 'Sep 2026', 'status' => 'Active'],
            ['name' => 'Maria Santos', 'email' => 'maria.santos@example.com', 'role' => 'Buyer', 'joined' => 'Sep 2026', 'status' => 'Active'],
            ['name' => 'Lena Ortiz', 'email' => 'lena.ortiz@example.com', 'role' => 'Artist', 'joined' => 'Aug 2026', 'status' => 'Pending'],
            ['name' => 'John Reyes', 'email' => 'john.reyes@example.com', 'role' => 'Buyer', 'joined' => 'Aug 2026', 'status' => 'Active'],
            ['name' => 'Dev Pillai', 'email' => 'dev.pillai@example.com', 'role' => 'Artist', 'joined' => 'Aug 2026', 'status' => 'Active'],
            ['name' => 'Paolo Dizon', 'email' => 'paolo.dizon@example.com', 'role' => 'Buyer', 'joined' => 'Jul 2026', 'status' => 'Blocked'],
            ['name' => 'Hana Park', 'email' => 'hana.park@example.com', 'role' => 'Artist', 'joined' => 'Jul 2026', 'status' => 'Active'],
            ['name' => 'Aira Cruz', 'email' => 'aira.cruz@example.com', 'role' => 'Buyer', 'joined' => 'Jul 2026', 'status' => 'Active'],
        ];
    }

    /** Page buttons: previous, 1, 2, 3, next (as in the wireframe). */
    public static function pages(int $page, int $last = 3): array
    {
        $page = max(1, min($last, $page));
        $pages = [['label' => '<', 'page' => max(1, $page - 1), 'active' => false, 'disabled' => $page === 1, 'aria' => 'Previous page']];
        for ($i = 1; $i <= $last; $i++) {
            $pages[] = ['label' => (string) $i, 'page' => $i, 'active' => $page === $i, 'disabled' => false, 'aria' => 'Page ' . $i];
        }
        $pages[] = ['label' => '>', 'page' => min($last, $page + 1), 'active' => false, 'disabled' => $page === $last, 'aria' => 'Next page'];

        return $pages;
    }

    /** @param array<string, string|null> $options label => value */
    private static function tabs(array $options, ?string $current): array
    {
        $tabs = [];
        foreach ($options as $label => $value) {
            $tabs[] = ['label' => $label, 'value' => $value, 'active' => $value === $current];
        }

        return $tabs;
    }

    public static function dashboard(): array
    {
        $artworks = SampleData::artworks();
        $topSelling = [];
        foreach ([[9, 98], [6, 84], [1, 76], [4, 71]] as [$id, $sales]) {
            $art = $artworks[$id];
            $topSelling[] = [
                'title' => $art['title'],
                'artist' => $art['artist'],
                'sales' => $sales,
                'revenue' => $sales * $art['price'],
            ];
        }

        return [
            'stats' => [
                ['label' => 'Total Sales', 'value' => '$12,480', 'note' => '+8% this month'],
                ['label' => 'Orders', 'value' => '342', 'note' => '+8% this month'],
                ['label' => 'Artworks', 'value' => '1,208', 'note' => '+8% this month'],
                ['label' => 'Users', 'value' => '2,415', 'note' => '+8% this month'],
            ],
            'recentOrders' => array_slice(self::allOrders(), 0, 5),
            'topSelling' => $topSelling,
        ];
    }

    public static function artworks(int $page = 1): array
    {
        $statuses = ['Published', 'Published', 'Pending', 'Published', 'Draft', 'Pending'];
        $rows = [];
        foreach (array_slice(array_values(SampleData::artworks()), 0, 6) as $i => $art) {
            $rows[] = $art + ['status' => $statuses[$i]];
        }

        return [
            'artworks' => $rows,
            'categories' => SampleData::CATEGORIES,
            'genres' => SampleData::GENRES,
            'statuses' => self::ARTWORK_STATUSES,
            'artists' => array_column(SampleData::artists(), 'name'),
            'pages' => self::pages($page),
        ];
    }

    public static function orders(?string $status = null, int $page = 1): array
    {
        $status = in_array($status, self::ORDER_STATUSES, true) ? $status : null;
        $orders = array_values(array_filter(
            self::allOrders(),
            fn ($order) => $status === null || $order['status'] === $status
        ));

        return [
            'orders' => $orders,
            'tabs' => self::tabs(['All' => null, 'Pending' => 'Pending', 'Paid' => 'Paid', 'Refunded' => 'Refunded'], $status),
            'pages' => self::pages($page),
        ];
    }

    public static function users(?string $role = null, int $page = 1): array
    {
        $role = in_array($role, ['Buyer', 'Artist'], true) ? $role : null;
        $users = array_values(array_filter(
            self::allUsers(),
            fn ($user) => $role === null || $user['role'] === $role
        ));

        return [
            'users' => $users,
            'tabs' => self::tabs(['All' => null, 'Buyers' => 'Buyer', 'Artists' => 'Artist'], $role),
            'pages' => self::pages($page),
        ];
    }

    /** Sidebar items that have no wireframe screen yet. */
    public static function page(string $slug): ?array
    {
        $pages = [
            'categories' => 'Categories',
            'genres' => 'Genres',
            'reports' => 'Reports',
            'settings' => 'Settings',
        ];

        return isset($pages[$slug]) ? ['title' => $pages[$slug]] : null;
    }
}
