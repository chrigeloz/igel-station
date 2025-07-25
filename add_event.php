<?php
require_once 'db.php';

$animals = $pdo->query("
    SELECT animals.id, animals.species, finders.name AS finder_name
    FROM animals
    LEFT JOIN finders ON animals.finder_id = finders.id
    ORDER BY animals.id DESC
")->fetchAll(PDO::FETCH_ASSOC);

$error = '';
$animal_id = '';
$event_date = '';
$description = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $animal_id = $_POST['animal_id'] ?? '';
    $event_date = $_POST['event_date'] ?? '';
    $description = $_POST['description'] ?? '';

    if ($animal_id && $event_date) {
        try {
            $stmt = $pdo->prepare("INSERT INTO events (animal_id, event_date, description) VALUES (?, ?, ?)");
            $stmt->execute([$animal_id, $event_date, $description]);
            header('Location: index.php');
            exit;
        } catch (PDOException $e) {
            $error = "Failed to add event. Please try again.";
            error_log("Add event error: " . $e->getMessage());
        }
    } else {
        $error = "Animal and Event Date are required.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Add Event</title>
    <link rel="stylesheet" href="styles.css" />
</head>
<body>
<h2>Add Event</h2>

<?php if ($error): ?>
    <p style="color:red;"><?= htmlspecialchars($error) ?></p>
<?php endif; ?>

<form method="POST" action="">
    <label>Animal*:
        <select name="animal_id" required>
            <option value="">Select Animal</option>
            <?php foreach ($animals as $animal): ?>
                <option value="<?= htmlspecialchars($animal['id']) ?>" <?= $animal['id'] == $animal_id ? 'selected' : '' ?>>
                    <?= htmlspecialchars($animal['species']) ?> (Finder: <?= htmlspecialchars($animal['finder_name']) ?>)
                </option>
            <?php endforeach; ?>
        </select>
    </label><br>

    <label>Event Date*: <input type="date" name="event_date" required value="<?= htmlspecialchars($event_date) ?>"></label><br>

    <label>Description:<br><textarea name="description"><?= htmlspecialchars($description) ?></textarea></label><br>

    <button type="submit">Add Event</button>
</form>

<a href="index.php">Back to list</a>
</body>
</html>
