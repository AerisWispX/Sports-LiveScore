<?php
// At the beginning of match_template.php
if (!isset($match) || !isset($status)) {
    die("Error: Match data not provided.");
}

// Include temp.php to prepare the data
include 'temp.php';

// Rest of your HTML and PHP code
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($home_team['name']); ?> vs <?php echo htmlspecialchars($away_team['name']); ?> | Live Match</title>
<style>
:root {
            --primary-color: #9b87f5;
            --secondary-color: #718096;
            --background-color: #0a0c10;
            --card-background: #1a1e24;
            --text-color: #e2e8f0;
            --secondary-text-color: #a0aec0;
        }
        body {
            font-family: Arial, sans-serif;
            background-color: var(--background-color);
            color: #ffffff;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 100%;
            margin: 0 auto;
            padding: 10px;
            transition: max-width 0.3s ease;
            padding-top: 72px; /* Added to accommodate fixed header */
        }
        @media (min-width: 768px) {
            .container {
                max-width: 80%;
            }
        }
        @media (min-width: 1200px) {
            .container {
                max-width: 1000px;
            }
        }
.match-card {
    background-color: #1a1e24;
    border-radius: 8px;
    padding: 15px;
    margin-bottom: 15px;
    transition: all 0.3s ease;
}

.match-card:hover {
    box-shadow: 0 0 15px rgba(138, 74, 243, 0.3);
}

.match-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 10px;
}

.match-tournament img {
    width: 20px;
    height: 20px;
    margin-right: 5px;
    vertical-align: middle;
}

.match-status {
    font-weight: bold;
    color: #8a4af3;
}

.match-info {
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.team {
    display: flex;
    flex-direction: column;
    align-items: center;
    width: 40%;
    transition: transform 0.3s ease;
}

.team:hover {
    transform: scale(1.05);
}

.team-logo img {
    width: 40px;
    height: 40px;
    margin-bottom: 5px;
    transition: transform 0.3s ease;
}

.team-logo img:hover {
    transform: scale(1.1);
}

.team-name {
    font-size: 14px;
    font-weight: bold;
    text-align: center;
}

.score {
    font-size: 24px;
    font-weight: bold;
    display: flex;
    align-items: center;
}

.score-divider {
    margin: 0 5px;
}

.goal-scorers {
    font-size: 12px;
    margin-top: 5px;
    text-align: center;
}

.match-details {
    text-align: center;
    margin-top: 10px;
    font-size: 12px;
    color: #888;
}

.tabs {
    display: flex;
    justify-content: space-between;
    margin-top: 15px;
    background-color: #1A1E24;
    border-radius: 5px;
    overflow: hidden;
}

.tab {
    flex: 1;
    padding: 10px;
    text-align: center;
    cursor: pointer;
    transition: background-color 0.3s, color 0.3s;
}

.tab:hover {
    background-color: #444;
}

.tab.active {
    background-color: #8a4af3;
    color: #1a1e24;
}

.tab-content {
    display: none;
    margin-top: 15px;
    background-color: #1a1e24;
    border-radius: 8px;
    padding: 15px;
    opacity: 0;
    transition: opacity 0.3s ease;
}

.tab-content.active {
    display: block;
    opacity: 1;
}

.football-field {
    width: 100%;
    height: 300px;
    margin: 0 auto;
    background-color: #539A46;
    position: relative;
    border: 2px solid #ffffff;
    transition: all 0.3s ease;
}

.player {
    position: absolute;
    width: 30px;
    height: 30px;
    background-color: rgba(255, 255, 255, 0.7);
    border-radius: 50%;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    font-size: 10px;
    transition: all 0.3s ease;
}

.player:hover {
    transform: scale(1.1);
    z-index: 10;
}

.player-circle {
    width: 20px;
    height: 20px;
    background-color: #8a4af3;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
    color: #121212;
    margin-bottom: 2px;
}

.player-name {
    display: none;
    position: absolute;
    bottom: 100%;
    left: 50%;
    transform: translateX(-50%);
    background-color: rgba(0, 0, 0, 0.8);
    color: #fff;
    padding: 2px 5px;
    border-radius: 3px;
    white-space: nowrap;
}

.player:hover .player-name {
    display: block;
}

#stats-view, #events-view, #commentary-view {
    max-height: 300px;
    overflow-y: auto;
    scrollbar-width: thin;
    scrollbar-color: #8a4af3 #1a1e24;
}

