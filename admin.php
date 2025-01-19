<?php
//admin.php
require_once 'config.php';
session_start();

// Simple authentication (replace with a more secure method in production)
if (!isset($_SESSION['admin']) && $_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($_POST['username'] === 'admin' && $_POST['password'] === 'password') {
        $_SESSION['admin'] = true;
    }
}

if (!isset($_SESSION['admin'])) {
    echo '<form method="post"><input type="text" name="username"><input type="password" name="password"><input type="submit" value="Login"></form>';
    exit;
}

$db = db_connect();

// Handle form submission for stream updates
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['match_id'])) {
    $match_id = $_POST['match_id'];
    $stream_link_hd1 = isset($_POST['stream_link_hd1']) ? $_POST['stream_link_hd1'] : '';
    $stream_link_hd2 = isset($_POST['stream_link_hd2']) ? $_POST['stream_link_hd2'] : '';
    $stream_link_hd3 = isset($_POST['stream_link_hd3']) ? $_POST['stream_link_hd3'] : '';
    $stream_link_hd4 = isset($_POST['stream_link_hd4']) ? $_POST['stream_link_hd4'] : '';
    $custom_link = isset($_POST['custom_link']) ? $_POST['custom_link'] : '';
    $is_stream_active = isset($_POST['is_stream_active']) ? 1 : 0;
    
    $stmt = $db->prepare("UPDATE matches SET stream_link_hd1 = ?, stream_link_hd2 = ?, stream_link_hd3 = ?, stream_link_hd4 = ?, custom_link = ?, is_stream_active = ? WHERE api_match_id = ?");
    $stmt->bind_param("sssssii", $stream_link_hd1, $stream_link_hd2, $stream_link_hd3, $stream_link_hd4, $custom_link, $is_stream_active, $match_id);
    $stmt->execute();
    $stmt->close();

    // Run the cron_generate_stream_pages.php script
    $output = [];
    $return_var = 0;
    exec('php cron_generate_stream_pages.php', $output, $return_var);

    if ($return_var !== 0) {
        echo "<p style='color: red;'>Error running cron script. Check the logs for details.</p>";
    } else {
        echo "<p style='color: green;'>Match information and stream pages updated successfully.</p>";
    }
}

function get_matches($db, $status, $search = '') {
    $query = "SELECT * FROM matches WHERE status = ?";
    $params = [$status];
    $types = "s";

    if (!empty($search)) {
        $query .= " AND (league_name LIKE ? OR home_team LIKE ? OR away_team LIKE ?)";
        $search_param = "%$search%";
        $params = array_merge($params, [$search_param, $search_param, $search_param]);
        $types .= "sss";
    }

    $query .= " ORDER BY match_date, match_time";
    
    $stmt = $db->prepare($query);
    $stmt->bind_param($types, ...$params);
    $stmt->execute();
    $result = $stmt->get_result();
    $matches = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
    return $matches;
}

