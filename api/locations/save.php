<?php
/**
 * API: Save/Update location with hotspots
 * Handles complete location data including hotspots
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, PUT');
header('Access-Control-Allow-Headers: Content-Type');

require_once '../config.php';

// Get JSON input
$input = json_decode(file_get_contents('php://input'), true);

if (!$input) {
    sendResponse(false, null, 'Invalid JSON data', 400);
}

// Validate required fields
$requiredFields = ['id', 'name', 'description', 'background_image'];
foreach ($requiredFields as $field) {
    if (empty($input[$field])) {
        sendResponse(false, null, "Field '$field' is required", 400);
    }
}

$locationId = $input['id'];
$name = $input['name'];
$description = $input['description'];
$backgroundImage = $input['background_image'];
$hotspots = $input['hotspots'] ?? [];
$userId = $input['user_id'] ?? null; // Optional: track who created/updated

try {
    // Start transaction
    $pdo->beginTransaction();

    // Check if location exists
    $checkStmt = $pdo->prepare("SELECT id FROM locations WHERE id = ?");
    $checkStmt->execute([$locationId]);
    $exists = $checkStmt->fetch();

    if ($exists) {
        // Update existing location
        $stmt = $pdo->prepare("
            UPDATE locations
            SET name = ?,
                description = ?,
                background_image = ?,
                updated_at = CURRENT_TIMESTAMP
            WHERE id = ?
        ");
        $stmt->execute([$name, $description, $backgroundImage, $locationId]);
        $message = 'Location updated successfully';
    } else {
        // Insert new location
        $stmt = $pdo->prepare("
            INSERT INTO locations (id, name, description, background_image, created_by)
            VALUES (?, ?, ?, ?, ?)
        ");
        $stmt->execute([$locationId, $name, $description, $backgroundImage, $userId]);
        $message = 'Location created successfully';
    }

    // Delete existing hotspots for this location
    $deleteStmt = $pdo->prepare("DELETE FROM hotspots WHERE location_id = ?");
    $deleteStmt->execute([$locationId]);

    // Insert new hotspots
    if (!empty($hotspots)) {
        $hotspotStmt = $pdo->prepare("
            INSERT INTO hotspots
            (location_id, type, x, y, width, height, label, description, target_location, item_id, interaction_data)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");

        foreach ($hotspots as $hotspot) {
            $interactionData = null;
            if (isset($hotspot['interaction_data'])) {
                $interactionData = is_string($hotspot['interaction_data'])
                    ? $hotspot['interaction_data']
                    : json_encode($hotspot['interaction_data']);
            }

            $hotspotStmt->execute([
                $locationId,
                $hotspot['type'] ?? 'navigation',
                $hotspot['x'] ?? 0,
                $hotspot['y'] ?? 0,
                $hotspot['width'] ?? 10,
                $hotspot['height'] ?? 10,
                $hotspot['label'] ?? null,
                $hotspot['description'] ?? null,
                $hotspot['target_location'] ?? null,
                $hotspot['item_id'] ?? null,
                $interactionData
            ]);
        }
    }

    // Handle connections if provided
    if (isset($input['connections'])) {
        // Delete old connections from this location
        $deleteConnStmt = $pdo->prepare("DELETE FROM connections WHERE from_location = ?");
        $deleteConnStmt->execute([$locationId]);

        // Insert new connections
        if (!empty($input['connections'])) {
            $connStmt = $pdo->prepare("
                INSERT INTO connections (from_location, to_location)
                VALUES (?, ?)
                ON DUPLICATE KEY UPDATE from_location = from_location
            ");

            foreach ($input['connections'] as $targetLocation) {
                $connStmt->execute([$locationId, $targetLocation]);
            }
        }
    }

    // Commit transaction
    $pdo->commit();

    sendResponse(true, ['id' => $locationId], $message);

} catch (PDOException $e) {
    // Rollback on error
    $pdo->rollBack();
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
