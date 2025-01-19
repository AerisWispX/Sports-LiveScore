<?php
require_once 'config.php';

$user_timezone = $_GET['timezone'] ?? 'UTC';

$api_url = API_BASE_URL . "fixtures/multi/3/4/5?api_token=" . API_TOKEN . "&include=league;participants;scores";

$ch = curl_init($api_url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Accept: application/json']);
$response = curl_exec($ch);

if (curl_errno($ch)) {
    echo json_encode(['error' => curl_error($ch)]);
    exit;
}

curl_close($ch);

$data = json_decode($response, true);

$live_matches = [];
$upcoming_matches = [];
$finished_matches = [];

foreach ($data['data'] as $match) {
    $home_team = $match['participants'][0]['meta']['location'] === 'home' ? $match['participants'][0] : $match['participants'][1];
    $away_team = $match['participants'][0]['meta']['location'] === 'away' ? $match['participants'][0] : $match['participants'][1];
    
    $status = 'upcoming';
    $home_score = null;
    $away_score = null;
    $minute = null;
    
    if (!empty($match['scores'])) {
        foreach ($match['scores'] as $score) {
            if ($score['description'] === 'CURRENT' || $score['description'] === 'FT') {
                $status = $match['state'] === 'finished' ? 'finished' : 'live';
                if ($score['participant_id'] === $home_team['id']) {
                    $home_score = $score['score']['goals'];
                } else {
                    $away_score = $score['score']['goals'];
                }
            }
        }
    }
    
    if ($status === 'live') {
        $minute = $match['minute'] ?? 'Live';
    }
    
    $match_time = new DateTime($match['starting_at'], new DateTimeZone('UTC'));
    $match_time->setTimezone(new DateTimeZone($user_timezone));
    
    $processed_match = [
        'id' => $match['id'],
        'league_id' => $match['league']['id'],
        'league_name' => $match['league']['name'],
        'home_team' => [
            'name' => $home_team['name'],
            'logo' => $home_team['image_path'],
            'score' => $home_score,
        ],
        'away_team' => [
            'name' => $away_team['name'],
            'logo' => $away_team['image_path'],
            'score' => $away_score,
        ],
        'starting_at' => $match_time->format('c'),
        'status' => $status,
        'minute' => $minute,
    ];
    
    if ($status === 'live') {
        $live_matches[] = $processed_match;
    } elseif ($status === 'upcoming') {
        $upcoming_matches[] = $processed_match;
    } else {
        $finished_matches[] = $processed_match;
    }
}

// Sort matches by start time
usort($upcoming_matches, function($a, $b) {
    return strtotime($a['starting_at']) - strtotime($b['starting_at']);
});

usort($finished_matches, function($a, $b) {
    return strtotime($b['starting_at']) - strtotime($a['starting_at']);
});

$response = [
    'live' => $live_matches,
    'upcoming' => $upcoming_matches,
    'finished' => $finished_matches,
];

echo json_encode($response);
?>