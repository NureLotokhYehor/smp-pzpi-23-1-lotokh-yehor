<header>
    <div class="container">
        <nav class="menu">
            <div class="menu-item">
                <img src="images/home.png" alt="home">
                <a href="index.php">Home</a>
            </div>
            <div class="menu-item">
                <img src="images/menu.png" alt="home">
                <a href="index.php">Products</a>
            </div>
            <?php if (isset($_SESSION['userid'])): ?>
                <div class="menu-item">
                    <img src="images/cart.png" alt="home">
                    <a href="cart.php">Cart</a>
                </div>
                <div class="menu-item">
                    <img src="images/user.png" alt="profile">
                    <a href="profile.php">Profile</a>
                </div>
                <div class="menu-item">
                    <img src="images/user.png" alt="Logout">
                    <a href="logout.php">Logout</a>
                </div>
            <?php else: ?>
                <div class="menu-item">
                    <img src="images/user.png" alt="login">
                    <a href="login.php">Login</a>
                </div>
            <?php endif; ?>
        </nav>
    </div>
</header>