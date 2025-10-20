<?php
require __DIR__.'/auth.php';
require_role('admin');

if (isset($_GET['id'])) {
    $id = (int) $_GET['id'];

    // Delete this user's tasks first
    $stmt = $mysqli->prepare("DELETE FROM tasks WHERE user_id=?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();

    // Delete the user
    $stmt = $mysqli->prepare("DELETE FROM users WHERE id=?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();

    flash('success', 'User and all their tasks deleted.');
}
header("Location: admin.php");
exit;
