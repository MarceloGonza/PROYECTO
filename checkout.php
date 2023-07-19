<?php

require 'config/database.php';
require './config/config.php';
$db = new Database();
$con = $db->conectar();

$productos = isset($_SESSION['carrito']['productos']) ? $_SESSION['carrito']['productos'] : null;

$lista_carrito = array();

if($productos!= null){
    foreach($productos as $clave => $cantidad){
        $sql = $con->prepare("SELECT id, nombre, precio, descuento, $cantidad AS cantidad 
        FROM productos WHERE
        id=? AND activo = 1");
        $sql->execute([$clave]);
        $lista_carrito[] = $sql->fetch(PDO::FETCH_ASSOC);//traer de a uno
    }
}


//session_destroy();
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nación Z</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" 
    rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" 
    crossorigin="anonymous">
    <link rel="stylesheet" href="./CSS/style.css">
</head>
<body>

<header>
<div class="navbar navbar-expand navbar-dark bg-orange shadow-sm">
        <div class="container d-flex justify-content-between">
            <a href="#" class="navbar-brand d-flex align-items-center">
                <img width="40px" src="./images/logo 10.png" alt="Logo" class="logo-img mr-2">
                <strong>Tienda Online</strong>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarHeader" 
            aria-controls="navbarHeader" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarHeader">
                <ul class="navbar-nav me-auto mb-2 mb-sm-0">
                    <li class="nav-item">
                        <a href="#" class="nav-link active">Productos</a>
                    </li>
                    <li class="nav-item">
                        <a href="#" class="nav-link">Contacto</a>
                    </li>
                </ul>
                <a href="#" class="btn custom-btn">
                    Carrito<span id="num_cart" class="badge bg-dark"><?php echo $num_cart; ?></span>
                </a>
            </div>
        </div>
    </div>
</header>

<main>
    <div class="container">
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Producto</th>
                        <th>Precio</th>
                        <th>Cantidad</th>
                        <th>Subtotal</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
    <?php if($lista_carrito == null) {
        echo '<tr><td colspan="5" class="text-center"><b>Lista vacía</b></td></tr>';
    } else {
        $total = 0;
        foreach($lista_carrito as $producto) {
            $_id = $producto['id'];
            $nombre = $producto['nombre'];
            $precio = $producto['precio'];
            $descuento = $producto['descuento'];
            $cantidad = $producto['cantidad'];
            $precio_desc = $precio - (($precio * $descuento) / 100);
            $subtotal = $cantidad * $precio_desc;
            $total += $subtotal;
            ?>
            <tr>
                <td><?php echo $nombre; ?></td>
                <td>$<?php echo number_format($precio_desc, 3, '.', ','); ?></td>
                <td> 
                <input type="number" min="1" max="10" step="1" value="<?php echo 
                $cantidad; ?>" size="5" id="cantidad_<?php echo $_id; ?>" 
                onchange="actualizaCantidad(this.value, <?php echo $_id; ?>, <?php echo $precio; ?> )">
                </td>
                <td class="subtotal_<?= $_id?>">
                    $<?php echo number_format($subtotal, 3, '.', ','); ?>
                </td>
                <td>
                    <a href="#" id="eliminar" class="btn btn-warning btn-sm" data-bs-id="<?php echo $_id; ?>" data-bs-toggle="modal" data-bs-target="eliminaModal">Eliminar</a> 
                </td>
            </tr>
        <?php } ?>
        <tr>
            <td colspan="3"></td>
            <td colspan="2">
                <p class="h3" id="total">
                    $<?php echo number_format($total, 3, '.', ','); ?>
                </p>
            </td>
        </tr>
    <?php } ?>
</tbody>

            </table>          
        </div>
            <div class="row">
                <div class="col-md-5 offset-md-7 d-grid gap-2">
                    <button class="btn custom-btn btn-lg">
                        Pagar ahora
                    </button>
                </div>
            </div>

    </div>
</main>


<script src="./javascript/script1.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"
integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" 
crossorigin="anonymous"></script>

<script>
function actualizaCantidad(cantidad, id, precio) {
    document.querySelector('#subtotal_[$id]').innerHTML = precio*cantidad;
    let url = './clases/actualizar_carrito.php';
    let formData = new FormData();
    formData.append('action', 'agregar');
    formData.append('id', id);
    formData.append('cantidad', cantidad);

    fetch(url, {
        method: 'POST',
        body: formData,
        mode: 'cors'
    })
    .then(response => response.json())
    .then(data => {
        if (data.ok) {
            let divsubtotal = document.getElementById('subtotal_' + id);
            divsubtotal.innerHTML = '$' + data.sub; // Update the subtotal displayed on the page
            actualizarTotal(); // Call function to update the total
        }
    });
}

function actualizarTotal() {
    let subtotales = document.querySelectorAll('td.subtotal');
    let total = 0;
    subtotales.forEach(subtotal => {
        let subtotalValue = parseFloat(subtotal.textContent.replace('$', '').replace(',', ''));
        total += subtotalValue;
    });

    let totalElement = document.getElementById('total');
    totalElement.textContent = '$' + total.toFixed(3).replace(/\d(?=(\d{3})+\.)/g, '$&,'); // Update the total displayed on the page
}
</script>
</body>
</html>
