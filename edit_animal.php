<?php
require_once 'db.php';

$id = $_GET['id'] ?? null;
$error = '';
$success = '';

$fieldMap = [
    'name'   => 'Name',
    'species'   => 'Species',
    'age'       => 'Age',
    'gender'    => 'Gender',
    'condition' => 'Condition'
];

$finders = $pdo->query("SELECT id, surname, name FROM finders ORDER BY surname, name")->fetchAll(PDO::FETCH_ASSOC);

if (!$id) {
    die('No animal ID provided.');
}

// Fetch current animal data
$stmt = $pdo->prepare("SELECT * FROM animals WHERE id = ?");
$stmt->execute([$id]);
$animal = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$animal) {
    die("Animal not found.");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $updates = [];
    $values = [];

    $animal['finder_id'] = $_POST['finder_id'] ?? '';
    $updates[] = "finder_id = ?";
    $values[] = $animal['finder_id'];

    foreach ($fieldMap as $field => $label) {
        $animal[$field] = trim($_POST[$field] ?? '');
        $updates[] = "$field = ?";
        $values[] = $animal[$field];
    }

    $values[] = $id;

    try {
        $stmt = $pdo->prepare("UPDATE animals SET " . implode(', ', $updates) . " WHERE id = ?");
        $stmt->execute($values);
        $success = "Animal updated successfully.";
    } catch (PDOException $e) {
        $error = "Failed to update animal.";
        error_log("Edit animal error: " . $e->getMessage());
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Animal</title>
    <link rel="stylesheet" href="styles.css" />
</head>
<body>
<h2>Edit Animal</h2>

<?php if ($error): ?>
    <p style="color:red;"><?= htmlspecialchars($error) ?></p>
<?php elseif ($success): ?>
    <p style="color:green;"><?= htmlspecialchars($success) ?></p>
<?php endif; ?>

<form method="POST">
    <label>Finder*:
        <select name="finder_id" required>
            <option value="">Select Finder</option>
            <?php foreach ($finders as $finder): ?>
                <option value="<?= htmlspecialchars($finder['id']) ?>" <?= $animal['finder_id'] == $finder['id'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($finder['surname'] . ', ' . $finder['name']) ?>
                </option>
            <?php endforeach; ?>
        </select>
    </label><br>

    <?php foreach ($fieldMap as $field => $label): ?>
        <?php if ($field === 'condition'): ?>
            <label><?= $label ?>:<br>
                <textarea name="<?= $field ?>"><?= htmlspecialchars($animal[$field] ?? '') ?></textarea>
            </label><br>
        <?php elseif ($field === 'gender'): ?>
            <label><?= $label ?>:
                <select name="gender">
                    <option value="Unknown" <?= $animal['gender'] === 'Unknown' ? 'selected' : '' ?>>Unknown</option>
                    <option value="Male" <?= $animal['gender'] === 'Male' ? 'selected' : '' ?>>Male</option>
                    <option value="Female" <?= $animal['gender'] === 'Female' ? 'selected' : '' ?>>Female</option>
                </select>
            </label><br>
        <?php else: ?>
            <label><?= $label ?>:
                <input type="text" name="<?= $field ?>" value="<?= htmlspecialchars($animal[$field] ?? '') ?>">
            </label><br>
        <?php endif; ?>
    <?php endforeach; ?>

    <button type="submit">Update Animal</button>
</form>

<a href="index.php">Back to list</a>
</body>
</html>
