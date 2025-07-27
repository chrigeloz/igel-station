<?php
require_once 'db.php';

$error = '';
$formData = [];

$fieldMap = [
    'surname'  => 'Surname',
    'name'     => 'Name',
    'phone'    => 'Phone',
    'email'    => 'Email',
    'street'   => 'Street',
    'postcode' => 'Postcode',
    'suburb'   => 'Suburb',
    'notes'    => 'Notes'
];

// Initialize formData with empty values
foreach ($fieldMap as $field => $label) {
    $formData[$field] = '';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Populate formData from submitted form
    foreach ($fieldMap as $field => $label) {
        $formData[$field] = $_POST[$field] ?? '';
    }

    if (!empty($formData['name'])) {
        try {
            $stmt = $pdo->prepare("
                INSERT INTO finders (surname, name, phone, email, street, postcode, suburb, notes)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)
            ");
            $stmt->execute([
                $formData['surname'],
                $formData['name'],
                $formData['phone'],
                $formData['email'],
                $formData['street'],
                $formData['postcode'],
                $formData['suburb'],
                $formData['notes']
            ]);
            header('Location: index.php');
            exit;
        } catch (PDOException $e) {
            $error = "Failed to add finder. Please try again.";
            error_log("Add finder error: " . $e->getMessage());
        }
    } else {
        $error = "Name is required";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Add Finder</title>
    <link rel="stylesheet" href="styles.css" />
</head>
<body>
<h2>Add Finder</h2>

<?php if ($error): ?>
    <p style="color:red;"><?= htmlspecialchars($error) ?></p>
<?php endif; ?>

<form method="POST" action="">
    <?php foreach ($fieldMap as $field => $label): ?>
        <?php if ($field === 'notes'): ?>
            <label><?= $label ?>:<br>
                <textarea name="<?= $field ?>"><?= htmlspecialchars($formData[$field] ?? '') ?></textarea>
            </label><br>
        <?php else: ?>
            <label><?= $label ?>:
                <input type="<?= $field === 'email' ? 'email' : 'text' ?>" 
                       name="<?= $field ?>" 
                       value="<?= htmlspecialchars($formData[$field] ?? '') ?>"
                       <?= $field === 'name' ? 'required' : '' ?>>
            </label><br>
        <?php endif; ?>
    <?php endforeach; ?>
    <button type="submit">Add Finder</button>
</form>

<a href="index.php">Back to list</a>
</body>
</html>
