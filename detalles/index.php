<?php
require '../config/database.php';
require '../config/config.php';

$db = new Database();
$con = $db->conectar();

$id = isset($_GET['id']) ? trim($_GET['id']) : '';
$token = isset($_GET['token']) ? $_GET['token'] : '';

if ($id == '' || $token == '') {
    echo "Error al procesar la solicitud";
    exit;
} else {
    $token_tmp = hash_hmac('sha1', $id, KEY_TOKEN);

    if ($token == $token_tmp) {
        $sql = $con->prepare("SELECT count(id) FROM productos WHERE id=? AND activo = 1 
        LIMIT 1");
        $sql->execute([$id]);

        if ($sql->fetchColumn() > 0) {
            $sql = $con->prepare("SELECT nombre, descripcion, precio, descuento 
            FROM productos WHERE id=? AND activo = 1");
            $sql->execute([$id]);
            $row = $sql->fetch(PDO::FETCH_ASSOC);
            $nombre = $row['nombre'];
            $descripcion = $row['descripcion'];
            $precio = $row['precio'];
            $descuento = $row['descuento'];
            $precio_desc = $precio - (($precio * $descuento) /100 );

            $dir_images = '../images/productos/' . $id . '/';
            $rutaImg = $dir_images . 'principal.jpg';

            if (!file_exists($rutaImg)) {
                $rutaImg = '../images/no-photo.png';
            }

            $imagenes = array();

            $archivos = scandir($dir_images);

            foreach ($archivos as $archivo) {
                $extension = pathinfo($archivo, PATHINFO_EXTENSION);

                if ($archivo != 'principal.jpg' && ($extension == 'jpg' || $extension == 'jpeg')) {
                    $imagenes[] = $dir_images . $archivo;
                }
            }
        }
    } else {
        echo "Error al procesar la solicitud";
        exit;
    }
}
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
    <link rel="stylesheet" href="../CSS/style.css">
</head>
<body>

<header>
<div class="navbar navbar-expand navbar-dark bg-orange shadow-sm">
        <div class="container d-flex justify-content-between">
            <a href="#" class="navbar-brand d-flex align-items-center">
                <img width="40px" src="../images/logo 10.png" alt="Logo" class="logo-img mr-2">
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
                <a href="../checkout.php" class="btn custom-btn">
                    Carrito<span id="num_cart" class="badge bg-dark"><?php echo $num_cart; ?></span>
                </a>
            </div>
        </div>
    </div>
</header>

<main>
    <div class="container">
        <div class="row">
            <div class="col-md-6 order-md-1">
                <div id="carouselImages" class="carousel slide">
                    <div class="carousel-inner">
                        <div class="carousel-item active">
                            <img src="<?php echo $rutaImg ?>" class="d-block w-100">
                        </div>

                        <?php if (!empty($imagenes)) { ?>
                            <?php foreach($imagenes as $img) { ?>
                                <div class="carousel-item">
                                    <img src="<?php echo $img ?>" class="d-block w-100">
                                </div>
                            <?php } ?>
                        <?php } ?>
                    </div>

                    <button class="carousel-control-prev" type="button" data-bs-target="#carouselImages" data-bs-slide="prev">
                        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                        <span class="visually-hidden">Previous</span>
                    </button>
                    <button class="carousel-control-next" type="button" data-bs-target="#carouselImages" data-bs-slide="next">
                        <span class="carousel-control-next-icon" aria-hidden="true"></span>
                        <span class="visually-hidden">Next</span>
                    </button>
                </div>
            </div>
            
            <div class="col-md-6 order-md-2">
                <h2><?php echo $nombre; ?></h2>

                <?php if($descuento >0 ) {?>

                    <p><del> $ <?php echo $precio; ?></del></p>
                    <h2>
                        $ <?php echo $precio_desc; ?>
                        <small class="text-success"><?php echo $descuento; ?>% descuento</small>
                    </h2>
                <?php }else { ?>

                <h2>$ <?php echo $precio; ?></h2>

                <?php } ?>

                <p class="lead"><?php echo $descripcion; ?></p>
            

                <div class="d-grid gap-3 col-10 mx-auto">
                    <button class="btn custom-btn" type="button">
                        Comprar ahora
                    </button>
                    <button class="btn btn-outline custom-btn" type="button" onclick="addProducto
                    (<?php echo $id; ?>, '<?php echo $token_tmp; ?>')">
                        Agregar al carrito
                    </button>
                </div>
            </div>
        </div>
    </div>
</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"
integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" 
crossorigin="anonymous"></script>

<script>
//function para el carrito de compras
function addProducto(id, token) {
    let url = '../clases/carrito.php';
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
