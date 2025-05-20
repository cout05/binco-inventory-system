<?php
require("dbconnect.php");
session_start();

if (!isset($_SESSION['is_login']) || $_SESSION['is_login'] !== true) {
    header('Location: login.html');
    exit;
}

$items = [];
$sql = "SELECT * FROM items WHERE status = 1";
$result = $conn->query($sql);
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $items[] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <title>Binco Archives</title>
</head>

<body>
    <h1 class="title">Binco's Apparel Archives</h1>
    <div class="nav-btn">
        <a href="./">Back to Inventory</a>
    </div>

    <table id="itemsTable">
        <thead>
            <tr>
                <th>ID</th>
                <th>Product Name</th>
                <th>Size</th>
                <th>Quantity</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if (count($items) > 0): ?>
                <?php foreach ($items as $item): ?>
                    <tr>
                        <td><?= htmlspecialchars($item['id']) ?></td>
                        <td><?= htmlspecialchars($item['name']) ?></td>
                        <td><?= htmlspecialchars($item['size']) ?></td>
                        <td><?= htmlspecialchars($item['quantity']) ?></td>
                        <td>
                            <button class="primary" onclick="openRestoreModal(<?= (int)$item['id'] ?>)">Restore</button>
                            <button class="error" onclick="openDeleteModal(<?= (int)$item['id'] ?>)">Delete</button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="5" class="no-data">No archived items found.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

    <div class="overlay" id="overlay"></div>

    <div class="form-popup" id="restoreModal">
        <h3>Restore Item</h3>
        <p class="form-txt">Are you sure you want to restore this item?</p>
        <div class="btn-group">
            <button class="primary" id="confirmRestoreBtn">Yes, Restore</button>
            <button class="error" onclick="closeRestoreModal()">Cancel</button>
        </div>
    </div>

    <div class="form-popup" id="deleteModal">
        <h3>Delete Item</h3>
        <p class="form-txt">Are you sure you want to permanently delete this item?</p>
        <div class="btn-group">
            <button class="error" id="confirmDeleteBtn">Yes, Delete</button>
            <button class="secondary" onclick="closeDeleteModal()">Cancel</button>
        </div>
    </div>

    <script>
        const overlay = document.getElementById('overlay');
        let currentItemId = null;

        function openRestoreModal(id) {
            currentItemId = id;
            overlay.style.display = 'block';
            document.getElementById('restoreModal').style.display = 'block';
            document.getElementById('confirmRestoreBtn').onclick = confirmRestore;
        }

        function closeRestoreModal() {
            document.getElementById('restoreModal').style.display = 'none';
            overlay.style.display = 'none';
            currentItemId = null;
        }

        function confirmRestore() {
            const formData = new FormData();
            formData.append('id', currentItemId);
            fetch('restore.php', {
                    method: 'POST',
                    body: formData
                })
                .then(res => res.json())
                .then(() => {
                    closeRestoreModal();
                    window.location.reload();
                });
        }

        function openDeleteModal(id) {
            currentItemId = id;
            overlay.style.display = 'block';
            document.getElementById('deleteModal').style.display = 'block';
            document.getElementById('confirmDeleteBtn').onclick = confirmDelete;
        }

        function closeDeleteModal() {
            document.getElementById('deleteModal').style.display = 'none';
            overlay.style.display = 'none';
            currentItemId = null;
        }

        function confirmDelete() {
            const formData = new FormData();
            formData.append('id', currentItemId);
            fetch('delete.php', {
                    method: 'POST',
                    body: formData
                })
                .then(res => res.json())
                .then(() => {
                    closeDeleteModal();
                    window.location.reload();
                });
        }
    </script>
</body>

</html>