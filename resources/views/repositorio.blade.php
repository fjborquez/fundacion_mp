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
          <li class="nav-item">
            <a class="nav-link" href="{{ url('/configuraciones') }}">Configuraciones</a>
          </li>
          <li class="nav-item ">
            <a class="nav-link" href="{{ url('/usuarios') }}">Gestión de Usuarios</a>
          </li>
          <li class="nav-item active">
            <a class="nav-link" href="{{ url('/repositorio') }}">Repositorio</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="{{ url('/logout') }}">Cerrar Sesión</a>
          </li>
        </ul>

      </div>
    </nav>

    <main role="main" class="container">

      <div class="container">

          <div class="row">
              <div class="col-12">
                  <h3>Repositorio Descarga CSV</h3>
                <table class="table table-light">
                    <thead class="thead-light">
                        <tr>
                            <th>#</th>
                            <th>Fecha</th>
                            <th>Nombre Archivo</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>1</td>
                            <td>29-04-2021</td>
                            <td>29042021_lic-no-select.csv</td>
                            <td>
                                <button type="button" class="btn btn-info btn-sm">Descargar</button>
                            </td>
                        </tr>
                    </tbody>
                </table>
              </div>
          </div>
      </div>

    </main><!-- /.container -->


        <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.bundle.min.js" integrity="sha384-Piv4xVNRyMGpqkS2by6br4gNJ7DXjqk09RmUpJ8jgGtD7zP9yug3goQfGII0yAns" crossorigin="anonymous"></script>
        <script src="https://cdn.jsdelivr.net/npm/feather-icons@4.28.0/dist/feather.min.js"></script>
    </body>
</html>
