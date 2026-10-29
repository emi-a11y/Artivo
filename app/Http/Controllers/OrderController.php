<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class OrderController extends Controller
{
    /** My Orders page: the logged-in buyer's own orders, newest first. */
    public function index(Request $request)
    {
        $orders = $request->user()->orders()
            ->with('items')
            ->latest()->latest('id')
            ->paginate(5)->withQueryString();

        return view('orders', ['orders' => $orders]);
    }
}
