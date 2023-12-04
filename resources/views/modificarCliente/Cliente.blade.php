<html>
    <form action="{{ route('modificarCliente') }}" method="post">
        <!-- An unexamined life is not worth living. - Socrates -->
        <input id="cuit" type="number" name="cuit" required>CUITCliente
        <br><!-- comment -->
        <input id="razonsocial" type="text" name="razonsocial" required>Razón Social
        <br><!-- comment -->
        <input id="nro" type="number" name="nro" required>Número de cliente
        <br><!-- comment -->
        <input class="btn" type="submit" name="submit"> 
    </form>
</html>


