<?php require_once 'config.php'; 
//schedule.php
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Match Schedule</title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/alpinejs/3.10.2/cdn.min.js" defer></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment-timezone/0.5.33/moment-timezone-with-data.min.js"></script>
assets/schedule.css
<script src="js/schedule.js"></script>
<link rel="stylesheet" href="assets/schedule.css">
</head>
<body>
    <?php require_once 'header.php'; ?>
    <div class="container" x-data="scheduleApp()">
        <div class="schedule-header">
            <div class="date-nav">
                <button id="prev-date" @click="prevDate">&lt; Previous</button>
                <div class="date-display" id="current-date" x-text="formatCurrentDate"></div>
                <button id="next-date" @click="nextDate">Next &gt;</button>
            </div>
        </div>
        
        <div id="error-container" x-show="error" x-text="error" class="error-message"></div>
        
        <div id="matches-container">
            <h2><span>Scheduled Matches</span></h2>
            <div id="matches-list">
                <template x-if="matches.length === 0">
                    <p class="no-matches">No matches scheduled for this date.</p>
                </template>
                <template x-for="match in matches" :key="match.id">
                    <div class="match-card" @click="goToMatchPage(match)">
                        <div class="match-header">
                            <div class="league-name" x-text="match.league_name"></div>
                            <div class="match-time" x-text="formatTime(match.starting_at)"></div>
                        </div>
                        <div class="match-date-time" x-text="formatDateTime(match.starting_at)"></div>
                        <div class="match-teams">
                            <div class="team home-team">
                                <div class="team-name" x-text="match.home_team.name"></div>
                                <img :src="match.home_team.logo" :alt="match.home_team.name" class="team-logo">
                            </div>
                            <div class="score-container">
                                <template x-if="match.status === 'finished'">
                                    <div class="score">
                                        <div class="team-score" x-text="match.home_team.score"></div>
                                        <div class="score-divider">-</div>
                                        <div class="team-score" x-text="match.away_team.score"></div>
                                    </div>
                                </template>
                                <template x-if="match.status !== 'finished'">
                                    <div class="vs">vs</div>
                                </template>
                                <div class="match-status" :class="match.status" x-text="match.status"></div>
                            </div>
                            <div class="team away-team">
                                <img :src="match.away_team.logo" :alt="match.away_team.name" class="team-logo">
                                <div class="team-name" x-text="match.away_team.name"></div>
                            </div>
                        </div>
                    </div>
                </template>
            </div>
        </div>
        
        <div class="pagination">
            <button @click="loadPreviousPage" :disabled="currentPage === 1">&lt; Previous</button>
            <span class="pagination-info" x-text="`Page ${currentPage} of ${totalPages}`"></span>
            <button @click="loadNextPage" :disabled="currentPage === totalPages">Next &gt;</button>
        </div>
    </div>
<?php include 'footer.php'; ?>

<footer class="site-footer">
    <div class="site-footer-content">
        <div class="site-footer-section">
            <h3>About Us</h3>
            <p>Live Football Scores brings you real-time updates from matches around the world.</p>
        </div>
        <div class="site-footer-section">
            <h3>Quick Links</h3>
            <ul>
                <li><a href="index.php">Home</a></li>
                <li><a href="schedule.php">Fixtures</a></li>
                <li><a href="sidebar/leagues.php">Leagues</a></li>
            </ul>
        </div>
        <div class="site-footer-section">
            <h3>Follow Us</h3>
            <div class="site-footer-social-icons">
                <a href="#" class="site-footer-social-icon" aria-label="Facebook">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z"></path>
                    </svg>
                </a>
                <a href="#" class="site-footer-social-icon" aria-label="Twitter">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M23 3a10.9 10.9 0 0 1-3.14 1.53 4.48 4.48 0 0 0-7.86 3v1A10.66 10.66 0 0 1 3 4s-4 9 5 13a11.64 11.64 0 0 1-7 2c9 5 20 0 20-11.5a4.5 4.5 0 0 0-.08-.83A7.72 7.72 0 0 0 23 3z"></path>
                    </svg>
                </a>
                <a href="#" class="site-footer-social-icon" aria-label="Instagram">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="2" y="2" width="20" height="20" rx="5" ry="5"></rect>
                        <path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"></path>
                        <line x1="17.5" y1="6.5" x2="17.51" y2="6.5"></line>
                    </svg>
                </a>
            </div>
        </div>
    </div>
    <div class="site-footer-bottom">
        <p>&copy; <?php echo date("Y"); ?> Live Football Scores. All rights reserved.</p>
    </div>
</footer>
</body>
</html>