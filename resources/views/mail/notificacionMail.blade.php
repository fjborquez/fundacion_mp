<!DOCTYPE html>
<html>
    <head>
        <title>{{ $asunto }}</title>
    </head>
    <body>
        <h1>{{ $asunto }}</h1>
        <p>{{ $texto }}</p>
        <table>
            <thead>
                <tr>
                    <th>Código</th>
                    <th>Nombre</th>
                    <th>Estado</th>
                    <th>Comprador</th>
                    <th>Monto estimado</th>
                </tr>
            </thead>
            <tbody>
                @foreach($licitaciones as $licitacion)
                <tr>
                    <td>
                        <a href="http://www.mercadopublico.cl/Procurement/Modules/RFB/DetailsAcquisition.aspx?idlicitacion={{ $licitacion['CodigoExterno'] }}">
                            {{ $licitacion['CodigoExterno'] }}
                        </a>
                    </td>
                    <td>{{ $licitacion['Nombre'] }}</td>
                    <td>{{ $licitacion['Estado'] }}</td>
                    <td>{{ $licitacion['Comprador']['NombreOrganismo'] }}</td>
                    <td>{{ $licitacion['MontoEstimado'] }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </body> 
</html>
