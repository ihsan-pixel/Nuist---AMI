<?php

namespace App\Http\Controllers\Ami;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminPlaceholderController extends Controller
{
    public function index(Request $request): View
    {
        $title = (string) $request->route('title');

        return view('admin.placeholder', compact('title'));
    }
}
