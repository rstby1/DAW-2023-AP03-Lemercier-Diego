<html>
    <head>
        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link rel="stylesheet" href="..//resources/css/styles.css">
        <link href="https://fonts.bunny.net/css?family=figtree:400,600&display=swap" rel="stylesheet" />    
    </head>
    <body>
        <div class="import">
            <form action="{{ route('buscarCliente') }}" method="post">
                <!-- An unexamined life is not worth living. - Socrates -->
                <label for="cuit">Enter customer CUIT</label>
                <br>
                <input type="text" name="cuit" id="cuit" required>
                <br>
                <br>
                <button  class="btn" type="submit">Search customer</button>
            </form>
            <a class="btn" href="{{ url('/') }}">Volver</a>
        </div>

    </body>
</html>


