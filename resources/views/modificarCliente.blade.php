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
        <form action="{{ route('buscarCliente') }}" method="post">
            <input id="cuit" type="number" name="cuit" required>CUITCliente
            <!--             
            <br>
            <input id="razonsocial" type="text" name="razonsocial" required>Razón Social
            <br>
            <input id="nro" type="number" name="nro" required>Número de cliente
            <br> 
            -->
            <input class="btn" type="submit" name="submit"> 
        </form>

        <script src="..//resources/js/script.js"></script>
    </body>
</html>


