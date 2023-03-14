fetch('http://bcv.pronet21.net/')
.then((response) => response.json())
.then((data) => {
    const element = document.getElementById('tasa_del_dia');
    element.innerHTML = data.usd;
    console.log(data);
});