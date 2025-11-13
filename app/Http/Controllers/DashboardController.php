<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        if ($user->isAdmin()) {
            return view('dashboard');
        } elseif ($user->isDosen()) {
            return view('dashboard');
        } else {
            return view('dashboard');
        }
    }
}
