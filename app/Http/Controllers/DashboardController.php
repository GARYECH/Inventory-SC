<?php

namespace App\Http\Controllers;

use App\Models\Item;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        // Fetch all items from your PostgreSQL database
        $items = Item::all();

        // Pass the $items variable to the dashboard view
        return view('dashboard', compact('items'));
    }
}