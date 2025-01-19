<?php
//temp.php
// Ensure $match and $status variables are available
if (!isset($match) || !isset($status)) {
    die("Error: Match data not provided.");
}

// Helper function to get team data
if (!function_exists('getTeamData')) {
    function getTeamData($participant, $scores, $events) {
        $score = '-';
        $goalScorers = [];
        foreach ($scores as $scoreData) {
            if ($scoreData['participant_id'] == $participant['id'] && $scoreData['description'] == 'CURRENT') {
                $score = $scoreData['score']['goals'];
                break;
            }
        }
        foreach ($events ?? [] as $event) {
            if ($event['type_id'] == 14 && $event['participant_id'] == $participant['id']) {
                $goalScorers[] = [
                    'name' => $event['player_name'],
                    'time' => $event['minute']
                ];
            }
        }
        return [
            'name' => $participant['name'],
            'logo' => $participant['image_path'],
            'score' => $score,
            'goalScorers' => $goalScorers
        ];
    }
}

// Get home and away team data
$home_team = getTeamData(
    $match['participants'][0]['meta']['location'] === 'home' ? $match['participants'][0] : $match['participants'][1],
    $match['scores'] ?? [],
    $match['events'] ?? []
);
$away_team = getTeamData(
    $match['participants'][0]['meta']['location'] === 'away' ? $match['participants'][0] : $match['participants'][1],
    $match['scores'] ?? [],
    $match['events'] ?? []
);

$match_time = new DateTime($match['starting_at']);
$match_time_utc = $match_time->getTimestamp();
$current_time = new DateTime();
$time_diff = $current_time->diff($match_time);
$minutes_played = $status === 'live' ? $time_diff->days * 24 * 60 + $time_diff->h * 60 + $time_diff->i : 0;

// Get venue
$venue = $match['venue']['name'] ?? 'Unknown Venue';

// Get league data
$league = [
    'name' => $match['league']['name'],
    'logo' => $match['league']['image_path'] ?? ''
];

// Function to get formatted match status
if (!function_exists('getMatchStatus')) {
    function getMatchStatus($status, $minutes_played, $starting_at) {
        if ($status === 'live') {
            return $minutes_played . "'";
        } elseif ($status === 'finished') {
            return 'FT';
        } else {
            return date('H:i', strtotime($starting_at));
        }
    }
}

$match_status = getMatchStatus($status, $minutes_played, $match['starting_at']);

// Prepare match data for JavaScript
$matchJson = json_encode([
    'id' => $match['id'],
    'home_team' => $home_team,
    'away_team' => $away_team,
    'match_time_utc' => $match_time_utc,
    'status' => $status,
    'venue' => $venue,
    'league' => $league,
    'match_status' => $match_status,
    'lineups' => $match['lineups'] ?? [],
    'statistics' => $match['statistics'] ?? [],
    'events' => $match['events'] ?? [],
    'commentary' => $match['comments'] ?? [],
    'formations' => $match['formations'] ?? [],
    'coaches' => $match['coaches'] ?? []
]);
?>