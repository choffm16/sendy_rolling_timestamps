<?php

//Create cron job, like you did for your other Sendy Crons: 
// This is the schedule I use: 0 1 * * * 
// This is the command: php /path/to/rolling_timestamps.php > /dev/null 2>&1

// Include the config file to access database credentials
require_once '/path/to/your/sendy/includes/config.php';

// Use the variables from config.php to create the database connection
$conn = new mysqli($dbHost, $dbUser, $dbPass, $dbName, $dbPort);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Step 1: Query the "seg" table to get IDs of records where "name" contains "ROLLING"
$sqlSelectSeg = "SELECT id FROM seg WHERE name LIKE '%ROLLING%'";
$segIdsResult = $conn->query($sqlSelectSeg);

if ($segIdsResult->num_rows > 0) {
    $segIds = [];
    while($segRow = $segIdsResult->fetch_assoc()) {
        $segIds[] = $segRow['id'];
    }
    // Convert the IDs array to a string for the SQL query
    $segIdsStr = implode(',', $segIds);

    // Step 2: Modify the "seg_cons" query to only select records related to the fetched "seg" IDs, and make sure we just operate on timestamp fields
    $sqlSelect = "SELECT id, val FROM seg_cons WHERE seg_id IN ($segIdsStr) AND field = 'timestamp'";

    $result = $conn->query($sqlSelect);

    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            $segmentId = $row["id"];
            $val = $row["val"];

            if (strpos($val, ' AND ') !== false) {
                // "BETWEEN" segments will include the string " AND " in them.
                list($startTimestamp, $endTimestamp) = explode(' AND ', $val);
                $newStartTimestamp = $startTimestamp + 86400; // Add one day to the start
                $newEndTimestamp = $endTimestamp + 86400; // Add one day to the end
                $newValue = "$newStartTimestamp AND $newEndTimestamp";
            } else {
                // Otherwise, it's a single date, increment by one day
                $newTimestamp = $val + 86400; // Add one day
                $newValue = $newTimestamp;
            }

            // Prepare SQL statement to update the segment condition with new timestamps
            $sqlUpdate = "UPDATE seg_cons SET val = '$newValue' WHERE id = $segmentId";

            if ($conn->query($sqlUpdate) === TRUE) {
                echo "Segment $segmentId updated successfully\n";
            } else {
                echo "Error updating segment $segmentId: " . $conn->error . "\n";
            }
        }
    } else {
        echo "No segments found to update.\n";
    }
} else {
    echo "No 'ROLLING' segments found.\n";
}

$conn->close();
?>
