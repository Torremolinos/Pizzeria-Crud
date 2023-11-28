<?php
session_start();
if (!isset($_SESSION["usuario"])) {
    header("Location: Index.php?redirigido=true");
}
function conectarBD()
{
    $cadena_conexion = 'mysql:dbname=dwes_t3;host=127.0.0.1';
    $usuario = "root";
    $contrasenia = "";

    try {
        $bd = new PDO($cadena_conexion, $usuario, $contrasenia);
        return $bd;
    } catch (PDOException $e) {
        echo "Error conectando a la bd: " . $e->getMessage();
    }
}

// Función para conectar a la BD y otras funciones
// Conectar a la base de datos
$conn = conectarBD();

// Revisa si el formulario ha sido enviado
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Recoger los datos del formulario
    $nombrePizza = $_POST['nombre'];
    $costePizza = $_POST['coste'];
    $precioPizza = $_POST['precio'];
    $ingredientesPizza = $_POST['ingredientes'];

    // Preparar la consulta SQL
    // Verificar que los datos no estén vacíos antes de insertar
    if (!empty($nombrePizza) && !empty($costePizza) && !empty($precioPizza) && !empty($ingredientesPizza)) {
        // Preparar la consulta SQL
        $insertar = $conn->prepare("INSERT INTO pizzas (nombre, coste, precio, ingredientes) VALUES (:nombre, :coste, :precio, :ingredientes)");

        // Vincular los parámetros
        $insertar->bindParam(':nombre', $nombrePizza);
        $insertar->bindParam(':coste', $costePizza);
        $insertar->bindParam(':precio', $precioPizza);
        $insertar->bindParam(':ingredientes', $ingredientesPizza);

        $insertar->execute();
    } elseif ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['borrar'])) {
        $pizza_id = $_POST['pizza_id'];

        // Preparar la consulta SQL para borrar la pizza
        $borrar = $conn->prepare("DELETE FROM pizzas WHERE id = :pizza_id");
        $borrar->bindParam(':pizza_id', $pizza_id);
        $borrar->execute();
    }
};


// Función para listar pizzas
function listarPizzas($conn)
{
    $consulta = $conn->prepare("SELECT id, nombre, ingredientes,coste , precio FROM pizzas");
    $consulta->execute();
    echo "<table border='2'>";
    echo "<tr><th>Pizza</th><th>Ingredientes</th><th>Precio</th><th>Acciones Admin</th></tr>";
    foreach ($consulta->fetchAll(PDO::FETCH_ASSOC) as $row) {
        echo "<tr>";
        echo "<td>$row[nombre]</td><td>$row[ingredientes]</td><td>$row[coste]</td><td> $row[precio]€</td>";
        echo "<td>
                <form action='editar_pizza.php' method='post'>
                    <input type='hidden' name='pizza_id' value='$row[id]'>
                    <input type='submit' name='editar' value='Editar'>
                </form>
                <form method='post' onsubmit='return confirm(\"¿Estás seguro de que deseas borrar esta pizza?\")'>
                    <input type='hidden' name='pizza_id' value='$row[id]'>
                    <input type='submit' name='borrar' value='Borrar'>
                </form>
              </td>";
        echo "</tr>";
    }
    echo "</table>";
}

?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Zona Admin</title>
    <link rel="stylesheet" href="../styles/index.css">
</head>

<body>
    <h1>Bienvenido, <?php echo $_SESSION['nombre'] ?></h1>
    <div class="formulario">
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            Nombre: <input type="text" name="nombre"><br>
            Coste: <input type="number" step="0.01" name="coste"><br>
            Precio: <input type="number" step="0.01" name="precio"><br>
            Ingredientes: <textarea name="ingredientes"></textarea><br>
            <input type="submit" name="submit" value="Añadir Pizza">
        </form>
    </div>
    <h1>Listado de Pizzas</h1>
    <div class="tabla">
        <?php
        listarPizzas($conn);
        ?>
    </div>
</body>

</html>