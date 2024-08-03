<?php
session_start();
include 'include/config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT id, password FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->bind_result($id, $hashed_password);
        $stmt->fetch();
        if (password_verify($password, $hashed_password)) {
            $_SESSION['user'] = $username;
            header("Location: index.php");
            exit();
        } else {
            $_SESSION['notification'] = "Password salah.";
            $_SESSION['notification_type'] = "danger";
        }
    } else {
        $_SESSION['notification'] = "Username tidak ditemukan.";
        $_SESSION['notification_type'] = "danger";
    }

    $stmt->close();
}

include 'include/header.php';
?>

<div class="container d-flex justify-content-center align-items-center min-vh-100">
    <div class="card shadow-sm" style="width: 100%; max-width: 400px;">
        <div class="card-body">
            <h4 class="card-title text-center mb-4">Login</h4>
            <?php if (isset($_SESSION['notification'])): ?>
                <div class="alert alert-<?php echo $_SESSION['notification_type']; ?> alert-dismissible fade show" role="alert" id="notificationAlert">
                    <?php echo $_SESSION['notification']; ?>
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <?php unset($_SESSION['notification'], $_SESSION['notification_type']); ?>
            <?php endif; ?>

            <form action="login.php" method="POST">
                <div class="form-floating mb-3">
                    <input type="text" class="form-control" id="username" name="username" placeholder="Username" required>
                    <label for="username"><i class="bi bi-person"></i> Username</label>
                </div>
                <div class="form-floating mb-3">
                    <input type="password" class="form-control" id="password" name="password" placeholder="Password" required>
                    <label for="password"><i class="bi bi-lock"></i> Password</label>
                </div>
                <button type="submit" class="btn btn-primary w-100">Login</button>
            </form>
        </div>
    </div>
</div>

<?php include 'include/footer.php'; ?>

<script>
    $(document).ready(function() {
        setTimeout(function() {
            $("#notificationAlert").alert('close');
        }, 3000);
    });
</script>
