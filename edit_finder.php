<?php
require_once 'db.php';  // Include database connection file

include 'fieldmap_finder.php';

$id = $_GET['id'] ?? null;  // Get the 'id' parameter from URL query string, or null if not set
$error = '';    // Initialize variable to hold error messages
$success = '';  // Initialize variable to hold success messages

// Map of database fields to their corresponding human-readable labels for the form
/*
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
*/

// If no ID provided in the URL, stop script execution and display error
if (!$id) {
    die('No finder ID provided.');
}

// Prepare and execute SQL statement to fetch the finder record by ID
$stmt = $pdo->prepare("SELECT * FROM finders WHERE id = ?");
$stmt->execute([$id]);
$finder = $stmt->fetch(PDO::FETCH_ASSOC);  // Fetch the record as an associative array

// If no finder found with the given ID, stop execution with error message
if (!$finder) {
    die("Finder not found.");
}

// Check if the form was submitted using POST method
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $updates = [];  // Array to hold the SQL update assignments (e.g. "name = ?")
    $values = [];   // Array to hold the values for prepared statement placeholders

    // Loop through each field defined in the fieldMap to prepare update statement and values
    foreach ($fieldMap as $field => $label) {
        $value = $_POST[$field] ?? '';    // Get submitted form value or default to empty string
        $updates[] = "$field = ?";        // Add to SQL update assignments array
        $values[] = $value;               // Add corresponding value for prepared statement
    }

    $values[] = $id;  // Add the ID value for the WHERE clause in the prepared statement

    try {
        // Prepare the UPDATE SQL query with the dynamically built assignments
        $stmt = $pdo->prepare("UPDATE finders SET " . implode(', ', $updates) . " WHERE id = ?");
        $stmt->execute($values);  // Execute the update with bound values
        $success = "Finder updated successfully.";  // Set success message
        // Update local $finder array with the submitted POST data to refresh form display
        $finder = array_merge($finder, $_POST);
    } catch (PDOException $e) {
        $error = "Failed to update finder.";  // Set error message for user
        error_log("Edit finder error: " . $e->getMessage());  // Log detailed error for debugging
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Finder</title>
    <link rel="stylesheet" href="styles.css" />  <!-- Link to external stylesheet -->
</head>
<body>
<h2>Edit Finder</h2>

<!-- Display error message if any -->
<?php if ($error): ?>
    <p style="color:red;"><?= htmlspecialchars($error) ?></p>
<!-- Otherwise display success message if set -->
<?php elseif ($success): ?>
    <p style="color:green;"><?= htmlspecialchars($success) ?></p>
<?php endif; ?>

<!-- Form for editing the finder data -->
<form method="POST">
    <!-- Loop through each field in the field map to generate form inputs -->
    <?php foreach ($fieldMap as $field => $label): ?>
        <?php if ($field === 'notes'): ?>
            <!-- Use a textarea for 'notes' field -->
            <label><?= $label ?>:<br>
                <textarea name="<?= $field ?>"><?= htmlspecialchars($finder[$field] ?? '') ?></textarea>
            </label><br>
        <?php else: ?>
            <!-- Use text input for all other fields -->
            <label><?= $label ?>: 
                <input type="text" name="<?= $field ?>" value="<?= htmlspecialchars($finder[$field] ?? '') ?>">
            </label><br>
        <?php endif; ?>
    <?php endforeach; ?>

    <!-- Submit button to update the finder -->
    <button type="submit">Update Finder</button>
</form>

<!-- Link back to main list page -->
<a href="index.php">Back to list</a>
</body>
</html>
