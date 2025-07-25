<?php
require_once 'db.php';

$finders = $pdo->query("SELECT id, name FROM finders ORDER BY name")->fetchAll(PDO::FETCH_ASSOC);

$error = '';
$finder_id = '';
$species = '';
$age = '';
$gender = 'Unknown';
$condition = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $finder_id = $_POST['finder_id'] ?? '';
    $species = trim($_POST['species'] ?? '');
    $age = trim($_POST['age'] ?? '');
    $gender = $_POST['gender'] ?? 'Unknown';
    $condition = trim($_POST['condition'] ?? '');

    if ($finder_id && $species) {
        try {
            $stmt = $pdo->prepare("INSERT INTO animals (finder_id, species, age, gender, `condition`) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$finder_id, $species, $age, $gender, $condition]);
            header('Location: index.php');
            exit;
        } catch (PDOException $e) {
            $error = "Failed to add animal. Please try again.";
            error_log('Add animal error: ' . $e->getMessage());
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

<form method="POST" action="">
    <label>Finder*:
        <select name="finder_id" required>
            <option value="">Select Finder</option>
            <?php foreach ($finders as $finder): ?>
                <option value="<?= htmlspecialchars($finder['id']) ?>" <?= $finder['id'] == $finder_id ? 'selected' : '' ?>>
                    <?= htmlspecialchars($finder['name']) ?>
                </option>
            <?php endforeach; ?>
        </select>
    </label><br>

    <label>Species*: <input type="text" name="species" required value="<?= htmlspecialchars($species) ?>"></label><br>
    <label>Age: <input type="text" name="age" value="<?= htmlspecialchars($age) ?>"></label><br>

    <label>Gender:
        <select name="gender">
            <option value="Unknown" <?= $gender === 'Unknown' ? 'selected' : '' ?>>Unknown</option>
            <option value="Male" <?= $gender === 'Male' ? 'selected' : '' ?>>Male</option>
            <option value="Female" <?= $gender === 'Female' ? 'selected' : '' ?>>Female</option>
        </select>
    </label><br>

    <label>Condition:<br><textarea name="condition"><?= htmlspecialchars($condition) ?></textarea></label><br>

    <button type="submit">Add Animal</button>
</form>

<a href="index.php">Back to list</a>
</body>
</html>
