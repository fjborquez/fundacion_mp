<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminUsuarioController
{
    public function index() 
    {
        return view('usuarios', [
            'usuarios' => User::all()
        ]);
    }

    public function add()
    {
        return view('crearUsuarios', [
            'formUrl' => '/crearUsuarios',
            'usuario' => new User
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'lastname' => 'required',
            'password' => 'required',
            'email' => 'required|email|unique:users',
        ]);

        $data = $request->only(['password', 'email', 'name', 'lastname']);
        $data['password'] = Hash::make($data['password']);
        $user = User::create($data);
        // TODO: Mensaje error o exito
        return view('crearUsuarios', [
            'usuario' => $user,
            'formUrl' => '/modificarUsuarios'
        ]);
    }

    public function modify($id)
    {
        $user = User::findOrFail($id);

        return view('crearUsuarios', [
            'usuario' => $user,
            'formUrl' => '/modificarUsuarios'
        ]);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'email' => 'email|unique:users,email,' . $id
        ]);

        $data = $request->only(['password', 'email', 'name', 'lastname']);
        $user = User::findOrFail($id);

        if ($request->has('password') && 
            $user->password === $request->get('password')) {
            $data['password'] = Hash::make($data['password']);
        }

        $user->update($data);
         // TODO: Mensaje error o exito
        return view('crearUsuarios', [
            'usuario' => $user,
            'formUrl' => '/modificarUsuarios'
        ]);
    }

    // TODO: Eliminar
}