$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$upcoming_matches = get_matches($db, 'upcoming', $search);
$live_matches = get_matches($db, 'live', $search);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel</title>
    <style>
        :root {
            --primary-color: #9b87f5;
            --secondary-color: #202124;
            --text-color: #e8eaed;
            --border-color: #5f6368;
            --card-bg-color: #303134;
            --hover-color: #3367d6;
        }
        body {
            font-family: 'Roboto', Arial, sans-serif;
            line-height: 1.6;
            background-color: #000000;
            color: var(--text-color);
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }
        h1, h2 {
            color: var(--primary-color);
        }
        .search-form {
            margin-bottom: 20px;
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }
        input[type="text"], input[type="submit"] {
            padding: 10px;
            border: 1px solid var(--border-color);
            border-radius: 4px;
            background-color: var(--secondary-color);
            color: var(--text-color);
        }
        input[type="text"] {
            flex-grow: 1;
            min-width: 200px;
        }
        input[type="submit"] {
            background-color: var(--primary-color);
            color: white;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        input[type="submit"]:hover {
            background-color: var(--hover-color);
        }
        .match-list {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 20px;
        }
        .match-card {
            background-color: var(--card-bg-color);
            border: 1px solid var(--border-color);
            border-radius: 8px;
            padding: 15px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.2);
        }
        .match-card h3 {
            margin-top: 0;
            color: var(--primary-color);
        }
        .match-card p {
            margin: 5px 0;
        }
        .match-form input[type="text"] {
            width: calc(100% - 22px);
            margin-bottom: 10px;
        }
        .match-form label {
            display: flex;
            align-items: center;
            margin-bottom: 10px;
        }
        .match-form input[type="checkbox"] {
            margin-right: 5px;
        }
        .button-group {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }
        .button-group input[type="submit"] {
            flex: 1;
            min-width: 100px;
        }
        .delete-button {
            background-color: #ea4335;
            color: white;
            border: none;
            padding: 10px;
            border-radius: 4px;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        .delete-button:hover {
            background-color: #d33828;
        }
        @media (max-width: 600px) {
            .container {
                padding: 10px;
            }
            .match-list {
                grid-template-columns: 1fr;
            }
            .button-group input[type="submit"] {
                width: 100%;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Admin Panel</h1>
        
        <form method="get" action="" class="search-form">
            <input type="text" name="search" value="<?php echo htmlspecialchars($search); ?>" placeholder="Search by team or league">
            <input type="submit" value="Search">
        </form>

        <h2>Live Matches</h2>
        <div class="match-list">
            <?php 
            foreach ($live_matches as $match) {
                $match_id = $match['api_match_id'];
                $league_name = htmlspecialchars($match['league_name']);
                $home_team = htmlspecialchars($match['home_team']);
                $away_team = htmlspecialchars($match['away_team']);
                $match_time = $match['match_date'] . ' ' . $match['match_time'];
                ?>
                <div class="match-card">
                    <h3><?php echo "$league_name"; ?></h3>
                    <p><strong><?php echo "$home_team vs $away_team"; ?></strong></p>
                    <p>Match Time: <?php echo $match_time; ?></p>
                    <form method="post" class="match-form">
                        <input type="hidden" name="match_id" value="<?php echo $match_id; ?>">
                        <input type="text" name="stream_link_hd1" value="<?php echo htmlspecialchars($match['stream_link_hd1'] ?? ''); ?>" placeholder="Stream Link HD1">
                        <input type="text" name="stream_link_hd2" value="<?php echo htmlspecialchars($match['stream_link_hd2'] ?? ''); ?>" placeholder="Stream Link HD2">
                        <input type="text" name="stream_link_hd3" value="<?php echo htmlspecialchars($match['stream_link_hd3'] ?? ''); ?>" placeholder="Stream Link HD3">
                        <input type="text" name="stream_link_hd4" value="<?php echo htmlspecialchars($match['stream_link_hd4'] ?? ''); ?>" placeholder="Stream Link HD4">
                        <input type="text" name="custom_link" value="<?php echo htmlspecialchars($match['custom_link'] ?? ''); ?>" placeholder="Custom Link (external website)">
                        <label>
                            <input type="checkbox" name="is_stream_active" <?php echo $match['is_stream_active'] ? 'checked' : ''; ?>>
                            Active
                        </label>
                        <div class="button-group">
                            <input type="submit" name="action" value="Update">
                            <input type="submit" name="action" value="Delete" class="delete-button" onclick="return confirm('Are you sure you want to delete this stream?');">
                        </div>
                    </form>
                </div>
            <?php } ?>
        </div>

        <h2>Upcoming Matches</h2>
        <div class="match-list">
            <?php 
            foreach ($upcoming_matches as $match) {
                $match_id = $match['api_match_id'];
                $league_name = htmlspecialchars($match['league_name']);
                $home_team = htmlspecialchars($match['home_team']);
                $away_team = htmlspecialchars($match['away_team']);
                $match_time = $match['match_date'] . ' ' . $match['match_time'];
                ?>
                <div class="match-card">
                    <h3><?php echo "$league_name"; ?></h3>
                    <p><strong><?php echo "$home_team vs $away_team"; ?></strong></p>
                    <p>Match Time: <?php echo $match_time; ?></p>
                    <form method="post" class="match-form">
                        <input type="hidden" name="match_id" value="<?php echo $match_id; ?>">
                        <input type="text" name="stream_link_hd1" value="<?php echo htmlspecialchars($match['stream_link_hd1'] ?? ''); ?>" placeholder="Stream Link HD1">
                        <input type="text" name="stream_link_hd2" value="<?php echo htmlspecialchars($match['stream_link_hd2'] ?? ''); ?>" placeholder="Stream Link HD2">
                        <input type="text" name="stream_link_hd3" value="<?php echo htmlspecialchars($match['stream_link_hd3'] ?? ''); ?>" placeholder="Stream Link HD3">
                        <input type="text" name="stream_link_hd4" value="<?php echo htmlspecialchars($match['stream_link_hd4'] ?? ''); ?>" placeholder="Stream Link HD4">
                        <input type="text" name="custom_link" value="<?php echo htmlspecialchars($match['custom_link'] ?? ''); ?>" placeholder="Custom Link (external website)">
                        <label>
                            <input type="checkbox" name="is_stream_active" <?php echo $match['is_stream_active'] ? 'checked' : ''; ?>>
                            Active
                        </label>
                        <div class="button-group">
                            <input type="submit" name="action" value="Update">
                            <input type="submit" name="action" value="Delete" class="delete-button" onclick="return confirm('Are you sure you want to delete this stream?');">
                        </div>
                    </form>
                </div>
            <?php } ?>
        </div>
    </div>
</body>
</html>