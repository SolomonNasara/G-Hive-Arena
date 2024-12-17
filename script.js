document.addEventListener('DOMContentLoaded', () => {
    // Countdown Timer
    const timer = document.getElementById('timer');
    if (timer) {
        const countdownDate = new Date("2024-12-25T19:00:00Z").getTime();  // Target date: 25th Dec 2024, 7 PM UTC

        // Function to update the countdown every second
        const countdownInterval = setInterval(() => {
            const now = new Date().getTime();  // Current time
            const distance = countdownDate - now;  // Time remaining until target date

            const days = Math.floor(distance / (1000 * 60 * 60 * 24));
            const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
            const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
            const seconds = Math.floor((distance % (1000 * 60)) / 1000);

            // Update the countdown display
            timer.innerHTML = `${days}d ${hours}h ${minutes}m ${seconds}s`;

            // When countdown is finished
            if (distance < 0) {
                clearInterval(countdownInterval);
                timer.innerHTML = "EXPIRED";
            }
        }, 1000);
    }

    // Graphical Device Performance Test
    const rating = document.getElementById('rating');
    if (rating) {
        const progressBar = document.createElement('div');
        progressBar.style.width = '100%';
        progressBar.style.height = '20px';
        progressBar.style.backgroundColor = '#ddd';
        rating.appendChild(progressBar);

        const progress = document.createElement('div');
        progress.style.width = '0%';
        progress.style.height = '100%';
        progress.style.backgroundColor = '#e63946';
        progressBar.appendChild(progress);

        function getPerformanceRating(score) {
            if (score > 10) {
                return "Excellent";
            } else if (score > 5) {
                return "Good";
            } else if (score > 2) {
                return "Fair";
            } else {
                return "Poor";
            }
        }

        function startPerformanceTest() {
            let progressValue = 0;
            const progressInterval = setInterval(() => {
                progressValue += 10;
                progress.style.width = `${progressValue}%`;

                if (progressValue >= 100) {
                    clearInterval(progressInterval);
                    const performanceScore = Math.random() * 15; // Simulate a random performance score
                    const performanceRatingText = getPerformanceRating(performanceScore);
                    rating.innerHTML = `Your device performance rating: ${performanceRatingText}`;
                }
            }, 300); // Adjust the duration as needed for the test delay
        }

        setTimeout(startPerformanceTest, 2000); // 2-second delay before starting the performance test
    }

    // Join Form and User ID
    const joinForm = document.getElementById('join-form');
    const arenaIDElement = document.getElementById('arena-id');
    const joinButton = document.getElementById('join-button');  // Get the button by its ID

    // Arena pop-up elements
    const arenaPopup = document.getElementById('arena-popup');
    const arenaIdValue = document.getElementById('arena-id-value');
    const entrantPositionValue = document.getElementById('entrant-position-value');
    const closePopupButton = document.getElementById('close-popup');

    if (joinForm && joinButton) {
        joinButton.addEventListener('click', (e) => {
            e.preventDefault(); // Prevent the button from doing a default action (like submitting the form)

            const name = document.getElementById('name').value;
            const email = document.getElementById('email').value;

            // Validate the fields before submitting
            if (!name || !email) {
                alert("Please fill in both username and email.");
                return;
            }

            const formData = new FormData();
            formData.append("name", name);
            formData.append("email", email);

            // Send the form data to 'data.php' via fetch API
            fetch(joinForm.action, {
                method: 'POST',
                body: formData // Send the form data
            })
            .then(response => response.json()) // Parse response as JSON
            .then(data => {
                console.log(data); // Log the response for debugging

                if (data.status === 'success') {
                    const arenaID = data.arenaID;
                    const totalEntrants = data.totalEntrants;

                    // Update Arena ID and total entrants
                    arenaIDElement.textContent = `Your Arena ID: ${arenaID}`;
                    document.getElementById('total-joins').textContent = `Total Entrants: ${totalEntrants}`;

                    // Update the Arena ID and Entrant Position below the "We’re excited..." message
                    document.getElementById('arena-id').textContent = `Your Arena ID: ${arenaID}`;
                    document.getElementById('entrant-position').textContent = `You are entrant number: ${totalEntrants}`;

                    // Update and show the pop-up with Arena ID and Entrant Position
                    arenaIdValue.textContent = arenaID;
                    entrantPositionValue.textContent = totalEntrants;

                    // Display the pop-up
                    arenaPopup.classList.remove('hidden');
                } else {
                    alert(data.message || 'There was an error joining the arena.'); // Show error message if not successful
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('There was an error processing your request.');
            });
        });
    }

    // Close the pop-up when the close button is clicked
    if (closePopupButton) {
        closePopupButton.addEventListener('click', () => {
            arenaPopup.classList.add('hidden');
        });
    }

    // Example function to add team stats (you can customize this)
    const teamStats = [
        { name: "Team Alpha", stats: "Wins: 10, Losses: 2", power: "High" },
        { name: "Team Beta", stats: "Wins: 8, Losses: 4", power: "Medium" }
    ];

    const teamsSection = document.getElementById('teams');
    if (teamsSection) {
        teamStats.forEach(team => {
            const teamDiv = document.createElement('div');
            teamDiv.classList.add('team');
            teamDiv.innerHTML = `<h3>${team.name}</h3><p>${team.stats}</p><p>Power: ${team.power}</p>`;
            teamsSection.appendChild(teamDiv);
        });
    }

    // Fetch the total number of entrants from the PHP file
    fetch('getTotalEntrants.php')
        .then(response => response.json())
        .then(data => {
            const totalEntrants = data.totalEntrants;
            document.getElementById('total-joins').textContent = `Total Entrants: ${totalEntrants}`;
        })
        .catch(error => {
            console.error('Error fetching total entrants:', error);
            document.getElementById('total-joins').textContent = 'Error loading data';
        });

    // Show/Hide Team Stats Details
    const detailsBtns = document.querySelectorAll('.see-details-btn');
    detailsBtns.forEach(btn => {
        btn.addEventListener('click', (e) => {
            const details = e.target.nextElementSibling;
            details.classList.toggle('hidden');
            console.log('Toggled visibility of team details');
        });
    });
});