#stats-view::-webkit-scrollbar, #events-view::-webkit-scrollbar, #commentary-view::-webkit-scrollbar {
    width: 8px;
}

#stats-view::-webkit-scrollbar-track, #events-view::-webkit-scrollbar-track, #commentary-view::-webkit-scrollbar-track {
    background: #1a1e24;
}

#stats-view::-webkit-scrollbar-thumb, #events-view::-webkit-scrollbar-thumb, #commentary-view::-webkit-scrollbar-thumb {
    background-color: #8a4af3;
    border-radius: 4px;
}

.stat-item {
    margin-bottom: 10px;
}

.stat-title {
    font-size: 12px;
    margin-bottom: 5px;
}

.stat-bar {
    height: 10px;
    background-color: #333;
    border-radius: 5px;
    overflow: hidden;
    display: flex;
    transition: all 0.3s ease;
}

.stat-value {
    height: 100%;
    transition: width 0.3s ease;
}

.home-stat {
    background-color: #8a4af3;
}

.away-stat {
    background-color: #ff4500;
}

.stat-numbers {
    display: flex;
    justify-content: space-between;
    font-size: 12px;
    margin-top: 5px;
}

#events-view ul, #commentary-view ul {
    list-style-type: none;
    padding: 0;
    margin: 0;
}

#events-view li, #commentary-view li {
    margin-bottom: 10px;
    font-size: 12px;
    transition: background-color 0.3s ease;
    padding: 5px;
    border-radius: 3px;
}

#events-view li:hover, #commentary-view li:hover {
    background-color: #2a2a2a;
}
.goal-scorers {
            font-size: 11px;
            margin-top: 5px;
            text-align: center;
            color: #888;
            font-weight: 300;
        }

        .watch-now-btn {
            background-color: #8a4af3;
            border: none;
            color: white;
            padding: 10px 20px;
            text-align: center;
            text-decoration: none;
            display: inline-block;
            font-size: 16px;
            margin: 10px auto;
            cursor: pointer;
            border-radius: 25px;
            transition: background-color 0.3s ease;
        }

        .watch-now-btn:hover {
            background-color: #7a3ae3;
        }

        .button-container {
            text-align: center;
            margin-top: 10px;
        }
        
