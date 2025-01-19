    function scheduleApp() {
        return {
            currentDate: new Date(),
            matches: [],
            error: null,
            userTimezone: moment.tz.guess(),
            currentPage: 1,
            totalPages: 25,

            init() {
                this.updateSchedule();
            },

            formatCurrentDate() {
                return moment(this.currentDate).format('MMMM D, YYYY');
            },

            formatDate(date) {
                return moment(date).format('YYYY-MM-DD');
            },

            formatTime(dateTime) {
                return moment.tz(dateTime, this.userTimezone).format('HH:mm');
            },

            formatDateTime(dateTime) {
                return moment.tz(dateTime, this.userTimezone).format('ddd, MMM D HH:mm');
            },

            prevDate() {
                this.currentDate = moment(this.currentDate).subtract(1, 'days').toDate();
                this.currentPage = 1;
                this.updateSchedule();
            },

            nextDate() {
                this.currentDate = moment(this.currentDate).add(1, 'days').toDate();
                this.currentPage = 1;
                this.updateSchedule();
            },

            updateSchedule() {
                fetch(`get_schedule.php?date=${this.formatDate(this.currentDate)}&timezone=${this.userTimezone}&page=${this.currentPage}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.error) {
                            this.error = `Error: ${data.error}`;
                            this.matches = [];
                        } else {
                            this.error = null;
                            this.matches = data.data || [];
                            this.currentPage = data.meta?.current_page || 1;
                            this.totalPages = data.meta?.total_pages || 25;
                        }
                    })
                    .catch(error => {
                        console.error('Fetch error:', error);
                        this.error = 'Error fetching match data. Please try again later.';
                        this.matches = [];
                    });
            },

            goToMatchPage(match) {
            const sanitizeFilename = (name) => {
                return name.toLowerCase()
                    .normalize("NFD").replace(/[\u0300-\u036f]/g, "") // Remove accents
                    .replace(/[^a-z0-9]+/g, '-')
                    .replace(/^-+|-+$/g, ''); // Trim hyphens from start and end
            };

            const leagueName = sanitizeFilename(match.league_name);
            const homeTeam = sanitizeFilename(match.home_team.name);
            const awayTeam = sanitizeFilename(match.away_team.name);
            const matchId = match.id;

            window.location.href = `leagues/${leagueName}/${homeTeam}-vs-${awayTeam}-${matchId}.php`;
        },

            loadNextPage() {
                if (this.currentPage < this.totalPages) {
                    this.currentPage++;
                    this.updateSchedule();
                }
            },

            loadPreviousPage() {
                if (this.currentPage > 1) {
                    this.currentPage--;
                    this.updateSchedule();
                }
            }
        }
    }