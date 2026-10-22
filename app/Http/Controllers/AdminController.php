<?php

namespace App\Http\Controllers;

use App\Support\AdminData;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function dashboard()
    {
        return view('admin.dashboard', AdminData::dashboard());
    }

    public function artworks(Request $request)
    {
        return view('admin.artworks', AdminData::artworks((int) $request->query('page', 1)));
    }

    public function orders(Request $request)
    {
        return view('admin.orders', AdminData::orders($request->query('status'), (int) $request->query('page', 1)));
    }

    public function users(Request $request)
    {
        return view('admin.users', AdminData::users($request->query('role'), (int) $request->query('page', 1)));
    }

    public function page(string $slug)
    {
        $data = AdminData::page($slug);
        abort_if($data === null, 404);

        return view('admin.page', $data);
    }
}