.football-field {
        width: 100%;
        height: 0;
        padding-bottom: 150%;
        background-color: #2c7d3c;
        position: relative;
        border: 2px solid #ffffff;
        border-radius: 10px;
        overflow: hidden;
    }

    .field-line {
        position: absolute;
        background-color: rgba(255, 255, 255, 0.7);
    }

    .center-circle {
        width: 30%;
        height: 20%;
        border: 2px solid rgba(255, 255, 255, 0.7);
        border-radius: 50%;
        top: 40%;
        left: 35%;
    }

    .halfway-line {
        width: 100%;
        height: 2px;
        top: 50%;
    }

    .penalty-area {
        width: 60%;
        height: 30%;
        border: 2px solid rgba(255, 255, 255, 0.7);
    }

    .penalty-area.home {
        bottom: 0;
        left: 20%;
    }

    .penalty-area.away {
        top: 0;
        left: 20%;
    }

    .goal-area {
        width: 30%;
        height: 10%;
        border: 2px solid rgba(255, 255, 255, 0.7);
    }

    .goal-area.home {
        bottom: 0;
        left: 35%;
    }

    .goal-area.away {
        top: 0;
        left: 35%;
    }

    .player {
        position: absolute;
        width: 40px;
        height: 40px;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        transition: all 0.3s ease;
        transform: translate(-50%, -50%);
    }

    .player:hover {
        transform: translate(-50%, -50%) scale(1.1);
        z-index: 10;
    }

    .player-circle {
        width: 28px;
        height: 28px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: bold;
        color: #ffffff;
        margin-bottom: 2px;
    }

    .player.home .player-circle {
        background-color: #3498db;
    }

    .player.away .player-circle {
        background-color: #e74c3c;
    }

    .player-name {
        font-size: 10px;
        color: #ffffff;
        text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.7);
        white-space: nowrap;
    }
     .top-header {
            background-color: var(--card-background);
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 12px 16px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1000;
        }
        .top-header .logo {
            font-size: 18px;
            font-weight: 600;
            color: var(--primary-color);
        }
        .top-header .nav-items {
            display: flex;
            align-items: center;
        }
        .top-header .nav-item {
            color: var(--secondary-text-color);
            text-decoration: none;
            margin-left: 16px;
            font-size: 14px;
            transition: color 0.2s ease;
        }
        .top-header .nav-item.active {
            color: var(--primary-color);
        }
         /* Sidebar button icon (for mobile) */
        .sidebar-btn {
            display: none;
            cursor: pointer;
            background: none;
            border: none;
        }

        .sidebar-btn svg {
            width: 24px;
            height: 24px;
            fill: var(--secondary-text-color);
        }

        /* Sidebar Styles (for mobile) */
        .sidebar {
            position: fixed;
            top: 60px; /* Adjust based on your top header height */
            left: -250px;
            width: 250px;
            height: calc(100% - 60px); /* Adjust based on your top header height */
            background-color: var(--card-background);
            box-shadow: 2px 0 10px rgba(0, 0, 0, 0.1);
            transition: left 0.3s ease;
            z-index: 999;
            overflow-y: auto;
        }

        .sidebar.open {
            left: 0;
        }

        .sidebar .nav-items {
            padding: 16px;
        }

        .sidebar .nav-item {
            display: block;
            color: var(--secondary-text-color);
            text-decoration: none;
            margin-bottom: 12px;
            font-size: 14px;
            transition: color 0.2s ease;
        }

        .sidebar .nav-item.active {
            color: var(--primary-color);
        }

        /* Bottom Navigation Bar Styles */
        .bottom-nav {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            background-color: var(--card-background);
            display: none;
            justify-content: space-around;
            padding: 10px 0;
            box-shadow: 0 -2px 10px rgba(0, 0, 0, 0.1);
            z-index: 1000;
        }

        .bottom-nav .nav-item {
            display: flex;
            flex-direction: column;
            align-items: center;
            color: var(--secondary-text-color);
            text-decoration: none;
            font-size: 12px;
        }

        .bottom-nav .nav-item svg {
            width: 24px;
            height: 24px;
            margin-bottom: 4px;
        }

        .bottom-nav .nav-item.active {
            color: var(--primary-color);
        }

        .bottom-nav .nav-item.active svg {
            fill: var(--primary-color);
        }

        /* Main content container */
        .container {
            padding-top: 72px; /* Adjust based on your top header height */
            padding-left: 16px;
            padding-right: 16px;
            padding-bottom: 16px;
            max-width: 800px;
            margin: 0 auto;
        }

        @media (max-width: 768px) {
            .top-header .nav-items {
                display: none;
            }

            .sidebar-btn {
                display: block;
            }

            .sidebar {
                display: block;
            }

            .bottom-nav {
                display: flex;
            }

            .container {
                padding-bottom: 70px; /* Adjust based on your bottom nav height */
            }
        }

        @media (min-width: 769px) {
            .sidebar-btn {
                display: none;
            }

            .sidebar {
                display: none;
            }

            .bottom-nav {
                display: none;
            }
        }    
    
