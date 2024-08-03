<?php
session_start();
include 'include/config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    if ($password !== $confirm_password) {
        $_SESSION['notification'] = "Password dan konfirmasi password tidak cocok.";
        $_SESSION['notification_type'] = "danger";
    } else {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("INSERT INTO users (username, password) VALUES (?, ?)");
        $stmt->bind_param("ss", $username, $hashed_password);

        if ($stmt->execute()) {
            $_SESSION['notification'] = "Registrasi berhasil. Silakan login.";
            $_SESSION['notification_type'] = "success";
            header("Location: login.php");
            exit();
        } else {
            $_SESSION['notification'] = "Terjadi kesalahan. Silakan coba lagi.";
            $_SESSION['notification_type'] = "danger";
        }

        $stmt->close();
    }
}

include 'include/header.php';
?>

<div class="container mt-4">
    <h2>Register</h2>
    <?php if (isset($_SESSION['notification'])): ?>
        <div class="alert alert-<?php echo $_SESSION['notification_type']; ?> alert-dismissible fade show" role="alert" id="notificationAlert">
            <?php echo $_SESSION['notification']; ?>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <?php unset($_SESSION['notification'], $_SESSION['notification_type']); ?>
    <?php endif; ?>

    <form action="register.php" method="POST">
        <div class="form-group">
            <label for="username">Username</label>
            <input type="text" class="form-control" id="username" name="username" required>
        </div>
        <div class="form-group">
            <label for="password">Password</label>
            <input type="password" class="form-control" id="password" name="password" required>
        </div>
        <div class="form-group">
            <label for="confirm_password">Confirm Password</label>
            <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
        </div>
        <button type="submit" class="btn btn-primary">Register</button>
    </form>
</div>

<?php include 'include/footer.php'; ?>

<script>
    $(document).ready(function() {
        setTimeout(function() {
            $("#notificationAlert").alert('close');
        }, 3000);
    });
</script>
