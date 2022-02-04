const countdown = document.getElementById('countdown');
let timer = new Timer();
timer.start({countdown: true, startValues: {seconds: parseInt(document.getElementById('seconds').innerText)  }});

timer.addEventListener('secondsUpdated', function (e) {
    countdown.innerHTML = timer.getTimeValues().toString();
});