</style>
</head>
<body>
     <header class="top-header">
        <div class="logo">Live Football Scores</div>
        <button class="sidebar-btn">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <line x1="3" y1="12" x2="21" y2="12"></line>
                <line x1="3" y1="6" x2="21" y2="6"></line>
                <line x1="3" y1="18" x2="21" y2="18"></line>
            </svg>
        </button>
        <nav class="nav-items">
            <a href="index.php" class="nav-item" data-page="home">Home</a>
            <a href="schedule.php" class="nav-item" data-page="schedule">Fixtures</a>
            <a href="sidebar/leagues.php" class="nav-item" data-page="leagues">Leagues</a>
        </nav>
    </header>

    <div class="sidebar">
        <nav class="nav-items">
            <a href="index.php" class="nav-item" data-page="home">Home</a>
            <a href="schedule.php" class="nav-item" data-page="schedule">Fixtures</a>
            <a href="sidebar/leagues.php" class="nav-item" data-page="leagues">Leagues</a>
        </nav>
    </div>

    <nav class="bottom-nav">
        <a href="index.php" class="nav-item" data-page="home">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
                <polyline points="9 22 9 12 15 12 15 22"></polyline>
            </svg>
            Home
        </a>
        <a href="schedule.php" class="nav-item" data-page="schedule">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                <line x1="16" y1="2" x2="16" y2="6"></line>
                <line x1="8" y1="2" x2="8" y2="6"></line>
                <line x1="3" y1="10" x2="21" y2="10"></line>
            </svg>
            Fixtures
        </a>
        <a href="sidebar/leagues.php" class="nav-item" data-page="leagues">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <polygon points="12 2 2 7 12 12 22 7 12 2"></polygon>
                <polyline points="2 17 12 22 22 17"></polyline>
                <polyline points="2 12 12 17 22 12"></polyline>
            </svg>
            Leagues
        </a>
    </nav>
    <div class="container">
        <div class="match-card">
            <div class="match-header">
                <div class="match-tournament">
                    <img src="<?php echo htmlspecialchars($league['logo']); ?>" alt="<?php echo htmlspecialchars($league['name']); ?> logo">
                    <span><?php echo htmlspecialchars($league['name']); ?></span>
                </div>
                <div class="match-status" id="match-status"><?php echo htmlspecialchars($match_status); ?></div>
            </div>
            <div class="match-info">
                <div class="team">
                    <div class="team-logo">
                        <img src="<?php echo htmlspecialchars($home_team['logo']); ?>" alt="<?php echo htmlspecialchars($home_team['name']); ?> logo">
                    </div>
                    <div class="team-name"><?php echo htmlspecialchars($home_team['name']); ?></div>
                    <div class="goal-scorers" id="home-goal-scorers"></div>
                </div>
                <div class="score">
                    <span class="home-score"><?php echo $home_team['score']; ?></span>
                    <span class="score-divider">-</span>
                    <span class="away-score"><?php echo $away_team['score']; ?></span>
                </div>
                <div class="team">
                    <div class="team-logo">
                        <img src="<?php echo htmlspecialchars($away_team['logo']); ?>" alt="<?php echo htmlspecialchars($away_team['name']); ?> logo">
                    </div>
                    <div class="team-name"><?php echo htmlspecialchars($away_team['name']); ?></div>
                    <div class="goal-scorers" id="away-goal-scorers"></div>
                </div>
            </div>
            <div class="button-container">
                <?php if ($status === 'live' || $status === 'upcoming'): ?>
                    <button class="watch-now-btn" onclick="watchStream('<?php echo $status; ?>')">
                        <?php echo $status === 'live' ? 'Watch Now' : 'Upcoming'; ?>
                    </button>
                <?php endif; ?>
            </div>
            <div class="match-details">
                <span id="match-time"></span> | <?php echo htmlspecialchars($venue); ?>
            </div>
        </div>
        
        <div class="tabs">
            <div class="tab active" data-view="lineup-view">Lineup</div>
            <div class="tab" data-view="stats-view">Stats</div>
            <div class="tab" data-view="events-view">Events</div>
            <div class="tab" data-view="commentary-view">Live</div>
        </div>
        
        <div class="tab-content active" id="lineup-view">
            <div class="football-field">
                <!-- Field lines and players will be added here dynamically -->
            </div>
        </div>
        <div class="tab-content" id="stats-view">
            <!-- Stats will be populated by JavaScript -->
        </div>
        <div class="tab-content" id="events-view">
            <!-- Events will be populated by JavaScript -->
        </div>
        <div class="tab-content" id="commentary-view">
            <!-- Commentary will be populated by JavaScript -->
        </div>
    </div>
    
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment-timezone/0.5.33/moment-timezone-with-data.min.js"></script>
    
    <script>
        const matchData = <?php echo $matchJson; ?>;

       function watchStream(status) {
    if (status === 'upcoming') {
        return;
    }
    function urlFriendlyName(name) {
        return name.toLowerCase().replace(/[^a-zA-Z0-9]+/g, '-');
    }
    const homeTeam = urlFriendlyName(matchData.home_team.name);
    const awayTeam = urlFriendlyName(matchData.away_team.name);
    const leagueName = urlFriendlyName(matchData.league.name);
    const matchId = matchData.id;
    const url = `${homeTeam}-vs-${awayTeam}-${matchId}-stream.php`;
    console.log("Redirecting to:", url); // For debugging
    window.location.href = url;
}
const navItems = document.querySelectorAll('.nav-item');
        navItems.forEach(item => {
            item.addEventListener('click', () => {
                navItems.forEach(i => i.classList.remove('active'));
                item.classList.add('active');
            });
        });

        // Sidebar toggle script
        const sidebar = document.querySelector('.sidebar');
        const sidebarToggle = document.querySelector('.sidebar-btn');

        sidebarToggle.addEventListener('click', () => {
            sidebar.classList.toggle('open');
        });

        // Set active nav item based on current page
        document.addEventListener('DOMContentLoaded', () => {
            const currentPage = window.location.pathname.split('/').pop().split('.')[0];
            const activeNavItem = document.querySelector(`.nav-item[data-page="${currentPage}"]`);
            if (activeNavItem) {
                activeNavItem.classList.add('active');
            }
        });
        function updateMatchInfo() {
            const userTimezone = Intl.DateTimeFormat().resolvedOptions().timeZone;
            const matchMoment = moment.unix(matchData.match_time_utc);
            
            document.getElementById('match-time').textContent = matchMoment.tz(userTimezone).format('MMMM D, YYYY HH:mm');
            
            document.querySelector('.home-score').textContent = matchData.home_team.score;
            document.querySelector('.away-score').textContent = matchData.away_team.score;
            
            if (matchData.status === 'live') {
                const minutesPlayed = moment().diff(matchMoment, 'minutes');
                document.getElementById('match-status').textContent = minutesPlayed + "'";
            } else {
                document.getElementById('match-status').textContent = matchData.match_status;
            }

            updateGoalScorers('home-goal-scorers', matchData.home_team.goalScorers);
            updateGoalScorers('away-goal-scorers', matchData.away_team.goalScorers);
        }

        function updateGoalScorers(elementId, goalScorers) {
            const element = document.getElementById(elementId);
            if (goalScorers.length > 0) {
                const scorersText = goalScorers.map(scorer => `${scorer.name.split(' ').pop()} ${scorer.time}'`).join(', ');
                element.textContent = scorersText;
                element.title = scorersText;
            } else {
                element.textContent = '';
                element.title = '';
            }
        }


