<?php 
ini_set('display_errors', 1);
error_reporting(E_ALL);

$filePath = '/tmp/joiners.json';

// Function to read the total number of entrants from the JSON file
function getTotalEntrants($filePath) {
    if (file_exists($filePath)) {
        $data = file_get_contents($filePath);
        $jsonData = json_decode($data, true);  // Decode the JSON data to an associative array
        return count($jsonData); // Return the number of valid entries
    }
    return 0; // No data found
}

// Function to generate a unique Arena ID
function generateArenaID($filePath) {
    $totalEntrants = getTotalEntrants($filePath); // Get the total number of entrants
    return "GHAE-" . str_pad($totalEntrants + 1, 5, '0', STR_PAD_LEFT); // Generates GHAE-00001, GHAE-00002, etc.
}

// Function to check if the email already exists in the JSON file
function isEmailAlreadyRegistered($filePath, $email) {
    if (file_exists($filePath)) {
        $data = file_get_contents($filePath);
        $jsonData = json_decode($data, true); // Decode the JSON data
        foreach ($jsonData as $entry) {
            if ($entry['email'] === $email) {
                return true; // Email is already registered
            }
        }
    }
    return false;
}

// Function to save user data to the JSON file
function saveUserData($filePath, $name, $email, $arenaID) {
    $userData = [
        'arenaID' => $arenaID,
        'name' => $name,
        'email' => $email
    ];
    
    $jsonData = [];
    
    // Read existing data from JSON file if it exists
    if (file_exists($filePath)) {
        $data = file_get_contents($filePath);
        $jsonData = json_decode($data, true);  // Decode existing data to associative array
    }

    // Append the new user data
    $jsonData[] = $userData;

    // Save the updated data back to the JSON file
    file_put_contents($filePath, json_encode($jsonData, JSON_PRETTY_PRINT));
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

        // Save to JSON file
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
