<?php
// cron_update_matches.php
error_reporting(E_ALL);
ini_set('display_errors', 1);

ob_start();

require_once 'config.php';

ini_set('log_errors', 1);
ini_set('error_log', 'matches_error.log');

function safe_log($message) {
    error_log(date('[Y-m-d H:i:s] ') . $message . "\n", 3, 'matches.log');
}

safe_log("Script started");

try {
    $db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    if ($db->connect_error) {
        throw new Exception("Database connection failed: " . $db->connect_error);
    }
    safe_log("Database connection successful");
} catch (Exception $e) {
    safe_log("Database Error: " . $e->getMessage());
    die("Database connection failed. Check the error log for details.");
}

// Define the allowed league IDs for database filtering
$allowed_league_ids = [2, 8, 5, 564, 82, 384, 72, 301, 779, 1082, 1101, 1538, 1007, 944];

// Fetch and process live matches
safe_log("Fetching live matches");
try {
    $live_matches = api_request("livescores", ['include' => 'league;participants;scores;comments;statistics;events;venue;coaches;formations;lineups.details.type']);
    if ($live_matches && isset($live_matches['data'])) {
        $live_match_count = count($live_matches['data']);
        safe_log("Found $live_match_count live matches to process");
        foreach ($live_matches['data'] as $match) {
            process_match($match, $db);
            generate_match_page($match, 'live');
        }
        echo "Processed $live_match_count live matches.\n";
    } else {
        safe_log("No live matches found or API request failed.");
        echo "No live matches found or API request failed.\n";
    }
} catch (Exception $e) {
    safe_log("Error processing live matches: " . $e->getMessage());
    echo "Error processing live matches. Check the error log for details.\n";
}

// Fetch and process matches for today and the next 7 days
safe_log("Fetching matches for today and the next 7 days");
$total_matches = 0;
try {
    for ($i = 0; $i < 8; $i++) {
        $date = date('Y-m-d', strtotime("+$i days"));
        $matches = api_request("fixtures/date/{$date}", ['include' => 'league;participants;scores;comments;statistics;events;venue;coaches;formations;lineups.details.type']);
        if ($matches && isset($matches['data'])) {
            $match_count = count($matches['data']);
            $total_matches += $match_count;
            safe_log("Found $match_count matches to process for $date");
            foreach ($matches['data'] as $match) {
                $status = determine_match_status($match);
                process_match($match, $db);
                generate_match_page($match, $status);
            }
            echo "Processed $match_count matches for $date.\n";
        } else {
            safe_log("No matches found or API request failed for $date.");
            echo "No matches found or API request failed for $date.\n";
        }
    }
    echo "Processed a total of $total_matches matches for today and the next 7 days.\n";
} catch (Exception $e) {
    safe_log("Error processing matches: " . $e->getMessage());
    echo "Error processing matches. Check the error log for details.\n";
}

$db->close();
safe_log("Database connection closed");

safe_log("Script completed");

$output = ob_get_clean();
echo $output;

function determine_match_status($match) {
    $now = new DateTime();
    $match_time = new DateTime($match['starting_at']);
    $time_diff = $now->getTimestamp() - $match_time->getTimestamp();

    if ($time_diff < 0) {
        return 'upcoming';
    } elseif ($time_diff < 7200) { // Assume a match lasts at most 2 hours
        foreach ($match['scores'] as $score) {
            if ($score['description'] === 'CURRENT') {
                return 'live';
            } elseif ($score['description'] === 'FT') {
                return 'finished';
            }
        }
        return 'live'; // If the match has started but no score is available, assume it's live
    } else {
        return 'finished';
    }
}

