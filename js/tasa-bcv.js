fetch('https://v2.web.ve/bcv/?action=rateOfDay')
.then(response => response.text())  // Obtener la respuesta como texto
.then(text => {
    const data = JSON.parse(text); // Convertir el string JSON a un objeto
    const tasa = determinarTasa(data);
    const element = document.getElementById('tasa_del_dia');
    element.innerHTML = tasa;
})
.catch(error => console.error('Error al obtener la tasa:', error));

function obtenerFechaHoraActual() {
    const ahora = new Date();
    const fechaServidor = ahora.toISOString().split('T')[0]; // YYYY-MM-DD
    return { fechaServidor, diaSemana: ahora.getDay() + 1 }; // 1 (Lunes) - 7 (Domingo)
}

function determinarTasa(exchangeRate) {
    const { fechaServidor, diaSemana } = obtenerFechaHoraActual();
    
    // Obtener fecha de actualización desde la API
    const dtUpdate = new Date(exchangeRate.updated);
    const fechaUpdate = dtUpdate.toISOString().split('T')[0]; // YYYY-MM-DD
    const horaUpdate = dtUpdate.toTimeString().split(' ')[0]; // HH:MM:SS

    const horaReferencia = "15:00:00"; // 3:00 PM
    
    let tasa;
    if (diaSemana === 6 || diaSemana === 7) { // Sábado o domingo
        tasa = exchangeRate.prev;
    } else if (fechaServidor === fechaUpdate && horaUpdate > horaReferencia) {
        tasa = exchangeRate.prev;
    } else {
        tasa = exchangeRate.price;
    }
    
    return tasa;
}