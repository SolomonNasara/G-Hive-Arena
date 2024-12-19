<?php
$filePath = 'joiners.json';

// Function to read the total number of entrants from the file
function getTotalEntrants($filePath) {
    if (file_exists($filePath)) {
        $data = file_get_contents($filePath);
        $lines = explode("\n", trim($data)); // Each line is a user entry
        return count($lines); // Return the number of entries
    }
    return 0; // No data found
}

// Return the total number of entrants as a JSON response
echo json_encode(['totalEntrants' => getTotalEntrants($filePath)]);
?>