function process_match($match, $db) {
    global $allowed_league_ids;
    
    if (!in_array($match['league']['id'], $allowed_league_ids)) {
        return null;
    }

    $home_team = $match['participants'][0]['meta']['location'] === 'home' ? $match['participants'][0] : $match['participants'][1];
    $away_team = $match['participants'][0]['meta']['location'] === 'away' ? $match['participants'][0] : $match['participants'][1];
    
    $home_score = null;
    $away_score = null;
    $status = 'upcoming';
    $match_time = '';
    $minutes_played = 0;
    
    foreach ($match['scores'] as $score) {
        if ($score['description'] === 'CURRENT' || $score['description'] === 'FT') {
            if ($score['participant_id'] === $home_team['id']) {
                $home_score = $score['score']['goals'];
            } else {
                $away_score = $score['score']['goals'];
            }
        }
    }
    
    // Check if the match has ended based on the state_id or result_info
    if ($match['state_id'] === 5 || (isset($match['result_info']) && strpos($match['result_info'], 'won') !== false)) {
        $status = 'finished';
    } else {
        $start_time = new DateTime($match['starting_at'], new DateTimeZone('UTC'));
        $current_time = new DateTime('now', new DateTimeZone('UTC'));
        $time_diff = $current_time->diff($start_time);
        $minutes_played = $time_diff->days * 24 * 60 + $time_diff->h * 60 + $time_diff->i;
        
        if ($minutes_played > 0) {
            $status = 'live';
            $match_time = $minutes_played . "'";
        }
    }
    
    $match_time_utc = new DateTime($match['starting_at'], new DateTimeZone('UTC'));
    
    // Use a default timezone or fetch it from your configuration
    $user_timezone = 'UTC'; // Replace with your default timezone or fetch from config
    $match_time_user = $match_time_utc->setTimezone(new DateTimeZone($user_timezone));
    
    return [
        'id' => $match['id'],
        'league_name' => $match['league']['name'],
        'home_team' => [
            'name' => $home_team['name'],
            'logo' => $home_team['image_path'],
            'score' => $home_score
        ],
        'away_team' => [
            'name' => $away_team['name'],
            'logo' => $away_team['image_path'],
            'score' => $away_score
        ],
        'status' => $status,
        'match_time' => $match_time,
        'minutes_played' => $minutes_played,
        'starting_at' => $match_time_user->format('c')
    ];
}

function generate_match_page($match, $status) {
    try {
        $home_team = $match['participants'][0]['meta']['location'] === 'home' ? $match['participants'][0] : $match['participants'][1];
        $away_team = $match['participants'][0]['meta']['location'] === 'away' ? $match['participants'][0] : $match['participants'][1];

        $league_name = sanitize_filename($match['league']['name']);
        $home_team_name = sanitize_filename($home_team['name']);
        $away_team_name = sanitize_filename($away_team['name']);
        $match_id = $match['id'];

        $league_dir = "leagues/{$league_name}";
        if (!is_dir($league_dir)) {
            if (!mkdir($league_dir, 0755, true)) {
                throw new Exception("Failed to create directory: " . $league_dir);
            }
        }

        $filename = "{$league_dir}/{$home_team_name}-vs-{$away_team_name}-{$match_id}.php";

        $content = generate_match_page_content($match, $status);
        if (file_put_contents($filename, $content) === false) {
            throw new Exception("Failed to write file: " . $filename);
        }
        safe_log("Generated match page: " . $filename);
    } catch (Exception $e) {
        safe_log("Error generating match page: " . $e->getMessage());
    }
}

function sanitize_filename($name) {
    $name = strtolower($name);
    $name = iconv('UTF-8', 'ASCII//TRANSLIT', $name); // Convert accented characters
    $name = preg_replace('/[^a-z0-9]+/', '-', $name);
    $name = trim($name, '-');
    return $name;
}

function generate_match_page_content($match, $status) {
    ob_start();
    include 'match_template.php';
    $match_data = [
        'match' => $match,
        'status' => $status
    ];
    extract($match_data);
    return ob_get_clean();
}

function api_request($endpoint, $params = []) {
    $url = API_BASE_URL . $endpoint;
    $params['api_token'] = API_TOKEN;
    $url .= '?' . http_build_query($params);

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Accept: application/json']);
    $response = curl_exec($ch);
    
    if (curl_errno($ch)) {
        safe_log("API request error: " . curl_error($ch));
        return false;
    }
    
    curl_close($ch);
    return json_decode($response, true);
}

echo 'PHP Version: ' . phpversion();
?>