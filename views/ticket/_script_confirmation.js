$(document).ready(function() {
   
    var myVar = setInterval(function(){ myTimer() }, 1000);
	var startTime = new Date();
	var secRefresh = 5;
	function myTimer() {
		var d = new Date();
		var r = d - startTime;
		var timeLeft = (secRefresh - Math.round(r/1000,0));
		if(r > (secRefresh*1000)) {
			myStopFunction();
			timeLeft = 0;
		}
		document.getElementById("countdown").innerHTML = 'Refresh in ' + timeLeft + ' seconds.';

		var t = d.toLocaleTimeString();
		
	}
   
	function myStopFunction() {
		clearInterval(myVar);
		window.location = '/ticket/ticket/index';
	}

});