<?php

namespace App\Http\Controllers;

use App\Models\Artwork;
use App\Models\Order;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AdminController extends Controller
{
    private const PER_PAGE = 8;

    public function dashboard()
    {
        $months = collect(range(5, 0))->map(fn ($i) => now()->startOfMonth()->subMonths($i));
        $paid = Order::where('status', 'Paid')->where('created_at', '>=', $months->first())->get(['total', 'created_at']);
        $chart = $months->map(fn ($m) => [
            'label' => $m->format('M'),
            'value' => (float) $paid->filter(fn ($o) => $o->created_at->format('Y-m') === $m->format('Y-m'))->sum('total'),
        ]);
        $max = max(1, $chart->max('value'));

        return view('admin.dashboard', [
            'stats' => [
                ['label' => 'Total Sales', 'value' => '$' . number_format(Order::where('status', 'Paid')->sum('total'), 2), 'note' => 'Paid orders only'],
                ['label' => 'Orders', 'value' => number_format(Order::count()), 'note' => Order::where('status', 'Pending')->count() . ' pending'],
                ['label' => 'Artworks', 'value' => number_format(Artwork::count()), 'note' => Artwork::where('status', 'Published')->count() . ' published'],
                ['label' => 'Users', 'value' => number_format(User::count()), 'note' => User::where('role', 'Artist')->count() . ' artists'],
            ],
            'chart' => $chart->map(fn ($c) => $c + ['height' => $c['value'] > 0 ? max(4, round($c['value'] / $max * 100)) : 0]),
            'topSelling' => Artwork::with('artist')->has('orderItems')->withCount('orderItems')
                ->orderByDesc('order_items_count')->orderBy('id')->take(4)->get(),
            'recentOrders' => Order::with('user')->withCount('items')->latest()->latest('id')->take(5)->get(),
        ]);
    }

    public function orders(Request $request)
    {
        $status = in_array($request->query('status'), Order::STATUSES, true) ? $request->query('status') : null;
        $orders = Order::with('user')->withCount('items')
            ->when($status, fn ($q) => $q->where('status', $status))
            ->latest()->latest('id')->paginate(self::PER_PAGE)->withQueryString();

        return view('admin.orders', [
            'orders' => $orders,
            'tabs' => $this->tabs(['All' => null, 'Pending' => 'Pending', 'Paid' => 'Paid', 'Refunded' => 'Refunded'], $status),
        ]);
    }

    public function updateOrder(Request $request, Order $order)
    {
        $data = $request->validate(['status' => ['required', Rule::in(Order::STATUSES)]]);
        $order->update($data);

        return back()->with('status', "{$order->code} is now {$order->status}.");
    }

    public function users(Request $request)
    {
        $role = in_array($request->query('role'), ['Buyer', 'Artist'], true) ? $request->query('role') : null;
        $users = User::when($role, fn ($q) => $q->where('role', $role))
            ->orderBy('id')->paginate(self::PER_PAGE)->withQueryString();

        return view('admin.users', [
            'users' => $users,
            'tabs' => $this->tabs(['All' => null, 'Buyers' => 'Buyer', 'Artists' => 'Artist'], $role),
        ]);
    }

    public function toggleUser(Request $request, User $user)
    {
        if ($user->isAdmin() || $user->id === $request->user()->id) {
            return back()->with('status', 'Admin accounts cannot be blocked.');
        }

        $user->update(['status' => $user->status === 'Blocked' ? 'Active' : 'Blocked']);

        return back()->with('status', "{$user->name} is now {$user->status}.");
    }

    public function page(string $slug)
    {
        $pages = ['reports' => 'Reports', 'settings' => 'Settings'];
        abort_unless(isset($pages[$slug]), 404);

        return view('admin.page', ['title' => $pages[$slug]]);
    }

    /** @param array<string, string|null> $options label => value */
    private function tabs(array $options, ?string $current): array
    {
        $tabs = [];
        foreach ($options as $label => $value) {
            $tabs[] = ['label' => $label, 'value' => $value, 'active' => $value === $current];
        }

        return $tabs;
    }
}
