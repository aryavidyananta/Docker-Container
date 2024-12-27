<?php
session_start();
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Dummy Login Validation
    if ($username === 'admin' && $password === 'password') {
        $_SESSION['logged_in'] = true;
        header('Location: dashboard.php');
        exit;
    } else {
        $error = "Invalid username or password.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login - ShopEasy</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center">
  <form method="POST" class="bg-white p-6 rounded-md shadow-md w-80">
    <h2 class="text-2xl font-bold mb-4">Login</h2>
    <?php if (!empty($error)): ?>
      <p class="text-red-500 text-sm mb-4"><?= $error ?></p>
    <?php endif; ?>
    <input type="text" name="username" placeholder="Username" class="w-full mb-4 px-4 py-2 border rounded">
    <input type="password" name="password" placeholder="Password" class="w-full mb-4 px-4 py-2 border rounded">
    <button type="submit" class="w-full bg-blue-500 text-white py-2 rounded">Login</button>
  </form>
</body>
</html>
