<?php //index.php ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Live Sports Scores</title>
    <link rel="stylesheet" href="assets/index.css">
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment-timezone/0.5.34/moment-timezone-with-data.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment-timezone/0.5.33/moment-timezone-with-data.min.js"></script>
<script src="js/index.js"></script>
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
        <div class="nav-items">
            <a href="index.php" class="nav-item" data-page="home">Home</a>
            <a href="schedule.php" class="nav-item" data-page="schedule">Fixtures</a>
            <a href="sidebar/leagues.php" class="nav-item" data-page="leagues">Leagues</a>
        </div>
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

   <div class="container" x-data="sportsApp()">
        <div id="live-matches-container">
<h2><span>Live Matches</span></h2>
            <div id="live-matches-list">
                <template x-if="liveMatches.length > 0">
                    <template x-for="match in liveMatches" :key="match.id">
                        <div x-html="createMatchCard(match, true)"></div>
                    </template>
                </template>
                <template x-if="liveMatches.length === 0">
                    <div class="no-matches">No live matches at the moment</div>
                </template>
            </div>
        </div>

        <div id="upcoming-matches-container">
            <h2>Upcoming Matches</h2>
            <div id="upcoming-matches-list">
                <template x-if="upcomingMatches.length > 0">
                    <template x-for="match in upcomingMatches" :key="match.id">
                        <div x-html="createMatchCard(match, false)"></div>
                    </template>
                </template>
                <template x-if="upcomingMatches.length === 0">
                    <div class="no-matches">No upcoming matches today</div>
                </template>
            </div>
        </div>

        <div id="finished-matches-container">
            <h2>Finished Matches</h2>
            <div id="finished-matches-list">
                <template x-if="finishedMatches.length > 0">
                    <template x-for="match in finishedMatches" :key="match.id">
                        <div x-html="createMatchCard(match, false)"></div>
                    </template>
                </template>
                <template x-if="finishedMatches.length === 0">
                    <div class="no-matches">No finished matches today</div>
                </template>
            </div>
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