document.addEventListener('DOMContentLoaded', (event) => {
    // Function to fetch the status of kiosks
    async function fetchStatus() {
        try {
            // Fetch the status file
            const response = await fetch('status.txt');
            const statusData = await response.json();

            // Update the status on the web page
            updateStatus(statusData);
        } catch (error) {
            console.error('Error fetching status:', error);
        }
    }

    // Function to update the status on the web page
    function updateStatus(statusData) {
        const statusContainer = document.getElementById('quioscos-status');
        statusContainer.innerHTML = '';

        // Iterate over the status data and create elements for each kiosk
        for (const [kioskName, status] of Object.entries(statusData)) {
            const kioskElement = document.createElement('div');
            kioskElement.className = 'kiosk-status';
            kioskElement.innerHTML = `<strong>${kioskName}</strong>: <span class="${status.toLowerCase()}">${status}</span>`;
            statusContainer.appendChild(kioskElement);
        }

        // Update the last check time
        const lastCheckTime = new Date().toLocaleString();
        document.getElementById('last-check-time').innerText = `Última comprobación: ${lastCheckTime}`;
    }

    // Fetch the status every 60 seconds
    setInterval(fetchStatus, 60000);

    // Initial fetch
    fetchStatus();
});
