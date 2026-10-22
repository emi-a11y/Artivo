<?php

namespace App\Http\Controllers;

use App\Support\SampleData;
use Illuminate\Http\Request;

class PageController extends Controller
{
    public function home()
    {
        return view('home', SampleData::home());
    }

    public function browse(Request $request)
    {
        return view('browse', SampleData::browse((int) $request->query('page', 1)));
    }

    public function artwork(int $id)
    {
        $data = SampleData::artwork($id);
        abort_if($data === null, 404);

        return view('artwork', $data);
    }

    public function cart()
    {
        return view('cart', SampleData::cart());
    }

    public function page(string $slug)
    {
        $data = SampleData::page($slug);
        abort_if($data === null, 404);

        return view('page', $data);
    }
}
