<?php
    session_start();

    include 'user_functions.php';

    $userid = $_SESSION['userid'];
    $userInfo = getUserInfo($userid);

    $errorMessage = "";

    $uploadDir = 'images/';
    $uploadFilePath = $uploadDir . $userid;
    $userImagePath = "";
    if (file_exists($uploadFilePath)) {
        $userImagePath = $uploadFilePath;
    } else {
        $userImagePath = 'images/image.png';
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (isset($_FILES['file-input'])) {
            $fileTmpPath = $_FILES['file-input']['tmp_name'];
            $fileName = $_FILES['file-input']['name'];
            $fileSize = $_FILES['file-input']['size'];
            $fileType = $_FILES['file-input']['type'];

            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }

            if (move_uploaded_file($fileTmpPath, $uploadFilePath)) {
                echo "Файл був завантажен!";
            } else {
                echo "Помилка при завантаженні файла";
            }
        } else {
            echo $_FILES['file-input']['error'];
        }

        $name = $_POST['name'];
        $surname = $_POST['surname'];
        $date = $_POST['dob'];
        $desc = $_POST['description'];
        
        if(empty($name) || empty($surname) || empty($date) || empty($desc)) {
            $errorMessage = "Ви заповнили не всі поля";
        } else if (strlen($name) <= 1 || strlen($surname) <= 1) {
            $errorMessage = "Ім'я користувача повинно бути більше 1 символа";
        } else if(!checkAge($date)) {
            $errorMessage = "Користувач має бути доросліше 16 років";
        } else {
            editUserInfo($userid, $name, $surname, $date, $desc);
            header ( 'Location: profile.php' );
            exit;
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
    <link rel="stylesheet" href="profile.css">
</head>
<?php
    include 'header.php';
?>
<body>
    <div class="form-container">
        <form action="#" method="post" enctype="multipart/form-data">
            <div class="form-left">
                <?php echo "<img src=$userImagePath alt=\"Upload Image\">" ?>
                <input type="file" id="file-input" name="file-input" class="file-input" accept="image/*">
            </div>
            <div class="form-right">
                <label for="name">Name</label>
                <input type="text" id="name" name="name" value="<?php echo $userInfo['name'] ?>" required>

                <label for="surname">Surname</label>
                <input type="text" id="surname" name="surname" value="<?php echo $userInfo['surname'] ?>" required>

                <label for="dob">Date of Birth</label>
                <input type="date" id="dob" name="dob" value="<?php echo $userInfo['date'] ?>" required>

                <label for="description">Brief description</label>
                <textarea id="description" name="description" rows="4" required><?php echo $userInfo['desc'] ?></textarea>

                <button type="submit">Зберегти</button>
            </div>
        </form>
        <?php
            if(!empty($errorMessage)) {
                echo "<p>$errorMessage</p>";
            }
        ?>
    </div>
</body>
<?php
    include 'footer.php';
?>
</html>