function populateLineup() {
        const field = document.querySelector('.football-field');
        field.innerHTML = `
            <div class="field-line center-circle"></div>
            <div class="field-line halfway-line"></div>
            <div class="field-line penalty-area home"></div>
            <div class="field-line penalty-area away"></div>
            <div class="field-line goal-area home"></div>
            <div class="field-line goal-area away"></div>
        `;

        const homeTeamId = matchData.formations.find(f => f.location === 'home').participant_id;
        const homeFormation = matchData.formations.find(f => f.location === 'home').formation.split('-').map(Number);
        const awayFormation = matchData.formations.find(f => f.location === 'away').formation.split('-').map(Number);

        function createPlayerElement(player, isHome) {
            const playerEl = document.createElement('div');
            playerEl.className = `player ${isHome ? 'home' : 'away'}`;
            playerEl.innerHTML = `
                <div class="player-circle">${player.jersey_number}</div>
                <div class="player-name">${player.player_name.split(' ').pop()}</div>
            `;
            return playerEl;
        }

        function positionPlayers(formation, players, isHome) {
            const yBase = isHome ? 95 : 5;
            const yDirection = isHome ? -1 : 1;
            let currentIndex = 0;

            // Add goalkeeper
            const goalkeeper = players.find(p => p.position_id === 1);
            if (goalkeeper) {
                const goalkeeperEl = createPlayerElement(goalkeeper, isHome);
                goalkeeperEl.style.left = '50%';
                goalkeeperEl.style.top = `${isHome ? 98 : 2}%`;
                field.appendChild(goalkeeperEl);
            }

            // Position outfield players
            formation.forEach((playersInLine, lineIndex) => {
                const y = yBase + yDirection * (85 / (formation.length + 1) * (lineIndex + 1));
                for (let i = 0; i < playersInLine; i++) {
                    const player = players[currentIndex];
                    if (player && player.position_id !== 1) {  // Exclude goalkeeper
                        const playerEl = createPlayerElement(player, isHome);
                        const x = 100 / (playersInLine + 1) * (i + 1);
                        playerEl.style.left = `${x}%`;
                        playerEl.style.top = `${y}%`;
                        field.appendChild(playerEl);
                        currentIndex++;
                    }
                }
            });
        }

        const homePlayers = matchData.lineups.filter(p => p.team_id === homeTeamId).sort((a, b) => a.formation_position - b.formation_position);
        const awayPlayers = matchData.lineups.filter(p => p.team_id !== homeTeamId).sort((a, b) => a.formation_position - b.formation_position);

        positionPlayers(homeFormation, homePlayers, true);
        positionPlayers(awayFormation, awayPlayers, false);
    }

    function adjustPlayerSize() {
        const field = document.querySelector('.football-field');
        const fieldWidth = field.offsetWidth;
        const players = document.querySelectorAll('.player');
        const playerSize = Math.max(30, Math.min(40, fieldWidth * 0.09));
        players.forEach(player => {
            player.style.width = `${playerSize}px`;
            player.style.height = `${playerSize}px`;
            player.style.fontSize = `${playerSize * 0.3}px`;
        });
        const playerCircles = document.querySelectorAll('.player-circle');
        playerCircles.forEach(circle => {
            circle.style.width = `${playerSize * 0.7}px`;
            circle.style.height = `${playerSize * 0.7}px`;
            circle.style.fontSize = `${playerSize * 0.35}px`;
        });
    }

    // Call these functions when the DOM is loaded and on window resize
    document.addEventListener('DOMContentLoaded', () => {
        if (typeof matchData !== 'undefined') {
            populateLineup();
            adjustPlayerSize();
        }
    });
    window.addEventListener('resize', adjustPlayerSize);
    
