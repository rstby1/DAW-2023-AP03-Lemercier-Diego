<html>
    <head>
        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link rel="stylesheet" href="..//resources/css/styles.css">
        <link href="https://fonts.bunny.net/css?family=figtree:400,600&display=swap" rel="stylesheet" />
    </head>
    <body>
        <div class="import conteinerPHP">
            <!-- Knowing is not enough; we must apply. Being willing is not enough; we must do. - Leonardo da Vinci -->
            <form action="{{ route('altaCliente') }}" method="post">
                <label for="cuit">CUITCliente</label>
                <br><!-- comment -->
                <input id="cuit" type="number" name="cuit" required>
                <br><!-- comment -->
                <label for="razonsocial">Razón Social</label>
                <br><!-- comment -->
                <input id="razonsocial" type="text" name="razonsocial" required>
                <br><!-- comment -->
                <label for="nro">Número de cliente</label>            
                <br><!-- comment -->
                <input id="nro" type="number" name="nro" required>
                <br><!-- comment -->
                <input class="btn" type="submit" name="submit"> 
            </form>
            @if(isset($_GET['msg']))
            <div class="alert alert-success">
                {{ urldecode($_GET['msg']) }}
            </div>
            @endif
            <a class="btn" href="{{ url('/') }}">Volver</a>
        </div>
    </body>
</html>

