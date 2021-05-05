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
          <li class="nav-item ">
            <a class="nav-link" href="{{ url('/configuraciones') }}">Configuraciones</a>
          </li>
          <li class="nav-item active">
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
      <div class="row">
          <div class="col-md-6 offset-md-3">
          <form action="{{ $formUrl }}" method="post">
              {{ csrf_field() }}
              <div class="form-group">
                  <label for="name">Nombre</label>
                  <input type="text" class="form-control" id="name" name="name" value="{{ $usuario->name }}" aria-describedby="emailHelp" />
                  @if ($errors->has('name'))
                      <span class="text-danger">{{ $errors->first('name') }}</span>
                  @endif
              </div>
              <div class="form-group">
                  <label for="lastname">Apellidos</label>
                  <input type="text" class="form-control" id="lastname" name="lastname" value="{{ $usuario->lastname }}" aria-describedby="emailHelp" />
                  @if ($errors->has('email'))
                      <span class="text-danger">{{ $errors->first('lastname') }}</span>
                  @endif
              </div>
              <div class="form-group">
                  <label for="email">Email</label>
                  <input type="email" class="form-control" id="email" name="email" value="{{ $usuario->email }}" aria-describedby="emailHelp" />
                  @if ($errors->has('email'))
                      <span class="text-danger">{{ $errors->first('email') }}</span>
                  @endif
              </div>
              <div class="form-group">
                  <label for="password">Password</label>
                  <input type="password" class="form-control" id="password" name="password" value="{{ $usuario->password }}" />
                  @if ($errors->has('password'))
                      <span class="text-danger">{{ $errors->first('password') }}</span>
                  @endif
              </div>
              <input type="submit" class="btn btn-success" value="Guardar Usuario" />
              <a href="{{ url('/usuarios') }}" class="btn btn-danger">Cancelar</a>
          </form>
          </div>
      </div>
    </main><!-- /.container -->
        <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.bundle.min.js" integrity="sha384-Piv4xVNRyMGpqkS2by6br4gNJ7DXjqk09RmUpJ8jgGtD7zP9yug3goQfGII0yAns" crossorigin="anonymous"></script>
        <script src="https://cdn.jsdelivr.net/npm/feather-icons@4.28.0/dist/feather.min.js"></script>
    </body>
</html>