function populateStatistics() {
    const statsView = document.getElementById('stats-view');
    statsView.innerHTML = '';
    
    if (matchData.statistics && matchData.statistics.length > 0) {
        const statsContainer = document.createElement('div');
        statsContainer.className = 'stats-container';
        
        const statTypes = {
            'Ball Possession': [45],
            'Shots Total': [42],
            'Shots On Target': [86],
            'Shots Off Target': [41],
            'Shots Blocked': [58],
            'Shots Inside Box': [49],
            'Shots Outside Box': [50],
            'Fouls': [56],
            'Corner Kicks': [34],
            'Offsides': [37],
            'Yellow Cards': [84],
            'Red Cards': [83],
            'Goalkeeper Saves': [57],
            'Total Passes': [80],
            'Successful Passes': [81],
            'Attacks': [43],
            'Dangerous Attacks': [60],
            'Tackles': [78],
            'Interceptions': [100],
            'Accurate Crosses': [99]
        };
        
        Object.entries(statTypes).forEach(([statName, typeIds]) => {
            const homeStat = matchData.statistics.find(s => typeIds.includes(s.type_id) && s.location === 'home');
            const awayStat = matchData.statistics.find(s => typeIds.includes(s.type_id) && s.location === 'away');
            
            if (homeStat || awayStat) {
                const homeValue = homeStat ? parseFloat(homeStat.data.value) : 0;
                const awayValue = awayStat ? parseFloat(awayStat.data.value) : 0;
                const totalValue = homeValue + awayValue;
                
                let homePercentage, awayPercentage;
                
                if (statName === 'Ball Possession') {
                    homePercentage = homeValue;
                    awayPercentage = 100 - homeValue;
                } else {
                    homePercentage = totalValue > 0 ? (homeValue / totalValue) * 100 : 50;
                    awayPercentage = totalValue > 0 ? (awayValue / totalValue) * 100 : 50;
                }

                const statItem = document.createElement('div');
                statItem.className = 'stat-item';
                statItem.innerHTML = `
                    <div class="stat-title">${statName}</div>
                    <div class="stat-bar">
                        <div class="stat-value home-stat" style="width: ${homePercentage}%"></div>
                        <div class="stat-value away-stat" style="width: ${awayPercentage}%"></div>
                    </div>
                    <div class="stat-numbers">
                        <span>${statName === 'Ball Possession' ? homeValue + '%' : homeValue}</span>
                        <span>${statName === 'Ball Possession' ? awayValue + '%' : awayValue}</span>
                    </div>
                `;
                statsContainer.appendChild(statItem);
            }
        });
        
        statsView.appendChild(statsContainer);
    } else {
        statsView.innerHTML = '<p>No statistics available for this match.</p>';
    }
}

