<?php 
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Retrieve Mailgun API settings from environment variables
$apiKey = getenv('a664c02a687bb1573dc7f7594fa89f55-0920befd-84420b27'); // Set this in your environment variables or hardcode it for testing
$domain = getenv('mailgun.com'); // For example, 'sandbox123.mailgun.org'
$yourEmail = getenv('solomontsebeje@gmail.com'); // Your email address to receive the copy

$teams = ['Hive Masters', 'Hive Kings', 'Hive Legends', 'Hive Dominators'];

// Function to generate a unique Arena ID
function generateArenaID($entrantsCount) {
    return "GHAE-" . str_pad($entrantsCount + 1, 5, '0', STR_PAD_LEFT); // Generates GHAE-00001, GHAE-00002, etc.
}

// Function to assign a team based on the number of entrants
function assignTeam($totalEntrants, $teams) {
    return $teams[$totalEntrants % count($teams)];
}

// Function to send confirmation email via Mailgun
function sendConfirmationEmail($email, $arenaID, $team) {
    global $apiKey, $domain, $yourEmail;

    $ch = curl_init();

    $data = [
        'from' => 'arena@g-hive.com',
        'to' => $email,
        'cc' => $yourEmail,  // Send a copy to your email
        'subject' => 'Welcome to the G-Hive Arena!',
        'text' => "Congratulations! You've successfully joined the G-Hive Arena.\n\nYour Arena ID: $arenaID\nYour Team: $team\n\nGet ready to compete and show your skills!"
    ];

    curl_setopt($ch, CURLOPT_URL, "https://api.mailgun.net/v3/$domain/messages");
    curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
    curl_setopt($ch, CURLOPT_USERPWD, "api:$apiKey");
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $response = curl_exec($ch);

    if (curl_errno($ch)) {
        echo json_encode(['status' => 'error', 'message' => 'CURL error: ' . curl_error($ch)]);
        exit;
    }

    $responseData = json_decode($response, true);

    if (isset($responseData['message']) && $responseData['message'] == 'Queued. Thank you.') {
        // Success
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Failed to send email: ' . $responseData['message']]);
        exit;
    }

    curl_close($ch);
}

// Function to get the total number of entrants (from a text file or database)
function getTotalEntrants($filePath) {
    if (file_exists($filePath)) {
        $data = file_get_contents($filePath);
        $lines = array_filter(explode("\n", trim($data))); // Filter out empty lines
        return count($lines); // Return the number of valid entries
    }
    return 0; // No data found
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

    // Validate that name and email are not empty
    if (!empty($name) && !empty($email)) {
        // Get total entrants count from a file
        $filePath = 'joiners.txt';
        $totalEntrants = getTotalEntrants($filePath);

        // Generate a new Arena ID
        $arenaID = generateArenaID($totalEntrants);

        // Assign team based on the total number of entrants
        $team = assignTeam($totalEntrants, $teams);

        // Send confirmation email to the user and yourself (admin)
        sendConfirmationEmail($email, $arenaID, $team);

        // Return success response in JSON format
        echo json_encode([
            'status' => 'success',
            'arenaID' => $arenaID,
            'team' => $team
        ]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Missing required fields.']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method.']);
}
?>
