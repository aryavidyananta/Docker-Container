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

// Create Table if not exists
$createTableQuery = "CREATE TABLE IF NOT EXISTS items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    price DECIMAL(10, 2) NOT NULL,
    description TEXT
)";
$connection->exec($createTableQuery);

// Handle form submission for Create and Update
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['create'])) {
        $name = $_POST['name'];
        $price = $_POST['price'];
        $description = $_POST['description'];

        // Insert new item
        $stmt = $connection->prepare("INSERT INTO items (name, price, description) VALUES (?, ?, ?)");
        $stmt->execute([$name, $price, $description]);
        echo "<div class='notification'>Barang berhasil ditambahkan!</div>";
    } elseif (isset($_POST['update'])) {
        $id = $_POST['id'];
        $name = $_POST['name'];
        $price = $_POST['price'];
        $description = $_POST['description'];

        // Update existing item
        $stmt = $connection->prepare("UPDATE items SET name = ?, price = ?, description = ? WHERE id = ?");
        $stmt->execute([$name, $price, $description, $id]);
        echo "<div class='notification'>Barang berhasil diperbarui!</div>";
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
    echo "<script>window.location.href = window.location.href;</script>"; // Refresh page without reloading full page
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
    <title>CRUD Barang</title>
    <style>
        /* Reset default styling */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Arial', sans-serif;
            background-color: #f4f4f4;
            color: #333;
            line-height: 1.6;
            padding: 20px;
        }

        h1, h2 {
            color: #333;
            margin-bottom: 15px;
        }

        /* Centering the page */
        .container {
            width: 80%;
            margin: 0 auto;
            max-width: 1200px;
        }

        /* Styling for forms */
        form {
            background-color: #fff;
            padding: 20px;
            margin-bottom: 20px;
            border-radius: 5px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        label {
            display: block;
            margin: 10px 0 5px;
            font-weight: bold;
        }

        input, textarea, button {
            width: 100%;
            padding: 12px;
            margin-bottom: 10px;
            border-radius: 5px;
            border: 1px solid #ccc;
            font-size: 16px;
        }

        input[type="number"], textarea {
            max-width: 500px;
        }

        button {
            background-color: #5cb85c;
            color: #fff;
            font-size: 16px;
            cursor: pointer;
            border: none;
        }

        button:hover {
            background-color: #4cae4c;
        }

        /* List Styling */
        ul {
            list-style-type: none;
            padding-left: 0;
        }

        li {
            background-color: #fff;
            padding: 15px;
            margin: 10px 0;
            border-radius: 5px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        li a {
            text-decoration: none;
            color: #007bff;
        }

        li a:hover {
            text-decoration: underline;
        }

        .item-actions {
            display: flex;
            gap: 10px;
        }

        .item-actions a {
            color: #d9534f;
        }

        /* Notifikasi */
        .notification {
            background-color: #5bc0de;
            color: #fff;
            padding: 10px;
            margin-bottom: 20px;
            border-radius: 5px;
            display: none;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .container {
                width: 100%;
                padding: 10px;
            }

            input[type="number"], textarea {
                max-width: 100%;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>CRUD Barang</h1>

        <!-- Form for creating or updating item (same form used for both) -->
        <h2>Tambah / Edit Barang</h2>
        <form method="POST" action="index.php">
            <input type="hidden" name="id" value="<?= isset($itemToEdit) ? $itemToEdit['id'] : '' ?>">
            <label for="name">Nama Barang:</label>
            <input type="text" name="name" value="<?= isset($itemToEdit) ? htmlspecialchars($itemToEdit['name']) : '' ?>" required><br>
            <label for="price">Harga Barang:</label>
            <input type="number" step="0.01" name="price" value="<?= isset($itemToEdit) ? htmlspecialchars($itemToEdit['price']) : '' ?>" required><br>
            <label for="description">Deskripsi Barang:</label>
            <textarea name="description"><?= isset($itemToEdit) ? htmlspecialchars($itemToEdit['description']) : '' ?></textarea><br>
            <button type="submit" name="<?= isset($itemToEdit) ? 'update' : 'create' ?>">
                <?= isset($itemToEdit) ? 'Perbarui Barang' : 'Tambah Barang' ?>
            </button>
        </form>

        <!-- List of items -->
        <h2>Daftar Barang</h2>
        <?php if ($items): ?>
            <ul>
                <?php foreach ($items as $item): ?>
                    <li>
                        <div>
                            <strong><?= htmlspecialchars($item['name']) ?></strong><br>
                            Harga: <?= htmlspecialchars($item['price']) ?> IDR<br>
                            Deskripsi: <?= htmlspecialchars($item['description']) ?>
                        </div>
                        <div class="item-actions">
                            <a href="?edit=<?= $item['id'] ?>">Edit</a> | 
                            <a href="javascript:void(0);" onclick="deleteItem(<?= $item['id'] ?>)">Hapus</a>
                        </div>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php else: ?>
            <p>Tidak ada barang yang tersedia.</p>
        <?php endif; ?>

    </div>

    <!-- JavaScript untuk AJAX hapus barang -->
    <script>
        function deleteItem(itemId) {
            if (confirm('Apakah Anda yakin ingin menghapus barang ini?')) {
                var xhr = new XMLHttpRequest();
                xhr.open('GET', 'index.php?delete=' + itemId, true);
                xhr.onload = function() {
                    if (xhr.status === 200) {
                        alert('Barang berhasil dihapus!');
                        location.reload(); // Reload the page after deleting item
                    }
                };
                xhr.send();
            }
        }
    </script>
</body>
</html>
