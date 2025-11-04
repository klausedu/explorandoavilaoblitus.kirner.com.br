<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');

// Step 1: Check if config file exists
if (!file_exists(__DIR__ . '/../config.php')) {
    sendResponse(false, null, 'Diagnostic: config.php not found.', 500);
}
require_once '../config.php';

// Step 2: Attempt to get DB connection
$pdo = null;
try {
    $pdo = getDBConnection();
} catch (Exception $e) {
    sendResponse(false, null, 'Diagnostic: getDBConnection() failed. Error: ' . $e->getMessage(), 500);
}

if ($pdo === null) {
    sendResponse(false, null, 'Diagnostic: PDO connection is null after getDBConnection().', 500);
}

// Step 3: Check 'locations' table
try {
    $stmt = $pdo->query("SELECT id FROM locations LIMIT 1");
    $stmt->fetch();
} catch (PDOException $e) {
    sendResponse(false, null, 'Diagnostic: Failed to query \'locations\' table. Error: ' . $e->getMessage(), 500);
}

// Step 4: Check 'hotspots' table
try {
    $stmt = $pdo->query("SELECT id FROM hotspots LIMIT 1");
    $stmt->fetch();
} catch (PDOException $e) {
    sendResponse(false, null, 'Diagnostic: Failed to query \'hotspots\' table. Error: ' . $e->getMessage(), 500);
}

// Step 5: Check 'connections' table
try {
    $stmt = $pdo->query("SELECT id FROM connections LIMIT 1");
    $stmt->fetch();
} catch (PDOException $e) {
    sendResponse(false, null, 'Diagnostic: Failed to query \'connections\' table. Error: ' . $e->getMessage(), 500);
}

// If all checks pass, run the original code
try {
    // Get all locations
    $stmt = $pdo->query("
        SELECT
            id,
            name,
            description,
            background_image,
            created_at,
            updated_at
        FROM locations
        ORDER BY name ASC
    ");

    $locations = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // For each location, get its hotspots
    foreach ($locations as &$location) {
        $hotspotStmt = $pdo->prepare("
            SELECT
                id,
                type,
                x,
                y,
                width,
                height,
                label,
                description,
                target_location,
                item_id,
                interaction_data
            FROM hotspots
            WHERE location_id = ?
            ORDER BY type, id
        ");
        $hotspotStmt->execute([$location['id']]);
        $location['hotspots'] = $hotspotStmt->fetchAll(PDO::FETCH_ASSOC);

        // Convert numeric strings to numbers
        foreach ($location['hotspots'] as &$hotspot) {
            $hotspot['x'] = (float) $hotspot['x'];
            $hotspot['y'] = (float) $hotspot['y'];
            $hotspot['width'] = (float) $hotspot['width'];
            $hotspot['height'] = (float) $hotspot['height'];

            // Parse interaction_data if present
            if ($hotspot['interaction_data']) {
                $hotspot['interaction_data'] = json_decode($hotspot['interaction_data'], true);
            }
        }
    }

    // Get all connections
    $connStmt = $pdo->query("
        SELECT from_location, to_location
        FROM connections
    ");
    $connections = $connStmt->fetchAll(PDO::FETCH_ASSOC);

    // Success response
    sendResponse(true, [
        'locations' => $locations,
        'connections' => $connections,
        'count' => count($locations)
    ], 'Locations loaded successfully');

} catch (PDOException $e) {
    sendResponse(false, null, 'Diagnostic: Error during final data retrieval. Error: ' . $e->getMessage(), 500);
}
