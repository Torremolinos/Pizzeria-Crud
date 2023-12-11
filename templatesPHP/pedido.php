<?php
session_start();
$conn = conectarBD();

if (!isset($_SESSION["usuario"])) {
    header("Location: index.php?redirigido=true");
}

function conectarBD()
{
    $cadena_conexion = 'mysql:dbname=dwes_t3;host=127.0.0.1';
    $usuario = "root";
    $clave = "";
    try {
        $bd = new PDO($cadena_conexion, $usuario, $clave);
        return $bd;
    } catch (PDOException $e) {
        echo "Error conectar BD: " . $e->getMessage();
    }
}

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Realizar Pedido</title>
    <link rel="stylesheet" href="../styles/pedido.css">
</head>
<body>
    <h1>Realizar Pedido</h1>
    <form action="procesar_pedido.php" method="post">
    <?php for ($i = 1; $i <= 4; $i++): ?>
        <label for="pizza<?php echo $i; ?>">Pizza<?php echo $i; ?>:</label>
        <select name="pizza<?php echo $i; ?>" id="pizza<?php echo $i; ?>">
            <option value="">Selecciona una Pizza</option>
            <?php
                $sql = "SELECT id, nombre FROM pizzas";
                $stmt = $conn->prepare($sql);
                $stmt->execute();

                foreach($stmt as $row){
                    echo "<option value='" . $row["id"] . "'>" . $row["nombre"] . "</option>";
                    //<option value='2'>Ejemplo</option>
                }

            ?>
        </select><br><br>
    <?php endfor; ?>
    <input type="submit" value="Hacer Pedido">
</form>

</body>
</html>