//index.js
function sportsApp() {
    return {
        liveMatches: [],
        upcomingMatches: [],
        finishedMatches: [],
        errorMessage: '',
        userTimezone: Intl.DateTimeFormat().resolvedOptions().timeZone,

        init() {
            this.updateMatches();
            setInterval(() => this.updateMatches(), 60000); // Update matches every minute
            setInterval(() => this.updateLiveTimers(), 1000); // Update live match timers every second
        },

        updateMatches() {
            fetch(`fetch.php?timezone=${encodeURIComponent(this.userTimezone)}`, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                this.liveMatches = data.live_matches || [];
                this.upcomingMatches = data.upcoming_matches || [];
                this.finishedMatches = data.finished_matches || [];
                this.errorMessage = '';
            })
            .catch(error => {
                console.error('Error fetching matches:', error);
                this.errorMessage = 'Error fetching matches. Please try again later.';
            });
        },

        updateLiveTimers() {
            this.liveMatches = this.liveMatches.map(match => {
                if (match.status === 'live') {
                    const startTime = moment(match.starting_at);
                    const currentTime = moment();
                    const minutesPlayed = currentTime.diff(startTime, 'minutes');
                    match.minutes_played = minutesPlayed;
                    match.match_time = this.formatMatchTime(minutesPlayed);
                }
                return match;
            });
        },

        formatMatchTime(minutesPlayed) {
            if (minutesPlayed <= 45) {
                return `${minutesPlayed}'`;
            } else if (minutesPlayed > 45 && minutesPlayed <= 60) {
                return "HT";
            } else if (minutesPlayed > 60 && minutesPlayed <= 105) {
                return `${minutesPlayed - 15}'`;
            } else {
                return "90+'";
            }
        },


        createMatchCard(match, isLive) {
            const sanitizeFilename = (name) => {
                return name.toLowerCase()
                    .normalize('NFD').replace(/[\u0300-\u036f]/g, '') // Remove diacritics
                    .replace(/[^a-z0-9]+/g, '-') // Replace non-alphanumeric characters with hyphens
                    .trim('-');
            };

            const leagueName = sanitizeFilename(match.league_name);
            const homeTeam = sanitizeFilename(match.home_team.name);
            const awayTeam = sanitizeFilename(match.away_team.name);
            const matchId = match.id;
            const matchUrl = `leagues/${leagueName}/${homeTeam}-vs-${awayTeam}-${matchId}.php`;

            const matchDate = moment.tz(match.starting_at, this.userTimezone);
            const formattedDate = matchDate.format('ddd, MMM D');
            const formattedTime = matchDate.format('h:mm A');

            const matchTime = isLive ? match.match_time : '';

            return `
                <div class="match-card" onclick="window.location.href='${matchUrl}'">
                    <div class="match-header">
                        <div class="league-name">${match.league_name}</div>
                        <div class="match-time">${formattedTime}</div>
                    </div>
                    <div class="match-date-time">${formattedDate}</div>
                    <div class="match-teams">
                        <div class="team home-team">
                            <div class="team-name">${match.home_team.name}</div>
                            <img src="${match.home_team.logo}" alt="${match.home_team.name}" class="team-logo">
                        </div>
                        <div class="score-container">
                            ${isLive || match.status === 'finished' ? 
                                `<div class="score">
                                    <div class="team-score">${match.home_team.score !== null ? match.home_team.score : '-'}</div>
                                    <div class="score-divider">-</div>
                                    <div class="team-score">${match.away_team.score !== null ? match.away_team.score : '-'}</div>
                                </div>
                                ${isLive ? `<div class="match-timer">${matchTime}</div>` : ''}` : 
                                `<div class="vs">vs</div>`
                            }
                            <div class="match-status ${match.status}">
                                ${match.status}
                            </div>
                        </div>
                        <div class="team away-team">
                            <img src="${match.away_team.logo}" alt="${match.away_team.name}" class="team-logo">
                            <div class="team-name">${match.away_team.name}</div>
                        </div>
                    </div>
                </div>
            `;
        }
    }
}

// Navigation and sidebar functionality
document.addEventListener('DOMContentLoaded', () => {
    const navItems = document.querySelectorAll('.nav-item');
    navItems.forEach(item => {
        item.addEventListener('click', () => {
            navItems.forEach(i => i.classList.remove('active'));
            item.classList.add('active');
        });
    });

    const sidebar = document.querySelector('.sidebar');
    const sidebarToggle = document.querySelector('.sidebar-btn');

    sidebarToggle.addEventListener('click', () => {
        sidebar.classList.toggle('open');
    });
});
