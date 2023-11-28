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
    $usuario = $_POST['coste'];
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
    }
    echo "Inserta los datos correctamente";
}
        ?>