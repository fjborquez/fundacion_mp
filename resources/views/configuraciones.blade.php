<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/css/bootstrap.min.css" integrity="sha384-B0vP5xmATw1+K9KRQjQERJvTumQW0nPEzvF6L/Z6nronJ3oUOFUFpCjEUQouq2+l" crossorigin="anonymous">
        <link rel="stylesheet" href="css/app.css" />
        <title>API MERCADO PÚBLICO</title>

        <!-- Fonts -->
        <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap" rel="stylesheet">

    </head>
    <body>

    <nav class="navbar navbar-expand-md navbar-dark bg-primary fixed-top">
      <a class="navbar-brand" href="#">BANCA ETICA</a>
      <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarsExampleDefault" aria-controls="navbarsExampleDefault" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>

      <div class="collapse navbar-collapse" id="navbarsExampleDefault">
      <ul class="navbar-nav mr-auto">
          <li class="nav-item">
            <a class="nav-link" href="{{ url('/') }}">Home </a>
          </li>
          <li class="nav-item active">
            <a class="nav-link" href="{{ url('/configuraciones') }}">Configuraciones</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="{{ url('/usuarios') }}">Gestión de Usuarios</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="{{ url('/repositorio') }}">Repositorio</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="{{ url('/logout') }}">Cerrar Sesión</a>
          </li>
        </ul>
      </div>
    </nav>

    @if(session()->has('message'))
    <div class="alert alert-success">
        {{ session()->get('message') }}
    </div>
    @endif

    @if(session()->has('error'))
    <div class="alert alert-error">
        {{ session()->get('error') }}
    </div>
    @endif

    <main role="main" class="container">

        <div>
            <h2>Formulario de Configuraciones para Inclusiones/Exclusiones</h2>
            <small>Las palabras deben ir por obligacion separadas por punto y coma (;) y
            todas las palabras en minusculas. Esto para mantener concordancia con la base de datos de mercado publico</small>

            <form class="mb-5 pb-5 mt-5 row" action="/configuraciones" method="post">
            @foreach ($settings as $settingName => $settingValue)
                <div class="form-group col-md-6">

                    <label for="{{ $settingName }}">{{ ucfirst(str_replace('_', ' ', $settingName)) }}</label>
                    <textarea class="form-control" id="{{ $settingName }}" name="settings[{{ $settingName }}]" rows="5">{{$settingValue}}</textarea>

                </div>
                @endforeach
                {{ csrf_field() }}
                <input type="submit" class="btn btn-primary" value="GUARDAR CONFIGURACION" />
            </form>
        </div>

    </main><!-- /.container -->

        <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.bundle.min.js" integrity="sha384-Piv4xVNRyMGpqkS2by6br4gNJ7DXjqk09RmUpJ8jgGtD7zP9yug3goQfGII0yAns" crossorigin="anonymous"></script>
        <script src="https://cdn.jsdelivr.net/npm/feather-icons@4.28.0/dist/feather.min.js"></script>
    </body>
</html>

