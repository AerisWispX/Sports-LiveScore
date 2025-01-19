<?php
//fetch.php
require_once 'config.php';

// Define the league IDs you want to filter
$allowed_league_ids = [8, 2, 5, 564, 82, 384, 72, 301, 779, 1082, 1101, 1538, 1007, 944, 85];

// Get user timezone from the request, default to UTC if not provided
$user_timezone = $_GET['timezone'] ?? 'UTC';

function fetch_matches($endpoint, $include) {
    $api_url = API_BASE_URL . $endpoint . "?api_token=" . API_TOKEN . "&include=" . $include;
    $ch = curl_init($api_url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Accept: application/json']);
    $response = curl_exec($ch);
    if (curl_errno($ch)) {
        return ['error' => curl_error($ch)];
    }
    curl_close($ch);
    return json_decode($response, true);
}

function determine_match_status($match) {
    $now = new DateTime('now', new DateTimeZone('UTC'));
    $match_time = new DateTime($match['starting_at'], new DateTimeZone('UTC'));
    $time_diff = $now->getTimestamp() - $match_time->getTimestamp();

    if ($time_diff < 0) {
        return 'upcoming';
    } elseif ($time_diff < 7200) { // Assume a match lasts at most 2 hours
        foreach ($match['scores'] as $score) {
            if ($score['description'] === 'CURRENT') {
                return 'live';
            }
        }
        // If the match has started but no CURRENT score is available, check if it's finished
        if ($match['state_id'] === 5 || (isset($match['result_info']) && strpos($match['result_info'], 'won') !== false)) {
            return 'finished';
        }
        // If no CURRENT score and not finished, assume it's live
        return 'live';
    } else {
        return 'finished';
    }
}

function process_match($match, $user_timezone) {
    global $allowed_league_ids;
    
    if (!in_array($match['league_id'], $allowed_league_ids)) {
        return null;
    }

    $home_team = $match['participants'][0]['meta']['location'] === 'home' ? $match['participants'][0] : $match['participants'][1];
    $away_team = $match['participants'][0]['meta']['location'] === 'away' ? $match['participants'][0] : $match['participants'][1];
    
    $home_score = null;
    $away_score = null;
    $status = determine_match_status($match);
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
    
    if ($status === 'live') {
        $start_time = new DateTime($match['starting_at'], new DateTimeZone('UTC'));
        $current_time = new DateTime('now', new DateTimeZone('UTC'));
        $time_diff = $current_time->diff($start_time);
        $minutes_played = $time_diff->days * 24 * 60 + $time_diff->h * 60 + $time_diff->i;
        $match_time = $minutes_played . "'";
    }
    
    $match_time_utc = new DateTime($match['starting_at'], new DateTimeZone('UTC'));
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

// Fetch live matches
$live_matches = fetch_matches("livescores", 'league;participants;scores');

// Fetch fixtures for today
$today = date('Y-m-d');
$fixtures = fetch_matches("fixtures/date/{$today}", 'league;participants;scores');

$processed_matches = [];

// Process live matches
if (isset($live_matches['data'])) {
    foreach ($live_matches['data'] as $match) {
        $processed_match = process_match($match, $user_timezone);
        if ($processed_match) {
            $processed_matches[$processed_match['id']] = $processed_match;
        }
    }
}

// Process fixtures (upcoming and finished matches)
if (isset($fixtures['data'])) {
    foreach ($fixtures['data'] as $match) {
        $processed_match = process_match($match, $user_timezone);
        if ($processed_match) {
            // Only add or update if not already present (live matches take precedence)
            if (!isset($processed_matches[$processed_match['id']])) {
                $processed_matches[$processed_match['id']] = $processed_match;
            }
        }
    }
}

// Separate matches into categories
$live_matches = [];
$upcoming_matches = [];
$finished_matches = [];

foreach ($processed_matches as $match) {
    switch ($match['status']) {
        case 'live':
            $live_matches[] = $match;
            break;
        case 'upcoming':
            $upcoming_matches[] = $match;
            break;
        case 'finished':
            $finished_matches[] = $match;
            break;
    }
}

// Sort upcoming matches by start time
usort($upcoming_matches, function($a, $b) {
    return strtotime($a['starting_at']) - strtotime($b['starting_at']);
});

// Sort finished matches by start time (most recent first)
usort($finished_matches, function($a, $b) {
    return strtotime($b['starting_at']) - strtotime($a['starting_at']);
});

$response_data = [
    'live_matches' => array_values($live_matches),
    'upcoming_matches' => $upcoming_matches,
    'finished_matches' => $finished_matches,
    'user_timezone' => $user_timezone
];

// Check if it's an AJAX request
if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
    echo json_encode($response_data);
    exit;
}

// If not an AJAX request, you might want to handle it differently or just output the data
echo "<pre>";
print_r($response_data);
echo "</pre>";
?>