<?php
    include 'functions.php';

    function login($name, $pass) {
        $db = getDatabase();

        $result = $db->query(
        "SELECT
            id
        FROM
            users
        WHERE
            login = '$name' AND pass = '$pass'");
        $row = $result->fetchArray(SQLITE3_ASSOC);
        return $row['id'];
    }

    function updateLoginTime($userid) {
        $currentDateTime = date("Y-m-d H:i:s");
        $db = getDatabase();
        $query = "UPDATE
            users
        SET
            time = '$currentDateTime'
        WHERE
            id = '$userid'";
        $db->exec($query);
    }

    function getUserInfo($userid) {
        $userInfo = [];
        $db = getDatabase();
        try {
            $result = $db->query(
            "SELECT
                name,
                surname,
                desc,
                date
            FROM
                users
            WHERE
                id = '$userid'");
            while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
                $userInfo['name'] = $row['name'];
                $userInfo['surname'] = $row['surname'];
                $userInfo['desc'] = $row['desc'];
                $userInfo['date'] = $row['date'];
            }
        } catch (Exception $e) {
            echo "Помилка підключення: " . $e->getMessage();
        }

        return $userInfo;
    }

    function editUserInfo($userid, $name, $surname, $date, $desc) {
        $db = getDatabase();
        $query = "UPDATE
            users
        SET
            name = '$name',
            surname = '$surname',
            date = '$date',
            desc = '$desc'
        WHERE
            id = '$userid'";
        $db->exec($query);
    }

    function checkAge($birthdate) {
        $birthdate = new DateTime($birthdate);
        $currentDate = new DateTime();
        $age = $currentDate->diff($birthdate)->y;

        if ($age >= 16) {
            return true;
        } else {
            return false;
        }
    }
?>