function populateEvents() {
    const eventsView = document.getElementById('events-view');
    eventsView.innerHTML = '';
    
    if (matchData.events && matchData.events.length > 0) {
        const eventList = document.createElement('ul');
        matchData.events.forEach(event => {
            let eventEmoji = '';
            let eventType = '';
            switch(event.type_id) {
                case 14:
                    eventEmoji = '⚽️';
                    eventType = 'Goal';
                    break;
                case 18:
                    eventEmoji = '🔄';
                    eventType = 'Substitution';
                    break;
                case 19:
                    eventEmoji = '🟨';
                    eventType = 'Yellow Card';
                    break;
                case 83:
                    eventEmoji = '🟥';
                    eventType = 'Red Card';
                    break;
                default:
                    eventEmoji = '📝';
                    eventType = 'Event';
            }
            
            eventList.innerHTML += `
                <li>
                    <strong>${event.minute}'</strong> ${eventEmoji} 
                    ${eventType}: ${event.player_name} 
                    ${event.related_player_name ? `(${event.related_player_name})` : ''}
                </li>
            `;
        });
        eventsView.appendChild(eventList);
    } else {
        eventsView.innerHTML = '<p>No events available for this match.</p>';
    }
}

function populateCommentary() {
    const commentaryView = document.getElementById('commentary-view');
    commentaryView.innerHTML = '';
    
    if (matchData.commentary && matchData.commentary.length > 0) {
        const commentaryList = document.createElement('ul');
        matchData.commentary.forEach(comment => {
            commentaryList.innerHTML += `
                <li>
                    <strong>${comment.minute}'</strong> - ${comment.comment}
                </li>
            `;
        });
        commentaryView.appendChild(commentaryList);
    } else {
        commentaryView.innerHTML = '<p>No commentary available for this match.</p>';
    }
}

// Initial population of all sections
updateMatchInfo();
populateLineup();
populateStatistics();
populateEvents();
populateCommentary();

// Update match info every minute
setInterval(updateMatchInfo, 60000);

// Tab switching functionality
document.querySelectorAll('.tab').forEach(tab => {
    tab.addEventListener('click', function() {
        document.querySelectorAll('.tab').forEach(t => t.classList.remove('active'));
        document.querySelectorAll('.tab-content').forEach(v => v.classList.remove('active'));
        
        this.classList.add('active');
        const viewId = this.getAttribute('data-view');
        document.getElementById(viewId).classList.add('active');
    });
});

// Responsive adjustment for player size and positioning
window.addEventListener('resize', adjustPlayerSize);
document.addEventListener('DOMContentLoaded', () => {
    if (typeof matchData !== 'undefined') {
        populateLineup();
        adjustPlayerSize();
    }
});
        
    </script>

</body>
</html>