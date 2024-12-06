<?php
$servername = "localhost";
$username = "root";
$password = "";

try {
    $conn = new PDO("mysql:host=$servername;dbname=bachpgbh1592", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

// Add product
if (isset($_POST['add'])) {
    $name = $_POST['name'];
    $price = $_POST['price'];
    $image = $_FILES['image']['name'];
    $quantity = $_POST['quantity'];
    $target = "uploads/" . basename($image);

    $stmt = $conn->prepare("INSERT INTO products (name, price, image, quantity) VALUES (?, ?, ?, ?)");
    $stmt->execute([$name, $price, $image, $quantity]);

    if (move_uploaded_file($_FILES['image']['tmp_name'], $target)) {
        echo "Image uploaded successfully!";
    } else {
        echo "Failed to upload image!";
    }
}

// Edit product
if (isset($_POST['update'])) {
    $id = $_POST['product_id '];
    $name = $_POST['name'];
    $price = $_POST['price'];
    $image = $_FILES['image']['name'];
    $quantity = $_POST['quantity'];
    $target = "uploads/" . basename($image);

    if ($image) {
        $stmt = $conn->prepare("UPDATE products SET name = ?, price = ?, image = ?, quantity = ? WHERE product_id = ?");
        $stmt->execute([$name, $price, $quantity, $image,  $id]);
        move_uploaded_file($_FILES['image']['tmp_name'], $target);
    } else {
        $stmt = $conn->prepare("UPDATE products SET name = ?, price = ?, quantity = ? WHERE product_id = ?");
        $stmt->execute([$name, $price, $quantity, $id]);
    }
}

// Delete product
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $stmt = $conn->prepare("DELETE FROM products WHERE product_id = ?");
    $stmt->execute([$id]);
}

// Get product list
$products = $conn->query("SELECT * FROM products")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style1.css">
    <title>Product Management</title>
</head>
<body>
    <h1>Product Management</h1>

    <form method="POST" enctype="multipart/form-data">
        <label for="id">Product ID:</label>
        <input type="number" name="id" id="id">
        <label for="name">Product Name:</label>
        <input type="text" name="name" id="name" required>
        <label for="price">Price:</label>
        <input type="number" name="price" id="price" step="0.01" required>
        <label for="image">Image:</label>
        <input type="file" name="image" id="image">
        <label for="quantity">Quantity:</label>
        <input type="number" name="quantity" id="quantity" required>
        <button type="submit" name="add">Add</button>
        <button type="submit" name="update">Update</button>
    </form>

    <!-- Back Button -->
    <div style="margin-top: 20px;">
        <a href="product_list.php">
            <button>Return to Product List</button>
        </a>
    </div>

    <h2>Product List</h2>
    <table border="1">
        <tr>
            <th>ID</th>
            <th>Product Name</th>
            <th>Price</th>
            <th>Image</th>
            <th>Quantity</th>
            <th>Action</th>
        </tr>
        <?php foreach ($products as $product): ?>
        <tr>
            <td><?= $product['product_id'] ?></td>
            <td><?= $product['name'] ?></td>
            <td><?= $product['price'] ?></td>
            <td><img src="uploads/<?= $product['image'] ?>" width="50"></td>
            <td><?= $product['quantity'] ?></td>
            <td>
                <button onclick="editProduct(<?= htmlspecialchars(json_encode($product)) ?>)">Edit</button>
                <a href="?delete=<?= $product['product_id'] ?>" onclick="return confirm('Do you want to delete?')">Delete</a>
            </td>
        </tr>
        <?php endforeach; ?>
    </table>

    <script>
        function editProduct(product) {
            document.getElementById('id').value = product.id;
            document.getElementById('name').value = product.name;
            document.getElementById('price').value = product.price;
        }
    </script>
</body>
</html>

