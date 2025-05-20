<?php
require("dbconnect.php");
session_start();

if (!isset($_SESSION['is_login']) || $_SESSION['is_login'] !== true) {
    header('Location: login.html');
    exit;
}

$items = [];
$sql = "SELECT * FROM items WHERE status = 0";
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
    <title>Binco</title>
</head>

<body>
    <h1 class="title">Binco's Apparel Inventory</h1>
    <div class="nav-btn">
        <div class="btn">
            <button class="primary" onclick="showCreateForm()">Add Item</button>
            <a class="secondary" href="archives.php">View Archives</a>
        </div>

        <a href="logout.php">Logout</a>
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
                            <button class="primary" onclick="showUpdateForm(<?= (int)$item['id'] ?>, `<?= htmlspecialchars($item['name'], ENT_QUOTES) ?>`, `<?= htmlspecialchars($item['size'], ENT_QUOTES) ?>`, <?= (int)$item['quantity'] ?>)">Edit</button>
                            <button class="secondary" onclick="openArchiveModal(<?= (int)$item['id'] ?>)">Archive</button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="5" style="text-align:center;">No items found.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

    <div class="overlay" id="overlay"></div>

    <div class="form-popup" id="itemFormPopup">
        <h3 id="formTitle">Add Item</h3>
        <input type="hidden" id="itemId">
        <input type="text" id="itemName" placeholder="Item Name">
        <input type="text" id="itemSize" placeholder="Item Size">
        <input type="number" id="itemQty" placeholder="Quantity">
        <div class="btn-group">
            <button class="primary" onclick="submitForm()" id="submitBtn">Save</button>
            <button class="error" onclick="closeForm()">Cancel</button>
        </div>
    </div>

    <div class="form-popup" id="archiveModal">
        <h3>Archive Item</h3>
        <p class="form-txt">Are you sure you want to archive this item?</p>
        <div class="btn-group">
            <button class="secondary" id="confirmArchiveBtn">Archive</button>
            <button class="error" onclick="closeArchiveModal()">Cancel</button>
        </div>
    </div>

    <script>
        const overlay = document.getElementById('overlay');
        let archiveItemId = null;

        function showCreateForm() {
            document.getElementById('formTitle').innerText = 'Add Item';
            document.getElementById('itemId').value = '';
            document.getElementById('itemName').value = '';
            document.getElementById('itemSize').value = '';
            document.getElementById('itemQty').value = '';
            overlay.style.display = 'block';
            document.getElementById('itemFormPopup').style.display = 'block';
        }

        function showUpdateForm(id, name, size, quantity) {
            document.getElementById('formTitle').innerText = 'Edit Item';
            document.getElementById('itemId').value = id;
            document.getElementById('itemName').value = name;
            document.getElementById('itemSize').value = size;
            document.getElementById('itemQty').value = quantity;
            overlay.style.display = 'block';
            document.getElementById('itemFormPopup').style.display = 'block';
        }

        function closeForm() {
            document.getElementById('itemFormPopup').style.display = 'none';
            overlay.style.display = 'none';
        }

        function submitForm() {
            const id = document.getElementById('itemId').value;
            const name = document.getElementById('itemName').value;
            const size = document.getElementById('itemSize').value;
            const quantity = document.getElementById('itemQty').value;
            const url = id ? 'update.php' : 'create.php';
            const formData = new FormData();
            if (id) formData.append('id', id);
            formData.append('name', name);
            formData.append('size', size);
            formData.append('quantity', quantity);
            fetch(url, {
                    method: 'POST',
                    body: formData
                })
                .then(res => res.json())
                .then(() => {
                    closeForm();
                    window.location.reload();
                });
        }

        function openArchiveModal(id) {
            archiveItemId = id;
            overlay.style.display = 'block';
            document.getElementById('archiveModal').style.display = 'block';
            document.getElementById('confirmArchiveBtn').onclick = confirmArchive;
        }

        function closeArchiveModal() {
            document.getElementById('archiveModal').style.display = 'none';
            overlay.style.display = 'none';
            archiveItemId = null;
        }

        function confirmArchive() {
            const formData = new FormData();
            formData.append('id', archiveItemId);
            fetch('archive.php', {
                    method: 'POST',
                    body: formData
                })
                .then(res => res.json())
                .then(() => {
                    closeArchiveModal();
                    window.location.reload();
                });
        }
    </script>
</body>

</html>