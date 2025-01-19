
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