<?php
//get_schedule.php
require_once 'config.php';

$date = $_GET['date'] ?? date('Y-m-d');
$user_timezone = $_GET['timezone'] ?? 'UTC';
$page = $_GET['page'] ?? 1;

$api_url = API_BASE_URL . "fixtures/date/{$date}?api_token=" . API_TOKEN . "&include=league;participants;scores&page={$page}";

$ch = curl_init($api_url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Accept: application/json']);
$response = curl_exec($ch);

if (curl_errno($ch)) {
    echo json_encode(['error' => curl_error($ch)]);
} else {
    $data = json_decode($response, true);
    
    $processed_data = [];
    
    if (isset($data['data']) && is_array($data['data'])) {
        foreach ($data['data'] as $match) {
            $home_team = $match['participants'][0]['meta']['location'] === 'home' ? $match['participants'][0] : $match['participants'][1];
            $away_team = $match['participants'][0]['meta']['location'] === 'away' ? $match['participants'][0] : $match['participants'][1];
            
            $status = 'upcoming';
            $home_score = null;
            $away_score = null;
            
            if (!empty($match['scores'])) {
                foreach ($match['scores'] as $score) {
                    if ($score['description'] === 'CURRENT' || $score['description'] === 'FT') {
                        $status = 'finished';
                        if ($score['participant_id'] === $home_team['id']) {
                            $home_score = $score['score']['goals'];
                        } else {
                            $away_score = $score['score']['goals'];
                        }
                    }
                }
            }
            
            // Convert match time to user's timezone
            $match_time = new DateTime($match['starting_at'], new DateTimeZone('UTC'));
            $match_time->setTimezone(new DateTimeZone($user_timezone));
            
            $processed_data[] = [
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
            ];
        }
    }
    
    // Sort matches by start time
    usort($processed_data, function($a, $b) {
        return strtotime($a['starting_at']) - strtotime($b['starting_at']);
    });
    
    $response = [
        'data' => $processed_data,
        'meta' => [
            'current_page' => $data['meta']['pagination']['current_page'] ?? 1,
            'total_pages' => $data['meta']['pagination']['total_pages'] ?? 25,
            'total_matches' => $data['meta']['pagination']['total'] ?? count($processed_data),
            'user_timezone' => $user_timezone
        ]
    ];
    
    echo json_encode($response);
}
curl_close($ch);
?>