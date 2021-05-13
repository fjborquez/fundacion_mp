<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Storage;

class AdminRepositorioController
{
    public function index() 
    {
        $files = collect(Storage::disk('local')->listContents('csv'))
            ->sortByDesc(function ($file) {
                return $file['timestamp'];
            });
        return view('repositorio', [
            'files' => $files
        ]);
    }

    public function download($file) {
        return Storage::download('csv/' . $file);
    }
}
