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

        return redirect()->route('modificarUsuarios', ['id' => $user->id])->with('message','Usuario creado');
    }

    public function modify($id)
    {
        $user = User::findOrFail($id);

        return view('crearUsuarios', [
            'usuario' => $user,
            'formUrl' => '/modificarUsuarios/' . $id
        ]);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required',
            'lastname' => 'required',
            'password' => 'required',
            'email' => 'email|unique:users,email,' . $id
        ]);

        $data = $request->only(['password', 'email', 'name', 'lastname']);
        $user = User::findOrFail($id);

        if ($request->has('password') && 
            $user->password !== $request->get('password')) {
            $data['password'] = Hash::make($data['password']);
        }

        $user->update($data);
        $request->session()->now('message','Usuario modificado'); 

        return view('crearUsuarios', [
            'usuario' => $user,
            'formUrl' => '/modificarUsuarios' . $id
        ]);
    }

    public function delete(Request $request, $id)
    {
        $user = User::findOrFail($id);
        $user->delete();

        $request->session()->now('message','Usuario eliminado'); 

        return view('usuarios', [
            'usuarios' => User::all()
        ]);
    }
}