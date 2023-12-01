<?php
session_start();

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

$conn = conectarBD();

function listarPizzas($conn)
{
    $consulta = $conn->prepare("SELECT nombre, ingredientes, precio FROM pizzas");
    $consulta->execute();
    echo "<form method='POST'>";
    echo "<label for='pizza'>Seleccione una pizza</label>";
    echo "<select name='pizza'>";
    echo "<option value='0'>Seleccione una pizza</option>";
    foreach ($consulta->fetchAll(PDO::FETCH_ASSOC) as $row) {
        echo "<option value='" . $row["nombre"] . "'>" . $row['nombre'] . "</option>";
    }
    echo "</select>";
    echo "<label for='cantidad'>Cantidad</label>";
    echo "<input type='number' name='cantidad' value='1' min='1'>";
    echo "<button type='submit'>Añadir a pedido</button>";
    echo "</form>";
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST["pizza"]) && isset($_POST["cantidad"])) {
        $pizza = $_POST["pizza"];
        $cantidad = $_POST["cantidad"];
        if (!isset($_SESSION["pedido"])) {
            $_SESSION["pedido"] = array();
        }

        $pizzaIndex = -1;
        foreach ($_SESSION["pedido"] as $index => $item) {
            if ($item["pizza"] == $pizza) {
                $pizzaIndex = $index;
                break;
            }
        }

        if ($pizzaIndex != -1) {
            $_SESSION["pedido"][$pizzaIndex]["cantidad"] += $cantidad;
        } else {
            $_SESSION["pedido"][] = array("pizza" => $pizza, "cantidad" => $cantidad);
        }
    }

    if (isset($_POST["confirmar_pedido"])) {
        unset($_SESSION["pedido"]);
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pedido de <?php echo $_SESSION['nombre'] ?></title>
    <link rel="stylesheet" href="../styles/pedido.css">
    <style>
        input {
            margin: 5px;
            padding: 10px 20px;
            border-radius: 3px;
        }

        button {
            padding: 10px 20px;
            background-color: burlywood;
            color: white;
            border: none;
            border-radius: 3px;
            cursor: pointer;
        }

        button:hover {
            background-color: bisque;
        }
    </style>
</head>

<body>
    <h1>PIZZERIA TRATORIA NAPOLÉS</h1>
    <h1>Pedido de <?php echo $_SESSION['nombre'] ?></h1>
    <h2>Seleccione las pizzas que desea añadir al pedido:</h2>
    <?php listarPizzas($conn); ?>

    <p>Resumen de tu pedido, <?php echo $_SESSION['nombre'] ?></p>
    <?php
    if (isset($_SESSION["pedido"]) && !empty($_SESSION["pedido"])) {
        echo "<div class='tabla'>";
        echo "<table border='2'>";
        echo "<tr><th>Pizza</th><th>Cantidad</th></tr>";
        foreach ($_SESSION["pedido"] as $item) {
            echo "<tr><td>$item[pizza]</td><td>$item[cantidad]</td></tr>";
        }
        echo "</table>";
        echo "</div>";
    } else {
        echo "<p>No hay pizzas en el pedido.</p>";
    }
    ?>
    <form method="POST" action="gracias.php">
        <button type="submit" name="confirmar_pedido">Confirmar Pedido</button>
    </form>
    <a href="Index.php">Volver Al Inicio</a>
</body>

</html>