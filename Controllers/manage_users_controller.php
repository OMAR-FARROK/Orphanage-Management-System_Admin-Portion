<?php
// Variable to store status message
$statusMessage = '';

function getUserById($conn, $user_id) {
    $stmt = $conn->prepare("SELECT user_id, username, email, role FROM Users WHERE user_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $user_id = $username = $email = $role = null;
    $stmt->bind_result($user_id, $username, $email, $role);
    $stmt->fetch();
    $stmt->close();
    return ['user_id' => $user_id, 'username' => $username, 'email' => $email, 'role' => $role];
}

// Handling form submissions for adding, editing, and deleting users
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['add_user'])) {
        $username = $_POST['username'];
        $email = $_POST['email'];
        $password = $_POST['password'];
        $role = $_POST['role'];

        $stmt = $conn->prepare("INSERT INTO Users (username, email, password_hash, role) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $username, $email, $password, $role);
        if ($stmt->execute()) {
            $statusMessage = 'User added successfully';
        }
        $stmt->close();
    } elseif (isset($_POST['delete_user'])) {
        $user_id = $_POST['user_id'];

        $stmt = $conn->prepare("DELETE FROM Users WHERE user_id = ?");
        $stmt->bind_param("i", $user_id);
        if ($stmt->execute()) {
            $statusMessage = 'User deleted successfully';
        }
        $stmt->close();
    } elseif (isset($_POST['edit_user'])) {
        $user_id = $_POST['user_id'];
        $username = $_POST['username'];
        $email = $_POST['email'];
        $role = $_POST['role'];

        $stmt = $conn->prepare("UPDATE Users SET username = ?, email = ?, role = ? WHERE user_id = ?");
        $stmt->bind_param("sssi", $username, $email, $role, $user_id);
        if ($stmt->execute()) {
            $statusMessage = 'User updated successfully';
        }
        $stmt->close();
    }
}

// Fetching all users from the database
$result = $conn->query("SELECT user_id, username, email, role FROM Users");

$editUser = null;
if (isset($_GET['edit_user_id'])) {
    $editUser = getUserById($conn, $_GET['edit_user_id']);
}

?>
