<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SocialNetwork;
use Inertia\Inertia;

class SocialNetworkController extends Controller
{
    public function index()
{
    return Inertia::render('Admin/Redes', [
        'networks' => SocialNetwork::all()
    ]);
}

public function store(Request $request)
{
    $val = $request->validate([
        'name' => 'required|string',
        'url'  => 'required|url',
    ]);

    SocialNetwork::create($val);
    return redirect()->back()->with('message', 'Red social agregada.');
}

public function destroy(SocialNetwork $socialNetwork)
{
    $socialNetwork->delete();
    return redirect()->back()->with('message', 'Red eliminada.');
}
}
