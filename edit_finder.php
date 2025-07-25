<?php
require_once 'db.php';

$id = $_GET['id'] ?? null;
$error = '';
$success = '';

$fieldMap = [
    'surname'  => 'Surname',
    'name'     => 'Name',
    'phone'    => 'Phone',
    'email'    => 'Email',
    'street'   => 'Street',
    'postcode' => 'Postcode',
    'suburb'  => 'suburb',
    'notes' => 'notes'
];

if (!$id) {
    die('No finder ID provided.');
}

// Fetch current finder data
$stmt = $pdo->prepare("SELECT * FROM finders WHERE id = ?");
$stmt->execute([$id]);
$finder = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$finder) {
    die("Finder not found.");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $updates = [];
    $values = [];

    foreach ($fieldMap as $field => $label) {
        $value = $_POST[$field] ?? '';
        $updates[] = "$field = ?";
        $values[] = $value;
    }

    $values[] = $id; // for WHERE clause

    try {
        $stmt = $pdo->prepare("UPDATE finders SET " . implode(', ', $updates) . " WHERE id = ?");
        $stmt->execute($values);
        $success = "Finder updated successfully.";
        // Refresh data
        $finder = array_merge($finder, $_POST);
    } catch (PDOException $e) {
        $error = "Failed to update finder.";
        error_log("Edit finder error: " . $e->getMessage());
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Finder</title>
    <link rel="stylesheet" href="styles.css" />
</head>
<body>
<h2>Edit Finder</h2>

<?php if ($error): ?>
    <p style="color:red;"><?= htmlspecialchars($error) ?></p>
<?php elseif ($success): ?>
    <p style="color:green;"><?= htmlspecialchars($success) ?></p>
<?php endif; ?>

<form method="POST">
    <?php foreach ($fieldMap as $field => $label): ?>
        <?php if ($field === 'suburb'): ?>
            <label><?= $label ?>:<br>
                <textarea name="<?= $field ?>"><?= htmlspecialchars($finder[$field] ?? '') ?></textarea>
            </label><br>
        <?php else: ?>
            <label><?= $label ?>: 
                <input type="text" name="<?= $field ?>" value="<?= htmlspecialchars($finder[$field] ?? '') ?>">
            </label><br>
        <?php endif; ?>
    <?php endforeach; ?>

    <button type="submit">Update Finder</button>
</form>

<a href="index.php">Back to list</a>
</body>
</html>
