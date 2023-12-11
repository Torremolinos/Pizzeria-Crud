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




if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id_cliente = 1; // Asumiendo el ID del cliente
    $fecha_pedido = date("Y-m-d H:i:s");
    $detalle_pedido = "";
    $total = 0;

    for($i = 1; $i <= 4; $i++) {
        $id = $_POST["pizza$i"];
        if (!empty($id)) {
            $sql = "SELECT nombre, precio FROM pizzas WHERE id = :id";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':id', $id);
            $stmt->execute();
            
            if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $detalle_pedido .= $row["nombre"] . ", ";
                $total += $row["precio"];
            }
        }
    }

    $detalle_pedido = rtrim($detalle_pedido, ", ");

    // Insertar en la tabla de pedidos
    $sql = "INSERT INTO pedidos (id_cliente, fecha_pedido, detalle_pedido, total)
            VALUES (:id_cliente, :fecha_pedido, :detalle_pedido, :total)";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':id_cliente', $id_cliente, PDO::PARAM_INT);
    $stmt->bindParam(':fecha_pedido', $fecha_pedido);
    $stmt->bindParam(':detalle_pedido', $detalle_pedido);
    $stmt->bindParam(':total', $total);

    if ($stmt->execute()) {
        $mensaje = "Pedido realizado con éxito";
    } else {
        $mensaje = "Error al realizar el pedido";
    }
} else {
    header("Location:error.html");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PROCESO PEDIDOS</title>
    <link rel="stylesheet" href="../styles/pedido.css">
</head>
<body>
<h1><?php echo $mensaje; ?></h1>
    
    <?php if ($mensaje === "Pedido realizado con éxito"): ?>
    <h2>Resumen del Pedido</h2>
    <p>Fecha del Pedido: <?php echo $fecha_pedido; ?></p>
    <p>Detalle del Pedido: <?php echo $detalle_pedido; ?></p>
    <p>Total: $<?php echo number_format($total, 2); ?></p>
    <?php endif; ?>
        
</body>
</html>