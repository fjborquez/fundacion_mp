<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index()
    {
        return User::all();
    }
 
    public function show($id)
    {
        return User::find($id);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'password' => 'required',
            'email' => 'required|email|unique:users',
        ]);

        $data = $request->only(['password', 'email', 'name']);
        $data['password'] = Hash::make($data['password']);

        return User::create($data);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'email' => 'email|unique:users'
        ]);

        $data = $request->only(['password', 'email', 'name']);

        if ($request->has('password')) {
            $data['password'] = Hash::make($data['password']);
        }

        $user = User::findOrFail($id);

        $user->update($data);
        return $user;
    }

    public function delete(Request $request, $id)
    {
        $user = User::findOrFail($id);
        $user->delete();

        return 204;
    }
}