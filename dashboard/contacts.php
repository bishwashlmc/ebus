<?php
include 'header.php';
include 'config.php';

$success = '';
$error = '';


if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $stmt = $pdo->prepare("DELETE FROM contact_messages WHERE id = ?");
    if ($stmt->execute([$id])) {
        $success = "Message deleted successfully.";
    } else {
        $error = "Failed to delete message.";
    }
}


$messages = $pdo->query("SELECT * FROM contact_messages ORDER BY id DESC")->fetchAll(PDO::FETCH_ASSOC);
?>

<h2>Contact Messages</h2>

<?php if ($success) echo "<p style='color: green;'>$success</p>"; ?>
<?php if ($error) echo "<p style='color: red;'>$error</p>"; ?>

<?php if (count($messages) > 0): ?>
<table border="1" cellpadding="10" cellspacing="0">
    <thead>
        <tr>
            <th>ID</th>
            <th>Sender</th>
            <th>Email</th>
            <th>Subject</th>
            <th>Message</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($messages as $msg): ?>
        <tr>
            <td><?= $msg['id'] ?></td>
            <td><?= htmlspecialchars($msg['name']) ?></td>
            <td><?= htmlspecialchars($msg['email']) ?></td>
            <td><?= htmlspecialchars($msg['subject']) ?></td>
            <td><?= nl2br(htmlspecialchars($msg['message'])) ?></td>
            <td>
                <a href="contacts.php?delete=<?= $msg['id'] ?>" onclick="return confirm('Delete this message?')">Delete</a>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>
<?php else: ?>
    <p>No contact messages found.</p>
<?php endif; ?>

