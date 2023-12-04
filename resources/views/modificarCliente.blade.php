<!DOCTYPE html>
<html>
    <head>
        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link rel="stylesheet" href="..//resources/css/styles.css">
        <link href="https://fonts.bunny.net/css?family=figtree:400,600&display=swap" rel="stylesheet" />
    </head>
    <body>
        @if(isset($_GET['msg']))
        <div class="alert alert-success">
            {{ urldecode($_GET['msg']) }}
        </div>
        @endif
        <!-- It is never too late to be what you might have been. - George Eliot -->
        <h2>Complete el formulario para poder modificar el cliente</h2>
        <form action="{{ url('modificarCliente/' . $cliente->CUITCliente) }}" method="post">
            <!-- @csrf  Agrega esto para incluir el token de CSRF -->

            <label for="cuit">CUITCliente:</label>
            <input id="cuit" type="number" name="cuit" value="{{ $cliente->CUITCliente }}" readonly required>
            <br>

            <label for="razonsocial">Razón Social:</label>
            <input id="razonsocial" type="text" name="razonsocial" value="{{ $cliente->RazonSocial }}" required>
            <br>

            <label for="nro">Número de cliente:</label>
            <input id="nro" type="number" name="nro" value="{{ $cliente->NroCliente }}" required>
            <br> 

            <input class="btn" type="submit" name="submit"> 
        </form>
        <a class="btn" href="{{ url('/buscarCliente') }}">Volver</a>
        <script src="..//resources/js/script.js"></script>
    </body>
</html>


