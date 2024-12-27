<?php
// Database configuration
$database = "mydb";
$user = "myuser";
$password = "password";
$host = "mysql";

try {
    // Create PDO connection
    $connection = new PDO("mysql:host={$host};dbname={$database};charset=utf8", $user, $password);
    $connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
    exit;
}

// Fetch all products (or categories)
$query = $connection->query("SELECT * FROM items");
$products = $query->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>ShopEasy</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <style>
    .glass-effect {
      background: rgba(255, 255, 255, 0.2);
      backdrop-filter: blur(10px);
      -webkit-backdrop-filter: blur(10px);
      border: 1px solid rgba(255, 255, 255, 0.3);
      border-radius: 12px;
    }
    .fade-in {
      opacity: 0;
      animation: fadeIn 1s forwards;
    }
    @keyframes fadeIn {
      to {
        opacity: 1;
      }
    }
  </style>
</head>
<body class="bg-gradient-to-b from-gray-100 to-gray-300 min-h-screen">
  <!-- Header -->
  <nav class="bg-gradient-to-r from-blue-600 to-purple-600 shadow-lg sticky top-0 z-50">
    <div class="container mx-auto px-4 py-4 flex justify-between items-center">
      <h1 class="text-3xl font-extrabold text-white flex items-center gap-2">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" viewBox="0 0 20 20" fill="currentColor">
          <path d="M10 2a8 8 0 11-4.906 14.39L2 18l1.61-3.094A8 8 0 0110 2z" />
        </svg>
        ShopEasy
      </h1>
      <ul class="flex space-x-6">
        <li><a href="#hero" class="text-white text-lg font-semibold hover:underline hover:text-gray-300 transition duration-300">Home</a></li>
        <li><a href="#about" class="text-white text-lg font-semibold hover:underline hover:text-gray-300 transition duration-300">About</a></li>
        <li><a href="#categories" class="text-white text-lg font-semibold hover:underline hover:text-gray-300 transition duration-300">Categories</a></li>
        <li><a href="#contact" class="text-white text-lg font-semibold hover:underline hover:text-gray-300 transition duration-300">Contact</a></li>
      </ul>
      <a href="login.php" class="bg-white text-blue-500 px-6 py-2 rounded-md hover:bg-gray-200 transition">Login</a>
    </div>
  </nav>

  <!-- Hero Section -->
  <section id="hero" class="relative bg-cover bg-center text-white py-24 flex items-center justify-center" style="background-image: url('https://o-cdn-cas.oramiland.com/parenting/images/Cara_Menggambar_Gunung.width-800.format-webp.webp');">
    <div class="absolute inset-0 bg-black opacity-50"></div> <!-- Optional overlay to darken the image -->
    <div class="container mx-auto text-center relative z-10">
      <h2 class="text-5xl font-bold mb-6">Welcome to ShopEasy</h2>
      <p class="text-xl mb-8">Find the best products at unbeatable prices!</p>
      <button class="bg-white text-blue-500 px-8 py-4 rounded-md font-bold hover:bg-gray-200 transition">Shop Now</button>
    </div>
  </section>
  
  <!-- Kategori Produk Section -->
  <section id="categories" class="py-16 bg-gray-200">
    <div class="container mx-auto px-4">
      <h2 class="text-4xl font-bold text-gray-800 text-center mb-12">Categories</h2>
      <div class="grid grid-cols-1 md:grid-cols-3 gap-10">
        <!-- Display Categories or Products from the database -->
        <?php foreach ($products as $product): ?>
          <div class="glass-effect p-6 shadow-lg transform hover:scale-105 transition duration-300">
            <img src="uploads/<?= htmlspecialchars($product['image']) ?>" alt="<?= htmlspecialchars($product['name']) ?>" class="w-full h-48 object-cover rounded-lg">
            <h3 class="text-xl font-bold text-gray-800 mt-4"><?= htmlspecialchars($product['name']) ?></h3>
            <p class="text-gray-600 mt-2"><?= htmlspecialchars($product['description']) ?></p>
            <p class="text-blue-500 font-semibold mt-2"><?= htmlspecialchars($product['price']) ?> IDR</p>
          </div>
        <?php endforeach; ?>
      </div>
    </div>
  </section>

  <!-- Contact Section -->
  <section id="contact" class="py-16 bg-gray-100">
    <div class="container mx-auto px-4 text-center">
      <h2 class="text-4xl font-bold text-gray-800 mb-8">Contact Us</h2>
      <p class="text-lg text-gray-600 mb-4">Have questions or need support? We're here to help!</p>
      <form class="max-w-md mx-auto">
        <div class="mb-4">
          <input type="text" placeholder="Your Name" class="w-full px-4 py-2 rounded-md border border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-500">
        </div>
        <div class="mb-4">
          <input type="email" placeholder="Your Email" class="w-full px-4 py-2 rounded-md border border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-500">
        </div>
        <div class="mb-4">
          <textarea placeholder="Your Message" class="w-full px-4 py-2 rounded-md border border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-500"></textarea>
        </div>
        <button type="submit" class="bg-blue-500 text-white px-6 py-2 rounded-md hover:bg-blue-600 transition">Send Message</button>
      </form>
    </div>
  </section>

  <!-- Footer -->
  <footer class="bg-gray-800 text-white py-12">
    <div class="container mx-auto grid grid-cols-1 md:grid-cols-3 gap-8">
      <div class="text-center md:text-left">
        <h3 class="text-lg font-bold mb-4">About ShopEasy</h3>
        <p class="text-gray-400">ShopEasy is your one-stop shop for amazing products at unbeatable prices.</p>
      </div>
      <div class="text-center">
        <h3 class="text-lg font-bold mb-4">Quick Links</h3>
        <ul class="space-y-2">
          <li><a href="#hero" class="text-gray-400 hover:text-white">Home</a></li>
          <li><a href="#about" class="text-gray-400 hover:text-white">About</a></li>
          <li><a href="#categories" class="text-gray-400 hover:text-white">Categories</a></li>
        </ul>
      </div>
      <div class="text-center md:text-right">
        <h3 class="text-lg font-bold mb-4">Follow Us</h3>
        <div class="flex justify-center md:justify-end space-x-4">
          <a href="#" class="text-gray-400 hover:text-white">Facebook</a>
          <a href="#" class="text-gray-400 hover:text-white">Twitter</a>
          <a href="#" class="text-gray-400 hover:text-white">Instagram</a>
        </div>
      </div>
    </div>
    <div class="mt-10 text-center">
      <p class="text-gray-400">© 2024 ShopEasy. All rights reserved.</p>
    </div>
  </footer>
</body>
</html>
