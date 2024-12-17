<?php 
ini_set('display_errors', 1);
error_reporting(E_ALL);

$filePath = 'joiners.txt';

// Function to read the total number of entrants from the file
function getTotalEntrants($filePath) {
    if (file_exists($filePath)) {
        $data = file_get_contents($filePath);
        $lines = array_filter(explode("\n", trim($data))); // Filter out empty lines
        return count($lines); // Return the number of valid entries
    }
    return 0; // No data found
}

// Function to generate a unique Arena ID
function generateArenaID($filePath) {
    $totalEntrants = getTotalEntrants($filePath); // Get the total number of entrants
    return "GHAE-" . str_pad($totalEntrants + 1, 5, '0', STR_PAD_LEFT); // Generates GHAE-00001, GHAE-00002, etc.
}

// Function to check if the email already exists in the file
function isEmailAlreadyRegistered($filePath, $email) {
    if (file_exists($filePath)) {
        $data = file_get_contents($filePath);
        return strpos($data, $email) !== false; // Search for the email within the file data
    }
    return false;
}

// Function to save user data to the file
function saveUserData($filePath, $name, $email, $arenaID) {
    $userData = "Arena ID: $arenaID, Name: $name, Email: $email\n";
    
    // Open the file and lock it for writing
    $fp = fopen($filePath, 'a');
    if ($fp === false) {
        echo json_encode(['status' => 'error', 'message' => 'Unable to open the file.']);
        exit;
    }

    // Try to lock the file before writing
    if (flock($fp, LOCK_EX)) {
        fwrite($fp, $userData);
        flock($fp, LOCK_UN); // Release the lock
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Unable to lock the file.']);
        fclose($fp);
        exit;
    }

    fclose($fp);
}

// Handle POST request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize user inputs
    $name = htmlspecialchars($_POST['name'] ?? '', ENT_QUOTES, 'UTF-8');
    $email = filter_var($_POST['email'] ?? '', FILTER_SANITIZE_EMAIL);

    // Validate email format
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(['status' => 'error', 'message' => 'Invalid email format.']);
        exit;
    }

    // Check if email is already registered
    if (isEmailAlreadyRegistered($filePath, $email)) {
        echo json_encode(['status' => 'error', 'message' => 'Email is already registered.']);
        exit;
    }

    // Validate that name and email are not empty
    if (!empty($name) && !empty($email)) {
        // Generate a new Arena ID
        $arenaID = generateArenaID($filePath);

        // Save to text file
        saveUserData($filePath, $name, $email, $arenaID);

        // Update the total number of entrants
        $totalEntrants = getTotalEntrants($filePath);

        // Return success response in JSON format
        echo json_encode([
            'status' => 'success',
            'arenaID' => $arenaID,
            'totalEntrants' => $totalEntrants
        ]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Missing required fields.']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method.']);
}
?>
