$(document).ready(function() {
	//Timer setup.
    var start_val = 60, //Number of seconds to refresh in.
		curr_val  = start_val,
		timer,
		paused = false,
		counter = function(){
			curr_val--;
			if (curr_val <= 0) {
				curr_val = start_val;
				$('#refreshBtn').trigger('click');
			}
			$('.refresh_timer').html(curr_val); //Display time. '<span class="glyphicon glyphicon-refresh"><span>' + 
			timer = setTimeout(function(){
				counter();
			}, 1000);
		};
	
	//Initialize cycle.
	counter(); 
    
	//Refresh only the data grid.
	$( "#refreshBtn" ).click(function() {
		$.pjax.reload({
		   container: "#tkt_dashboard",
		   url: "/ticket/ticket/index",
		   push: false,
		   replace: false
		});
	});
	
	//Toggle the timer.
	$('#pauseBtn').on('click', function(){
		clearTimeout(timer);
		paused = !paused;
		if (!paused) {
			counter();
		}
	});
	
});