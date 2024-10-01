const statusUrl = 'status.txt';  // Ruta del archivo status.txt
const updateInterval = 30000;  // 30 segundos para actualizar

async function fetchStatus() {
    try {
        const response = await fetch(statusUrl + '?_=' + new Date().getTime());  // Evitar caché con un query param
        const text = await response.text();
        
        // Reemplazar '\n' literales por saltos de línea reales
        const cleanedText = text.replace(/\\n/g, '\n');
        updateStatus(cleanedText);
    } catch (error) {
        console.error("Error al leer el archivo status.txt:", error);
    }
}

function updateStatus(data) {
    const lines = data.split('\n');

    // Actualizar el campo de la última comprobación
    const now = new Date();
    const lastCheckElement = document.getElementById('last-check-time');
    lastCheckElement.textContent = `Última comprobación: ${now.toLocaleString()}`;

    // Actualizar estado del servidor
    const serverStatusLine = lines[0].trim(); // Tomamos la línea del estado del servidor
    const serverStatusElement = document.getElementById('server-status');
    
    // Asumiendo que el formato es "SERVIDOR (IP): Estado"
    const serverStatusMatch = serverStatusLine.match(/(.+) \((.+)\): (Online|Offline)/);
    if (serverStatusMatch) {
        const name = serverStatusMatch[1]; // El nombre del servidor
        const statusText = serverStatusMatch[3]; // Estado (Online/Offline)
        serverStatusElement.textContent = `${name}: ${statusText}`;
        serverStatusElement.style.color = statusText === 'Online' ? 'green' : 'red';
    }

    // Actualizar estado de los equipos (quioscos)
    const quioscosContainer = document.getElementById('quioscos-status');
    quioscosContainer.innerHTML = '';  // Limpiar quioscos anteriores

    for (let i = 1; i < lines.length; i++) {  // Empieza desde la línea 1 porque la 0 es del servidor
        const line = lines[i].trim();
        if (line) {
            const [nameAndIp, status] = line.split(': ');
            const nameMatch = nameAndIp.match(/(.+) \((.+)\)/);
            if (nameMatch) {
                const name = nameMatch[1];  // Extraer solo el nombre del quiosco
                const isOnline = status === 'Online';
                const quioscoElement = document.createElement('div');
                quioscoElement.classList.add('quiosco');

                quioscoElement.innerHTML = `
                    <img src="img/quiosco.svg" alt="Quiosco" style="max-width: 40px;">
                    <span style="color: ${isOnline ? 'green' : 'red'}; margin-left: 10px;">
                        ${isOnline ? 'Online' : 'Offline'}
                    </span>
                    <span style="margin-left: 10px;">${name}</span> <!-- Mostrar solo el nombre del quiosco -->
                `;
                
                quioscosContainer.appendChild(quioscoElement);  // Añadir cada quiosco al contenedor
            }
        }
    }
}



// Iniciar la monitorización cada 60 segundos
setInterval(fetchStatus, updateInterval);
fetchStatus();  // Llamar una vez al cargar la página