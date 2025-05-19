<?php
require("dbconnect.php");
session_start();

if (!isset($_SESSION['is_login']) || $_SESSION['is_login'] !== true) {
    header('Location: login.html');
    exit;
}

$items = [];
$sql = "SELECT * FROM items";
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
        <button class="primary" onclick="showCreateForm()">Add Item</button>
        <a href="logout.php">Logout</a>
    </div>

    <table id="itemsTable">
        <thead>
            <tr>
                <th>ID</th>
                <th>Product Name</th>
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
                        <td><?= htmlspecialchars($item['quantity']) ?></td>
                        <td>
                            <button class="primary" onclick="showUpdateForm(<?= (int)$item['id'] ?>, `<?= htmlspecialchars($item['name'], ENT_QUOTES) ?>`, <?= (int)$item['quantity'] ?>)">Edit</button>
                            <button class="secondary" onclick="openDeleteModal(<?= (int)$item['id'] ?>)">Delete</button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="4" style="text-align:center;">No items found.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

    <div class="overlay" id="overlay"></div>

    <div class="form-popup" id="itemFormPopup">
        <h3 id="formTitle">Add Item</h3>
        <input type="hidden" id="itemId">
        <input type="text" id="itemName" placeholder="Item Name">
        <input type="number" id="itemQty" placeholder="Quantity">
        <div class="btn-group">
            <button class="primary" onclick="submitForm()" id="submitBtn">Save</button>
            <button class="secondary" onclick="closeForm()">Cancel</button>
        </div>
    </div>

    <div class="form-popup" id="deleteModal">
        <h3 id="formTitle">Delete Item</h3>
        <p class="form-txt">Are you sure you want to delete this item?</p>
        <div class="btn-group">
            <button class="secondary" id="confirmDeleteBtn">Delete</button>
            <button class=" primary" onclick="closeDeleteModal()">Cancel</button>
        </div>
    </div>


    <script>
        const overlay = document.getElementById('overlay');

        let deleteItemId = null;

        // Function to toggle the modal for adding item
        function showCreateForm() {
            document.getElementById('formTitle').innerText = 'Add Item';
            document.getElementById('itemId').value = '';
            document.getElementById('itemName').value = '';
            document.getElementById('itemQty').value = '';
            overlay.style.display = 'block';
            document.getElementById('itemFormPopup').style.display = 'block';
        }

        // Function to toggle the modal for updating the item
        function showUpdateForm(id, name, quantity) {
            document.getElementById('formTitle').innerText = 'Edit Item';
            document.getElementById('itemId').value = id;
            document.getElementById('itemName').value = name;
            document.getElementById('itemQty').value = quantity;
            overlay.style.display = 'block';
            document.getElementById('itemFormPopup').style.display = 'block';
        }

        // Function for closing the modals
        function closeForm() {
            document.getElementById('itemFormPopup').style.display = 'none';
            overlay.style.display = 'none';
        }

        // Function for submiiting the form, if Id is provided, it will redirect to update.php, 
        // if not it will redirect to create.php
        function submitForm() {
            const id = document.getElementById('itemId').value;
            const name = document.getElementById('itemName').value;
            const quantity = document.getElementById('itemQty').value;
            const url = id ? 'update.php' : 'create.php';
            const formData = new FormData();
            if (id) formData.append('id', id);
            formData.append('name', name);
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

        // Function for opening the delete modal 
        function openDeleteModal(id) {
            deleteItemId = id;
            overlay.style.display = 'block';
            document.getElementById('deleteModal').style.display = 'block';
            document.getElementById('confirmDeleteBtn').onclick = confirmDelete;
        }

        // Function for closing the delete modal
        function closeDeleteModal() {
            document.getElementById('deleteModal').style.display = 'none';
            overlay.style.display = 'none';
            deleteItemId = null;
        }

        // Function for redirecting to delete.php
        function confirmDelete() {
            const formData = new FormData();
            formData.append('id', deleteItemId);
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