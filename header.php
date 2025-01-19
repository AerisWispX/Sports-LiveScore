<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Live Football Scores</title>
    <style>
        :root {
            --primary-color: #9b87f5;
            --secondary-color: #718096;
            --background-color: #0a0c10;
            --card-background: #1a1e24;
            --text-color: #e2e8f0;
            --secondary-text-color: #a0aec0;
        }

        body, html {
            margin: 0;
            padding: 0;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen-Sans, Ubuntu, Cantarell, 'Helvetica Neue', sans-serif;
            background-color: var(--background-color);
            color: var(--text-color);
            font-size: 14px;
            line-height: 1.5;
        }

        /* Top Header Styles */
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

        /* Add these new styles for better iOS compatibility */
        @supports (-webkit-touch-callout: none) {
            .bottom-nav {
                padding-bottom: calc(10px + env(safe-area-inset-bottom));
            }
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
            padding-bottom: 70px; /* Adjust based on your bottom nav height */
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

    <script>
        // Navigation bar script
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
    </script>
</body>
</html>