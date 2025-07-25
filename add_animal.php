<?php
require_once 'db.php';

$finders = $pdo->query("SELECT id, surname, name FROM finders ORDER BY surname")->fetchAll(PDO::FETCH_ASSOC);
$error = '';

$fieldMap = [
    'finder_id' => 'Finder*',
    'species'   => 'Species*',
    'age'       => 'Age',
    'gender'    => 'Gender',
    'condition' => 'Condition',
];

// Default values
$formData = array_fill_keys(array_keys($fieldMap), '');
$formData['gender'] = 'Unknown';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    foreach ($formData as $field => $_) {
        $formData[$field] = trim($_POST[$field] ?? ($field === 'gender' ? 'Unknown' : ''));
    }

    // Basic required validation
    if ($formData['finder_id'] && $formData['species']) {
        try {
            $placeholders = implode(', ', array_fill(0, count($fieldMap), '?'));
            $columns = implode(', ', array_keys($fieldMap));
            $stmt = $pdo->prepare("INSERT INTO animals ($columns) VALUES ($placeholders)");
            $stmt->execute(array_values($formData));

            header('Location: index.php');
            exit;
        } catch (PDOException $e) {
            $error = "Failed to add animal. Please try again.";
            error_log("Add animal error: " . $e->getMessage());
        }
    } else {
        $error = "Finder and Species are required.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Add Animal</title>
    <link rel="stylesheet" href="styles.css" />
</head>
<body>
<h2>Add Animal</h2>

<?php if ($error): ?>
    <p style="color:red;"><?= htmlspecialchars($error) ?></p>
<?php endif; ?>

<form method="POST">
    <?php foreach ($fieldMap as $field => $label): ?>
        <?php if ($field === 'finder_id'): ?>
            <label><?= $label ?>:
                <select name="finder_id" required>
                    <option value="">Select Finder</option>
                    <?php foreach ($finders as $finder): ?>
                        <option value="<?= htmlspecialchars($finder['id']) ?>" <?= $formData['finder_id'] == $finder['id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($finder['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </label><br>

        <?php elseif ($field === 'gender'): ?>
            <label><?= $label ?>:
                <select name="gender">
                    <?php foreach (['Unknown', 'Male', 'Female'] as $option): ?>
                        <option value="<?= $option ?>" <?= $formData['gender'] === $option ? 'selected' : '' ?>>
                            <?= $option ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </label><br>

        <?php elseif ($field === 'condition'): ?>
            <label><?= $label ?>:<br>
                <textarea name="condition"><?= htmlspecialchars($formData['condition']) ?></textarea>
            </label><br>

        <?php else: ?>
            <label><?= $label ?>:
                <input type="text" name="<?= $field ?>" value="<?= htmlspecialchars($formData[$field]) ?>" <?= str_contains($label, '*') ? 'required' : '' ?>>
            </label><br>
        <?php endif; ?>
    <?php endforeach; ?>

    <button type="submit">Add Animal</button>
</form>

<a href="index.php">Back to list</a>
</body>
</html>
