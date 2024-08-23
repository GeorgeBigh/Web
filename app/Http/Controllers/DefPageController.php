<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
class DefPageController extends Controller
{
    //
    public function index() {
        $user = User::all();
        return view('default_page/index', compact('user'));
    }
}
