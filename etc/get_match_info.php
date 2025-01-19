<?php
// get_match_info.php
error_reporting(E_ALL);
ini_set('display_errors', 1);
header('Content-Type: application/json');
require_once 'config.php';

function debug_log($message) {
    error_log(date('[Y-m-d H:i:s] ') . $message . "\n", 3, 'get_match_info_debug.log');
}

debug_log("Script started");

$api_token = '66DxtVLqA8D5rcVuQKxqSO64v0v0eX8no0Yz9LtizfmbSaQz0zqWvwgY75Xo';
$api_url = "https://api.sportmonks.com/v3/football/livescores?api_token={$api_token}&include=league;participants;scores";

function api_request($endpoint, $params = []) {
    global $api_token, $api_url;
    $url = "https://api.sportmonks.com/v3/football/{$endpoint}?api_token={$api_token}";
    
    if (!empty($params)) {
        $url .= '&' . http_build_query($params);
    }
    
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Accept: application/json'
    ]);
    
    $response = curl_exec($ch);
    
    if (curl_errno($ch)) {
        throw new Exception(curl_error($ch));
    }
    
    curl_close($ch);
    return json_decode($response, true);
}

try {
    debug_log("Attempting API request");
    $data = api_request("livescores", ['include' => 'league;participants;scores']);
    debug_log("API request completed");
    
    if (isset($data['data'])) {
        $processed_data = array_map(function($match) {
            $home_team = $match['participants'][0]['meta']['location'] === 'home' ? $match['participants'][0] : $match['participants'][1];
            $away_team = $match['participants'][0]['meta']['location'] === 'away' ? $match['participants'][0] : $match['participants'][1];
            
            $home_score = 0;
            $away_score = 0;
            $halftime_home_score = 0;
            $halftime_away_score = 0;
            
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
            
            $start_time = new DateTime($match['starting_at']);
            $current_time = new DateTime();
            $time_diff = $current_time->diff($start_time);
            $minutes_played = $time_diff->days * 24 * 60 + $time_diff->h * 60 + $time_diff->i;
            
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
                'state' => $match['state_id']
            ];
        }, $data['data']);
        
        debug_log("Response prepared: " . json_encode(['data' => $processed_data]));
        echo json_encode(['data' => $processed_data]);
    } else {
        throw new Exception('Match data not found');
    }
} catch (Exception $e) {
    debug_log("Error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}

debug_log("Script completed");
?>