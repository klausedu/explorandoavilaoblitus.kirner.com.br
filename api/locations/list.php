<?php
/**
 * API: List all locations with their hotspots
 * Returns complete game map data
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');

require_once '../config.php';

// Get database connection
$pdo = getDBConnection();

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
    sendResponse(false, null, 'Database error: ' . $e->getMessage(), 500);
}

function sendResponse($success, $data = null, $message = '', $httpCode = 200) {
    http_response_code($httpCode);
    echo json_encode([
        'success' => $success,
        'data' => $data,
        'message' => $message,
        'timestamp' => time()
    ], JSON_UNESCAPED_UNICODE);
    exit;
}