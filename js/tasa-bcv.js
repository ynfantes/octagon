fetch('https://exchangemonitor.net/ajax/widget-unique?country=ve&type=bcv')
.then((response) => response.json())
.then((data) => {
    const element = document.getElementById('tasa_del_dia');
    element.innerHTML = data.price;
});
