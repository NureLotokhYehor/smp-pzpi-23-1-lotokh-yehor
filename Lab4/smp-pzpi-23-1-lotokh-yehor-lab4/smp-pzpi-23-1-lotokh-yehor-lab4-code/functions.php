<?php
    function getDatabase() {
        $db = new SQLite3('database.db');
        return $db;
    }

    function getProducts() {
        $products = [];
        $db = getDatabase();

        try {
            $result = $db->query('SELECT id, name, price FROM products');
            while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
                $products[] = [
                    'id' => $row['id'],
                    'name' => $row['name'],
                    'price' => $row['price']
                ];
            }
        } catch (Exception $e) {
            echo "Помилка з'єдання: " . $e->getMessage();
        }

        return $products;
    }

    function addToCart($product_id, $count) {
        $db = getDatabase();
        $sessionId = session_id();
        if(!productExistInCart($product_id)) {
            $query = "INSERT INTO cart (sesionId, productId, count)
            VALUES ('$sessionId', '$product_id', '$count')";
        } else {
            $query = "UPDATE
                cart
            SET
                count = '$count'
            WHERE
                productId = '$product_id' AND sesionId = '$sessionId'";
        }
        
        $db->exec($query);
    }

    function getCart() {
        $cart = [];
        $sessionId = session_id();
        $db = getDatabase();

        try {
            $result = $db->query(
            "SELECT
                p.id,
                p.name,
                p.price,
                c.count
            FROM
                cart AS c
            INNER JOIN
                products AS p
            ON
                c.productId = p.id
            WHERE
                c.sesionId = '$sessionId'");
            while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
                $cart[] = [
                    'id' => $row['id'],
                    'name' => $row['name'],
                    'price' => $row['price'],
                    'count' => $row['count']
                ];
            }
        } catch (Exception $e) {
            echo "Помилка підключення: " . $e->getMessage();
        }

        return $cart;
    }

    function productExistInCart($product_id) {
        $sessionId = session_id();
        $db = getDatabase();

        $result = $db->query(
        "SELECT
            id
        FROM
            cart
        WHERE
            sesionId = '$sessionId' AND productId = '$product_id'");
        if($result->fetchArray(SQLITE3_ASSOC))
            return true;
        else
            return false;
    }

    function removeFromCart($product_id) {
        $db = getDatabase();
        $sessionId = session_id();
        $query = "DELETE FROM
            cart
        WHERE
            sesionId = '$sessionId' AND productId = '$product_id'";
        $db->exec($query);
    }

    function clearCart() {
        $db = getDatabase();
        $sessionId = session_id();
        $query = "DELETE FROM
            cart
        WHERE
            sesionId = '$sessionId'";
        $db->exec($query);
    }
?>