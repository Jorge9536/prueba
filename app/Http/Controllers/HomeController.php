<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        if (auth()->check()) {
            return redirect()->route('agenda.calendar');
        } else {
            return redirect()->route('login');
        }
    }
}