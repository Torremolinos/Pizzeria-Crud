<?php
function conectarBD()
{
    //Funcion que nos conecta a la base de datos, tenemos que mandarle la direccion ip del host, el usuario, la clave y el nombre de la BD
    $cadena_conexion = 'mysql:dbname=dwes_t3;host=127.0.0.1';
    $usuario = "root";
    $contrasenia = "";
    try {
        //Se crea el objeto de conexion a la base de datos y se devueve
        $bd = new PDO($cadena_conexion, $usuario, $contrasenia);
        return $bd;
    } catch (PDOException $e) {
        echo "Error conectar BD: " . $e->getMessage();
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Recoger los datos del formulario
    $nombre = $_POST['nombre'];
    $pizza = $_POST['pizza'];
    $precioPizza = $_POST['precio'];
    $ingredientesPizza = $_POST['ingredientes'];

    // Preparar la consulta SQL
    // Verificar que los datos no estén vacíos antes de insertar
    if (!empty($nombre) && !empty($pizza) && !empty($precioPizza) && !empty($ingredientesPizza)) {
        // Preparar la consulta SQL
        $conn = conectarBD();
        $insertar = $conn->prepare("INSERT INTO pedidos (nombre_pizza, precio_pizza, ingredientes_pizza) VALUES (:nombre, :precio, :ingredientes)");

        // Vincular los parámetros
        $insertar->bindParam(':nombre', $nombre);
        $insertar->bindParam(':precio', $precioPizza);
        $insertar->bindParam(':ingredientes', $ingredientesPizza);

        $insertar->execute();
    }
    echo "Inserta los datos correctamente";
}

$conn = conectarBD();
$pizzas = $conn->query("SELECT nombre, precio, ingredientes FROM pedidos")->fetchAll(PDO::FETCH_ASSOC);
$totalPizzas = count($pizzas);
$precioTotal = 0;
$consulta = $conn->prepare("SELECT nombre, ingredientes, precio FROM pizzas");
                    $consulta->execute();

?>
<html>
<head>
    <title>Formulario de Pedido</title>
    <link rel="stylesheet" href="../styles/pedido.css">
</head>

<body>

    <form action="pedido.php" method="POST">
        <label for="pizza">Pizza:</label>
        <select name="pizza" id="pizza" required>
            <option value="">Seleccione una pizza</option>
            <?php foreach ($pizzas as $pizza) { ?>
                <option value="<?php echo $pizza['nombre']; ?>"><?php echo $pizza['nombre']; ?></option>
            <?php } ?>
        </select><br><br>
        <input type="submit" value="Añadir Pizza al Pedido">
    </form>

    <h1>Formulario de Pedido</h1>
    <div class="tabla">
        <table border="2">
            <tr>
                <th>Nombre</th>
                <th>Precio</th>
                <th>Ingredientes</th>
            </tr>
            <?php foreach ($pizzas as $pedido) { ?>
                <tr>
                    <td><?php echo $pedido['nombre']; ?></td>
                    <td><?php echo $pedido['precio']; ?></td>
                    <td><?php echo $pedido['ingredientes']; ?></td>
                </tr>
            <?php 
                $precioTotal += $pedido['precio'];
            } ?>
            <tr>
                <td colspan="2">Cantidad de Pizzas Pedidas:</td>
                <td><?php echo $totalPizzas; ?></td>
            </tr>
            <tr>
                <td colspan="2">Precio Total:</td>
                <td><?php echo $precioTotal; ?></td>
            </tr>
        </table>
    </div>
    <a href="gracias.php">Finalizar Pedido</a>
</body>

</html>