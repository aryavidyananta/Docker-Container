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

// Create Table if not exists (with image column)
$createTableQuery = "CREATE TABLE IF NOT EXISTS items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    price DECIMAL(10, 2) NOT NULL,
    description TEXT,
    image VARCHAR(255)
)";
$connection->exec($createTableQuery);

// Handle form submission for Create and Update
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $price = $_POST['price'];
    $description = $_POST['description'];
    $image = null;

    // Handle image upload
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $imageName = $_FILES['image']['name'];
        $imageTmpName = $_FILES['image']['tmp_name'];
        $imageExt = pathinfo($imageName, PATHINFO_EXTENSION);
        $imageNewName = uniqid('', true) . '.' . $imageExt;
        $imagePath = 'uploads/' . $imageNewName;

        // Ensure the upload directory exists
        if (!is_dir('uploads')) {
            mkdir('uploads', 0777, true);
        }

        if (move_uploaded_file($imageTmpName, $imagePath)) {
            $image = $imageNewName;
        } else {
            echo "<div class='bg-red-500 text-white p-4 mb-4 rounded'>Gagal mengunggah gambar.</div>";
        }
    }

    if (isset($_POST['create'])) {
        // Insert new item
        $stmt = $connection->prepare("INSERT INTO items (name, price, description, image) VALUES (?, ?, ?, ?)");
        $stmt->execute([$name, $price, $description, $image]);
        echo "<div class='bg-green-500 text-white p-4 mb-4 rounded'>Barang berhasil ditambahkan!</div>";
    } elseif (isset($_POST['update'])) {
        $id = $_POST['id'];

        // Update existing item
        if ($image) {
            $stmt = $connection->prepare("UPDATE items SET name = ?, price = ?, description = ?, image = ? WHERE id = ?");
            $stmt->execute([$name, $price, $description, $image, $id]);
        } else {
            $stmt = $connection->prepare("UPDATE items SET name = ?, price = ?, description = ? WHERE id = ?");
            $stmt->execute([$name, $price, $description, $id]);
        }
        echo "<div class='bg-green-500 text-white p-4 mb-4 rounded'>Barang berhasil diperbarui!</div>";
    }
}

// Fetch all items
$query = $connection->query("SELECT * FROM items");
$items = $query->fetchAll(PDO::FETCH_ASSOC);

// Handle delete action (AJAX)
if (isset($_GET['delete'])) {
    $deleteId = $_GET['delete'];
    $stmt = $connection->prepare("DELETE FROM items WHERE id = ?");
    $stmt->execute([$deleteId]);
    echo "<script>window.location.href = window.location.href;</script>";
}

// Handle edit action
$itemToEdit = null;
if (isset($_GET['edit'])) {
    $editId = $_GET['edit'];
    $stmt = $connection->prepare("SELECT * FROM items WHERE id = ?");
    $stmt->execute([$editId]);
    $itemToEdit = $stmt->fetch(PDO::FETCH_ASSOC);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard CRUD Barang</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">

    <div class="max-w-4xl mx-auto p-8 bg-white shadow-md rounded-lg mt-8">
        <h1 class="text-3xl font-semibold text-center text-gray-800 mb-6">Dashboard CRUD Barang</h1>

        <!-- Form for creating or updating item -->
        <h2 class="text-2xl font-semibold mb-4 text-gray-700"><?= isset($itemToEdit) ? 'Edit Barang' : 'Tambah Barang' ?></h2>
        <form method="POST" enctype="multipart/form-data" class="space-y-4">
            <input type="hidden" name="id" value="<?= isset($itemToEdit) ? $itemToEdit['id'] : '' ?>">
            <div>
                <label for="name" class="block text-gray-700">Nama Barang:</label>
                <input type="text" name="name" value="<?= isset($itemToEdit) ? htmlspecialchars($itemToEdit['name']) : '' ?>" class="w-full p-2 border border-gray-300 rounded-md" required>
            </div>

            <div>
                <label for="price" class="block text-gray-700">Harga Barang:</label>
                <input type="number" step="0.01" name="price" value="<?= isset($itemToEdit) ? htmlspecialchars($itemToEdit['price']) : '' ?>" class="w-full p-2 border border-gray-300 rounded-md" required>
            </div>

            <div>
                <label for="description" class="block text-gray-700">Deskripsi Barang:</label>
                <textarea name="description" class="w-full p-2 border border-gray-300 rounded-md" required><?= isset($itemToEdit) ? htmlspecialchars($itemToEdit['description']) : '' ?></textarea>
            </div>

            <div>
                <label for="image" class="block text-gray-700">Gambar Barang:</label>
                <input type="file" name="image" accept="image/*" class="w-full p-2 border border-gray-300 rounded-md">
            </div>

            <div class="flex justify-center">
                <button type="submit" name="<?= isset($itemToEdit) ? 'update' : 'create' ?>" class="px-6 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600 transition">
                    <?= isset($itemToEdit) ? 'Perbarui Barang' : 'Tambah Barang' ?>
                </button>
            </div>
        </form>

        <!-- List of items -->
        <h2 class="text-2xl font-semibold mt-6 mb-4 text-gray-700">Daftar Barang</h2>
        <?php if ($items): ?>
            <div class="space-y-4">
                <?php foreach ($items as $item): ?>
                    <div class="p-4 bg-white shadow-md rounded-lg flex justify-between items-center">
                        <div>
                            <strong class="text-xl text-gray-800"><?= htmlspecialchars($item['name']) ?></strong> - 
                            <span class="text-gray-600"><?= htmlspecialchars($item['price']) ?> IDR</span><br>
                            <p class="text-gray-700"><?= htmlspecialchars($item['description']) ?></p>
                        </div>
                        <?php if ($item['image']): ?>
                            <img src="uploads/<?= htmlspecialchars($item['image']) ?>" alt="Image of <?= htmlspecialchars($item['name']) ?>" class="w-16 h-16 object-cover rounded-md ml-4">
                        <?php endif; ?>
                        <div class="flex space-x-4">
                            <a href="?edit=<?= $item['id'] ?>" class="text-blue-500 hover:underline">Edit</a> |
                            <a href="javascript:void(0);" onclick="deleteItem(<?= $item['id'] ?>)" class="text-red-500 hover:underline">Hapus</a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <p class="text-center text-gray-700">Tidak ada barang yang tersedia.</p>
        <?php endif; ?>
    </div>

    <!-- JavaScript for delete action -->
    <script>
        function deleteItem(id) {
            if (confirm('Apakah Anda yakin ingin menghapus barang ini?')) {
                window.location.href = '?delete=' + id;
            }
        }
    </script>
</body>
</html>
