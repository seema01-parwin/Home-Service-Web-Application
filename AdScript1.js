// script.js

// Function to fetch booking data and render the chart
async function renderBookingChart() {
    try {
        const response = await fetch('GetBookingData1.php');
        const data = await response.json();

        // Extract labels and data from the response
        const labels = data.map(item => item.month);
        const counts = data.map(item => item.count);

        // Get the context of the canvas element
        const ctx = document.getElementById('bookingChart').getContext('2d');

        // Create the chart
        const bookingChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Monthly Bookings',
                    data: counts,
                    backgroundColor: 'rgba(125, 95, 255, 0.2)',
                    borderColor: '#7d5fff',
                    borderWidth: 2,
                    tension: 0.3,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                scales: {
                    x: {
                        title: {
                            display: true,
                            text: 'Month'
                        }
                    },
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Number of Bookings'
                        }
                    }
                }
            }
        });
    } catch (error) {
        console.error('Error fetching booking data:', error);
    }
}

// Call the function to render the chart when the page loads
document.addEventListener('DOMContentLoaded', renderBookingChart);
