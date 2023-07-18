<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>

    <script src="https://www.paypal.com/sdk/js?client-id=AfqntC2pC92Mf9U_S55chjGY8k8xJTTg5ynnVQvDZoNOoU6dH4ljmqB6-933Xd-iHXJe2vc3vBPo1gcB"></script>
</head>
<body>
    <div id="paypal-button-container"></div>

    <script>
        paypal.Buttons({
            style: {
                shape: 'pill',
                label: 'pay'
            },
            createOrder: function(data, actions) {
                return actions.order.create({
                    purchase_units: [{
                        amount: {
                            value: '1000'
                        }
                    }]
                });
            },
            onAprove: function(data, action){
                actions.order.capture().then(function(detalles){
                    window.location.href="pago/completado.php";
                });
            },


            onCancel: function(data){
                alert ("Pago cancelado");
                console.log(data);
            }
        }).render('#paypal-button-container');
    </script>
</body>
</html>
