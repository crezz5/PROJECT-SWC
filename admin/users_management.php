<?php
require_once '../includes/config.php';
$page_title = "Manage Users";
require_once '../admin/admin_header.php';

$formData = ['id' => '', 'username' => '', 'email' => ''];
$isEditing = false;
$isAdding = false;

// Handle user actions
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['delete_user'])) {
        $user_id = $_POST['user_id'];
        $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $_SESSION['message'] = "User deleted successfully!";
    } elseif (isset($_POST['edit_user'])) {
        $user_id = $_POST['user_id'];
        $result = $conn->query("SELECT id, username, email FROM users WHERE id = $user_id");
        if ($result->num_rows > 0) {
            $formData = $result->fetch_assoc();
            $isEditing = true;
        }
    } elseif (isset($_POST['save_user'])) {
        $user_id = $_POST['user_id'];
        $username = $_POST['username'];
        $email = $_POST['email'];

        $stmt = $conn->prepare("UPDATE users SET username=?, email=? WHERE id=?");
        $stmt->bind_param("ssi", $username, $email, $user_id);
        $stmt->execute();
        $_SESSION['message'] = "User updated successfully!";
    } elseif (isset($_POST['add_user'])) {
        $username = $_POST['username'];
        $email = $_POST['email'];
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

        $stmt = $conn->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $username, $email, $password);
        $stmt->execute();
        $_SESSION['message'] = "New user added successfully!";
    }
}

// Fetch all users
$users = $conn->query("SELECT id, username, email FROM users");
?>

<div class="card">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3>All Users</h3>
        <button class="btn btn-success" onclick="showAddModal()"><i class="fas fa-plus"></i> Add New User</button>
    </div>

    <?php if (isset($_SESSION['message'])): ?>
        <div class="alert alert-success"><?= $_SESSION['message'] ?></div>
        <?php unset($_SESSION['message']); ?>
    <?php endif; ?>

    <table class="table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Username</th>
                <th>Email</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php while($user = $users->fetch_assoc()): ?>
            <tr>
                <td><?= htmlspecialchars($user['id']) ?></td>
                <td><?= htmlspecialchars($user['username']) ?></td>
                <td><?= htmlspecialchars($user['email']) ?></td>
                <td>
                    <form method="POST" class="d-inline">
                        <input type="hidden" name="user_id" value="<?= $user['id'] ?>">
                        <button type="submit" name="edit_user" class="btn btn-primary btn-sm">
                            <i class="fas fa-edit"></i> Edit
                        </button>
                    </form>
                    <?php if ($user['id'] != $_SESSION['admin_id']): ?>
                    <form method="POST" class="d-inline">
                        <input type="hidden" name="user_id" value="<?= $user['id'] ?>">
                        <button type="submit" name="delete_user" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure?')">
                            <i class="fas fa-trash"></i> Delete
                        </button>
                    </form>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

<!-- Edit User Modal -->
<div id="userModal" class="modal" style="<?= $isEditing ? 'display:block;' : 'display:none;' ?>">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST">
                <div class="modal-header">
                    <h5 class="modal-title">Edit User</h5>
                    <button type="button" class="btn-close" onclick="closeModal()"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="user_id" value="<?= $formData['id'] ?>">
                    <div class="mb-3">
                        <label>Username</label>
                        <input type="text" name="username" class="form-control" value="<?= htmlspecialchars($formData['username']) ?>" required>
                    </div>
                    <div class="mb-3">
                        <label>Email</label>
                        <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($formData['email']) ?>" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" onclick="closeModal()" class="btn btn-secondary">Cancel</button>
                    <button type="submit" name="save_user" class="btn btn-primary">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Add New User Modal -->
<div id="addUserModal" class="modal" style="display:none;">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST">
                <div class="modal-header">
                    <h5 class="modal-title">Add New User</h5>
                    <button type="button" class="btn-close" onclick="closeAddModal()"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label>Username</label>
                        <input type="text" name="username" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label>Email</label>
                        <input type="email" name="email" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label>Password</label>
                        <input type="password" name="password" class="form-control" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" onclick="closeAddModal()" class="btn btn-secondary">Cancel</button>
                    <button type="submit" name="add_user" class="btn btn-success">Add User</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function closeModal() {
    document.getElementById('userModal').style.display = 'none';
}
function closeAddModal() {
    document.getElementById('addUserModal').style.display = 'none';
}
function showAddModal() {
    document.getElementById('addUserModal').style.display = 'block';
}

window.onclick = function(event) {
    if (event.target == document.getElementById('userModal')) closeModal();
    if (event.target == document.getElementById('addUserModal')) closeAddModal();
}
</script>

<?php $conn->close(); ?>
