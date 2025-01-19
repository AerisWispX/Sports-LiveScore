<?php
require_once 'config.php';

$match_id = $_GET['id'] ?? null;

if (!$match_id) {
    die('Match ID not provided');
}

$db = db_connect();
$stmt = $db->prepare("SELECT * FROM matches WHERE api_match_id = ?");
$stmt->bind_param("i", $match_id);
$stmt->execute();
$result = $stmt->get_result();
$match = $result->fetch_assoc();
$stmt->close();

if (!$match || $match['status'] !== 'live' || !$match['is_stream_active']) {
    die('Stream not available');
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($match['home_team'] . ' vs ' . $match['away_team']); ?> - Live Stream</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; margin: 0; padding: 20px; background-color: #f4f4f4; }
        .container { max-width: 800px; margin: auto; background: #fff; padding: 20px; border-radius: 5px; }
        h1 { color: #333; }
        .stream-container { position: relative; padding-bottom: 56.25%; height: 0; overflow: hidden; }
        .stream-container iframe { position: absolute; top: 0; left: 0; width: 100%; height: 100%; }
    </style>
</head>
<body>
    <div class="container">
        <h1><?php echo htmlspecialchars($match['home_team'] . ' vs ' . $match['away_team']); ?> - Live Stream</h1>
        <div class="stream-container">
            <iframe src="<?php echo htmlspecialchars($match['stream_link']); ?>" frameborder="0" allowfullscreen></iframe>
        </div>
    </div>
</body>
</html>