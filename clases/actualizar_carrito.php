<?php
require '../config/config.php';
require '../config/database.php';


$datos = array(); // Array para almacenar los datos de la respuesta

if (isset($_POST['action'])) {
    $action = isset($_POST['id']) ? $_POST['id'] : 0;

    if ($action == 'agregar') {
        $cantidad = isset($_POST['cantidad']) ? $_POST['cantidad'] : 0;
        $respuesta = agregar($id, $cantidad);
        if ($respuesta > 0) {
            $datos['ok'] = true;
        } else {
            $datos['ok'] = false;
        }
        $datos['sub'] = number_format($respuesta, 3, '.', ',');
    }elseif($action == 'eliminar'){
        $datos['ok'] =eliminar($id);
    } else {
        $datos['ok'] = false;
    }
} else {
    $datos['ok'] = false;
}

echo json_encode($datos); 

function agregar($id, $cantidad)
{
    $res = 0;
    if ($id > 0 && $cantidad > 0 && is_numeric($cantidad)) {
        if (isset($_SESSION['carrito']['productos'][$id])) {
            $_SESSION['carrito']['productos'][$id] = $cantidad;
        } else {
            $_SESSION['carrito']['productos'][$id] = $cantidad;
        }

        $db = new Database();
        $con = $db->conectar();
        $sql = $con->prepare("SELECT precio, descuento FROM productos WHERE id=? AND activo = 1");
        $sql->execute([$id]);
        $row = $sql->fetch(PDO::FETCH_ASSOC);
        $precio = $row['precio'];
        $descuento = $row['descuento'];
        $precio_desc = $precio - (($precio * $descuento) / 100);
        $res = $precio * $precio_desc;
    }

    return $res;
}

function eliminar($id){
    if($id > 0){
        if (isset($_SESSION['carrito']['productos'][$id])){
            unset($_SESSION['carrito']['productos'][$id]);
            return true;
        }
    }else {
        return false;
    }
}
?>
