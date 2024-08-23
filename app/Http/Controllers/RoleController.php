<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Role;

class RoleController extends Controller
{
    //










    public function index() {

        return view('Admin.add_role');
    }




    public function store(Request $request) {
        Role::create([
            'name' => $request->name,
            'guard_name' => $request->guard_name,
        ]);

        
        return redirect()->back();
    }
}
