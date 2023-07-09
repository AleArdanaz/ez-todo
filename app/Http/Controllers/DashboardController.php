<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Models\Lists;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Display the dashboard.
     */
    public function index()
    {
        $lists = Lists::with('tasks')->where('user_id', auth()->user()->id)->orderBy('order', 'asc')->get();

        return Inertia::render('Dashboard/Dashboard', [
            'lists' => $lists,
            'status' => session('status'),
        ]);
    }
}
