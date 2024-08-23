<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\User;
class LecturerController extends Controller
{
    //
    public function index() {
        $lecturers = User::all();
        return view('Lecturers.lecturers', compact('lecturers'));
    }
}
