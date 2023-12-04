<html>
    <head>
        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link rel="stylesheet" href="..//resources/css/styles.css">
        <link href="https://fonts.bunny.net/css?family=figtree:400,600&display=swap" rel="stylesheet" />
    </head>
    <body>

        <!-- Knowing is not enough; we must apply. Being willing is not enough; we must do. - Leonardo da Vinci -->
        <form action="{{ route('altaCliente') }}" method="post">
            <input id="cuit" type="number" name="cuit" required>CUITCliente
            <br><!-- comment -->
            <input id="razonsocial" type="text" name="razonsocial" required>Razón Social
            <br><!-- comment -->
            <input id="nro" type="number" name="nro" required>Número de cliente
            <br><!-- comment -->
            <input class="btn" type="submit" name="submit"> 
        </form>
        @if(isset($_GET['msg']))
        <div class="alert alert-success">
            {{ urldecode($_GET['msg']) }}
        </div>
        @endif
    </body>
</html>

