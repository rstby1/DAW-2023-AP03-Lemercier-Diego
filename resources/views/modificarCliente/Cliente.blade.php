<html>
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link rel="stylesheet" href="..//resources/css/styles.css">
    <link href="https://fonts.bunny.net/css?family=figtree:400,600&display=swap" rel="stylesheet" />
    <form action="{{ route('buscarCliente') }}" method="post">
        <!-- An unexamined life is not worth living. - Socrates -->
        <label for="cuit">Ingrese CUIT del Cliente:</label>
        <input type="text" name="cuit" id="cuit" required>
        <button  class="btn" type="submit">Buscar Cliente</button>
    </form>

</html>


