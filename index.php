<?php

require 'config/database.php';
require './config/config.php';

$db = new Database();
$con = $db->conectar();
$sql = $con->prepare("SELECT id, nombre, precio FROM productos WHERE activo = 1");
$sql->execute();
$resultado = $sql->fetchAll(PDO::FETCH_ASSOC);
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
                <a href="./checkout.php" class="btn custom-btn">
                    Carrito<span id="num_cart" class="badge bg-dark"><?php echo $num_cart; ?></span>
                </a>
            </div>
        </div>
    </div>
</header>



<main>
    <div class="container">

    <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 g-3">
        <?php foreach($resultado as $row) { ?>
        <div class="col">
            <div class="card shadow-sm">
                <?php

                $id = $row['id'];
                $imagen = "images/productos/" . $id . "/principal.jpg";

                if(!file_exists($imagen)){
                    $imagen = "images/no-photo.png";
                }

                ?>
                <img src="<?php echo $imagen; ?>" class="figura">
                <div class="card-body">
                    <h5 class="card-tittle"><?php echo $row['nombre']; ?></h5>
                    <p class="card-text">$ <?php echo $row['precio']; ?></p>
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="btn-group">
                            <a href="./detalles/index.php?id=<?php echo $row['id']; ?>
                            &token=<?php echo hash_hmac('sha1', $row['id'], KEY_TOKEN); ?>" 
                            class="btn custom-btn">Detalles</a>
                        </div>
                        <button class="btn btn-outline custom-btn" type="button" onclick="addProducto
                            (<?php echo $row['id']; ?>, '<?php echo hash_hmac('sha1', $row['id'], KEY_TOKEN); ?>')">
                            Agregar
                        </button>
                    </div>
                </div>
            </div>
        </div>
<?php } ?>
    </div>
</main>


<script src="./javascript/script1.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"
integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" 
crossorigin="anonymous"></script>

<script>
//function para el carrito de compras
function addProducto(id, token) {
    let url = './clases/carrito.php';
    let formData = new FormData();
    formData.append('id', id);
    formData.append('token', token);

    fetch(url, {
    method: 'POST',
    body: formData,
    mode: 'cors'
    })
    .then(response => response.json())
    .then(data => {
        console.log(data); // Verifica los datos obtenidos

        if (data.ok) {
        let elemento = document.getElementById('num_cart');
        elemento.innerHTML = data.numero;
        }
    });
}

</script>
</body>
</html>
