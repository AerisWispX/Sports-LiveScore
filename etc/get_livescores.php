<?php
//get_livescores.php
require_once 'config.php';

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

function process_match($match, $is_live) {
    $home_team = $match['participants'][0]['meta']['location'] === 'home' ? $match['participants'][0] : $match['participants'][1];
    $away_team = $match['participants'][0]['meta']['location'] === 'away' ? $match['participants'][0] : $match['participants'][1];
    
    $home_score = 0;
    $away_score = 0;
    $halftime_home_score = 0;
    $halftime_away_score = 0;
    
    if ($is_live) {
        foreach ($match['scores'] as $score) {
            if ($score['description'] === 'CURRENT') {
                if ($score['participant_id'] === $home_team['id']) {
                    $home_score = $score['score']['goals'];
                } else {
                    $away_score = $score['score']['goals'];
                }
            } elseif ($score['description'] === '1ST_HALF') {
                if ($score['participant_id'] === $home_team['id']) {
                    $halftime_home_score = $score['score']['goals'];
                } else {
                    $halftime_away_score = $score['score']['goals'];
                }
            }
        }
    }
    
    $start_time = new DateTime($match['starting_at']);
    $current_time = new DateTime();
    $time_diff = $current_time->diff($start_time);
    $minutes_played = $is_live ? $time_diff->days * 24 * 60 + $time_diff->h * 60 + $time_diff->i : 0;
    
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
        'halftime_score' => $halftime_home_score . '-' . $halftime_away_score,
        'minutes_played' => $minutes_played,
        'state' => $match['state_id'],
        'starting_at' => $match['starting_at'],
        'is_live' => $is_live
    ];
}

$live_matches = fetch_matches('livescores', 'league;participants;scores');
$today = date('Y-m-d');
$upcoming_matches = fetch_matches("fixtures/date/{$today}", 'league;participants');

$processed_data = [];

if (isset($live_matches['data'])) {
    foreach ($live_matches['data'] as $match) {
        $processed_data[] = process_match($match, true);
    }
}

if (isset($upcoming_matches['data'])) {
    foreach ($upcoming_matches['data'] as $match) {
        if (strtotime($match['starting_at']) > time()) {
            $processed_data[] = process_match($match, false);
        }
    }
}

// Sort matches by start time
usort($processed_data, function($a, $b) {
    return strtotime($a['starting_at']) - strtotime($b['starting_at']);
});

echo json_encode(['data' => $processed_data]);
?>