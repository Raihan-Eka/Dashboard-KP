<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PageController extends Controller
{
    /**
     * Menampilkan halaman home.
     */
    public function showHome()
    {
        // Mengembalikan view 'home.blade.php'
        return view('home');
    }

    /**
     * Menampilkan halaman datin (dashboard).
     */
    public function showDatin()
    {
        // Mengembalikan view 'datin.blade.php'
        return view('datin');
    }
}