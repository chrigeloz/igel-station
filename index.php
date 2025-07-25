<?php
require_once 'db.php';

$sql = "
    SELECT 
        animals.id, animals.species, animals.age, animals.gender, animals.condition, animals.created_at,
        finders.id AS finder_id, finders.name AS finder_name, finders.phone,
        (SELECT COUNT(*) FROM events WHERE animal_id = animals.id) AS event_count
    FROM animals
    LEFT JOIN finders ON animals.finder_id = finders.id
    ORDER BY animals.created_at DESC
";

try {
    $stmt = $pdo->query($sql);
    $animals = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log('DB Query error: ' . $e->getMessage());
    die('Failed to load animals.');
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Wildlife Rescue Cases</title>
    <link rel="stylesheet" href="styles.css" />
</head>
<body>
<h1>Wildlife Rescue Cases</h1>

<nav>
    <a href="add_finder.php">Add Finder</a> | 
    <a href="add_animal.php">Add Animal</a> | 
    <a href="add_event.php">Add Event</a>
</nav>

<?php if (count($animals) === 0): ?>
    <p>No animals recorded yet.</p>
<?php else: ?>
    <table>
        <thead>
            <tr>
                <th>Animal</th>
                <th>Finder</th>
                <th>Age</th>
                <th>Gender</th>
                <th>Condition</th>
                <th>Events</th>
                <th>Added On</th>
                
            </tr>
        </thead>
        <tbody>
            <?php foreach ($animals as $animal): ?>
                <tr>
                    <td><?= htmlspecialchars($animal['species']) ?><br><small>
                        <a href="edit_animal.php?id=<?= urlencode($animal['id']) ?>">edit</a></small>
                    </small></td>
                    <td>
                        <?= htmlspecialchars($animal['finder_name'] ?? 'Unknown') ?><br>
                        <small><?= htmlspecialchars($animal['phone'] ?? '-') ?><br>
                        <a href="edit_finder.php?id=<?= urlencode($animal['finder_id']) ?>">edit</a></small>
                    </td>
                    <td><?= htmlspecialchars($animal['age']) ?></td>
                    <td><?= htmlspecialchars($animal['gender']) ?></td>
                    <td><?= nl2br(htmlspecialchars($animal['condition'])) ?></td>
                    <td><?= $animal['event_count'] ?></td>
                    <td><?= htmlspecialchars($animal['created_at']) ?></td>
                    
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>

</body>
</html>
