<?php
require_once 'db.php';

$surname = '';
$name = '';
$phone = '';
$email = '';
$street = '';
$postcode = '';
$address = '';
$notes = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $surname = $_POST['surname'] ?? '';
    $name = $_POST['name']      ?? '';  
    $phone = $_POST['phone'] ?? '';
    $email = $_POST['email'] ?? '';
    $street = $_POST['street'] ?? '';
    $postcode = $_POST['postcode'] ?? '';
    $suburb = $_POST['suburb'] ?? '';
    $address = $_POST['notes'] ?? '';

    if ($name) {
        try {
            $stmt = $pdo->prepare("INSERT INTO finders (surname, name, phone, email, street, postcode, suburb, notes) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$surname, $name, $phone, $email, $street, $postcode, $suburb, $notes]);
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
    <label>Surname*: <input type="text" name="surname" required value="<?= htmlspecialchars($surname) ?>"></label><br>
    <label>Name*: <input type="text" name="name" required value="<?= htmlspecialchars($name) ?>"></label><br>
    <label>Phone: <input type="text" name="phone" value="<?= htmlspecialchars($phone) ?>"></label><br>
    <label>Email: <input type="email" name="email" value="<?= htmlspecialchars($email) ?>"></label><br>
    <label>Street: <input type="street" name="street" value="<?= htmlspecialchars($street) ?>"></label><br>
    <label>PostCode: <input type="postcode" name="postcode" value="<?= htmlspecialchars($postcode) ?>"></label><br>
    <label>Suburb: <input type="suburb" name="suburb" value="<?= htmlspecialchars($suburb) ?>"></label><br>
    <label>Address:<br><textarea name="address"><?= htmlspecialchars($notes) ?></textarea></label><br>
    <button type="submit">Add Finder</button>
</form>

<a href="index.php">Back to list</a>
</body>
</html>
