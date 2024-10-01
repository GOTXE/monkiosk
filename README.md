# Network Monitoring Web Application

This repository contains a web-based network monitoring system designed to monitor the status of various kiosks and a local server within a LAN. The solution combines a Bash script that checks the network connectivity of devices and a web interface that visually presents their online/offline status in real-time.

## Table of Contents

- [Overview](#overview)
- [Folder Structure](#folder-structure)
- [Technologies Used](#technologies-used)
- [Installation](#installation)
- [How It Works](#how-it-works)
- [Screenshots](#screenshots)
- [Customization](#customization)
- [License](#license)
- [Image Credits](#image-credits)

## Overview

This project is designed to run on a Linux server using Nginx as the web server. The system performs network checks using a Bash script to determine the status of devices in the local network (referred to as kiosks) and presents the results on a user-friendly web page. The system operates with minimal human interaction, making it suitable for environments like kiosks where manual input is limited or non-existent.

### Key Features:

- Monitors the status of multiple kiosks and a local server.
- Displays the results on a dynamic web interface using HTML, CSS, and JavaScript.
- Updates device statuses every 60 seconds without refreshing the entire page.
- Provides real-time online/offline status indicators for each kiosk.


## Folder Structure

Here’s the complete folder structure for the network monitoring system, including file types and suggested file permissions:

```plaintext

/opt/monitoring/                    # Directory for the monitoring script
  ├── monitor.sh                      # Bash script for monitoring devices (Permissions: 755)
/etc/systemd/system/                # Directory for systemd service configuration
  ├── monitoring.service              # Systemd service file to run monitor.sh (Permissions: 644)
/var/www/html/monkiosk/             # Root directory for the web interface (Permissions: 755)
  ├── index.html                      # Main HTML file for the web interface (Permissions: 644)
  ├── script.js                       # JavaScript file for fetching and displaying statuses (Permissions: 644)
  ├── styles.css                      # CSS file for styling the web page (Permissions: 644)
  ├── status.txt                      # Generated status file with server and kiosk states (Permissions: 644)
  ├── ips.txt                         # List of kiosk IPs and names (Permissions: 644)
  ├── img/                            # Directory for images used in the web interface
  │   ├── server.svg                  # SVG icon for the server (Permissions: 644)
  │   ├── quiosco.svg                 # SVG icon for kiosks (Permissions: 644)
/var/log/monitoring/                # Log directory for the monitoring system (Permissions: 755)
  ├── monitor.log                     # Log file for monitoring results (Permissions: 644)
```

## Technologies Used

- **Bash**: For performing network checks (ping and HTTP).
- **HTML/CSS/JavaScript**: For the web interface.
- **Nginx**: As the web server.
- **Curl**: To verify server availability.
- **Ping**: To check connectivity of kiosks.

## Installation

1. **Clone the Repository**:
    ```bash
    git clone https://github.com/GOTXE/network-monitoring.git
    ```

2. **Install Required Packages**:
    Make sure your system has Nginx installed. You can use:
    ```bash
    sudo apt update
    sudo apt install nginx curl
    ```

3. **Set Up the Web Interface**:
    - Copy the contents of the `/www/html` folder to your Nginx root directory (typically `/var/www/html/`).
    - Make sure Nginx is configured to serve the `index.html` file.

4. **Configure the Monitoring Script**:
    - Place `monitor.sh` in a suitable location on your server.
    - Set up a cron job or systemd service to run `monitor.sh` every minute.

5. **Configure IPs**:
    In the `ips.txt` file, add the IPs and corresponding names for each kiosk in the following format:
    ```
    192.168.1.2    Kiosk1
    192.168.1.3    Kiosk2
    ```

## How It Works


# Monitoring Service Setup

To ensure that the monitoring script runs as a background service and restarts automatically if it fails, you need to create a `systemd` service unit.

## Steps to Add `monitoring.service` to `systemd`

1. **Create the service file**

   First, create a new `service` file for the monitoring script:
   
   ```bash
   sudo nano /etc/systemd/system/monitoring.service
2. **Reload systemd to recognize the new service:**

    After saving the service file, reload the systemd manager configuration to apply the changes:
    ```
    sudo systemctl daemon-reload
    ```
3. **Enable the service to start on boot:**

    To ensure that the monitoring service starts automatically on system boot, enable the service:
    ```
    sudo systemctl enable monitoring.service
    ```
4. **Start the service:**

    Now, start the service manually:
    ```
    sudo systemctl start monitoring.service
    ```
5. **Check the status of the service:**

    To verify that the service is running correctly:
    ```
    sudo systemctl status monitoring.service
    ```

### Bash Script (`monitor.sh`):

This script continuously checks the availability of the server by sending an HTTP request to `http://localhost/index.php`. It also pings the IP addresses listed in `ips.txt` to check if they are online or offline.

The script outputs the status of the server and kiosks to the `status.txt` file. This file is then used by the web interface to display the status.

### Web Interface:

The web page, built with HTML, CSS, and JavaScript, reads the `status.txt` file every 30 seconds to update the status of the kiosks and the server without needing a full page refresh. The kiosks are visually represented with color-coded (green for online, red for offline) icons.

## Screenshots
![imagen](https://github.com/user-attachments/assets/49281686-a957-457f-82fa-b72cdc1a42f9)


**Figure 1**: Status page showing server and kiosk states

## Customization

### Update Interval:

You can change the update interval in the Bash script (`monitor.sh`) by modifying the `check_interval` variable.

### Kiosk Icons:

To customize the icons used for kiosks, replace the `quiosco.svg | server.svg` file in the `img/` directory with your preferred image.

## License

This project is open-source and available under the MIT License. Feel free to use and modify it for your needs.

## Image Credits

The SVG images used in this project (e.g., `server.svg`, `quiosco.svg`) are sourced from [svgrepo.com](https://www.svgrepo.com).
