<?php
    session_start();

    include 'functions.php';

    if (isset($_GET['action'])) {
        if ($_GET['action'] === 'remove' && isset($_GET['id'])) {
            $product_id = (int)$_GET['id'];
            removeFromCart($product_id);
            header('Location: cart.php');
            exit;
        } else if($_GET['action'] === 'clear') {
            clearCart();
        }
    }
    
    $cart = getCart();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="styles.css">
</head>
<?php
    include 'header.php';
?>
<body>
    <div class="container">
        <?php if (count($cart) != 0): ?>
            <table>
                <thead>
                    <tr>
                        <th>id</th>
                        <th>name</th>
                        <th>price</th>
                        <th>count</th>
                        <th>sum</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $total_sum = 0;
                    foreach ($cart as $product) {
                        $sum = $product['price'] * $product['count'];
                        $total_sum += $sum;
                        echo "<tr>
                                <td>{$product['id']}</td>
                                <td>{$product['name']}</td>
                                <td>\${$product['price']}</td>
                                <td>{$product['count']}</td>
                                <td>\${$sum}</td>
                                <td><a href=\"cart.php?action=remove&id=$product[id]\" class='delete-button'>Delete</a></td>
                            </tr>";
                    }
                    ?>
                    <tr>
                        <td>Total</td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <?php
                        echo "<td>{$total_sum}</td>"
                        ?>
                        <td></td>
                    </tr>
                </tbody>
            </table>
            <div class="buttons-table">
                <a href="cart.php?action=clear"><button>Cancel</button></a>
                <a href="cart.php?action=clear"><button>Pay</button></a>
            </div>
        <?php else: ?>
            <a href="index.php" class="empty-notify">Перейти до покупок</a>
        <?php endif; ?>
    </div>
</body>
<?php
    include 'footer.php';
?>
</html>