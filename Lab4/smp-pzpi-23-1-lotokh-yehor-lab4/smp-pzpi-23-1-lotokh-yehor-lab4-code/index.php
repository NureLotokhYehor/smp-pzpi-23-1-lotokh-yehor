<?php
    session_start();

    include 'functions.php';
    $products = getProducts();

    $errorMessage = "";

    if(isset($_SESSION['userid'])) {
        $userid = $_SESSION['userid'];
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $errorCount = true;
        foreach ($products as $product) {
            $productName = strtolower($product['name']) . 'Count';
            if (isset($_POST[$productName])) {
                if($_POST[$productName] != 0) {
                    addToCart($product['id'], $_POST[$productName]);
                    $errorCount = false;
                }
            }
        }
        if($errorCount == false) {
            $errorMessage = "";
            header ( 'Location: cart.php' );
            exit;
        } else {
            $errorMessage = "Будь ласка оберіть товар";
        }
    }
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
    <?php if (isset($_SESSION['userid'])): ?>
    <div class="container">
        <?php
            if(!empty($errorMessage)) {
                echo "<p>$errorMessage</p>";
            }
        ?>
        <form class="catalog" action="#" method="POST">
            <?php
                foreach ($products as $product) {
                    ?>
                    <div class="product">
                        <label for="<?= strtolower($product['name']) ?>"><?= $product['name'] ?></label>
                        <input type="number" id="<?= strtolower($product['name']) ?>Count" name="<?= strtolower($product['name']) ?>Count" value="0" min="0">
                        <span>$<?= $product['price'] ?></span>
                    </div>
                    <?php
                }
            ?>

            <div class="submit-form">
                <button type="submit">Send</button>
            </div>
        </form>
    </div>
    <?php else: ?>
    <?php
        include 'page404.php';
    ?>
    <?php endif; ?>
</body>
<?php
    include 'footer.php';
?>
</html>