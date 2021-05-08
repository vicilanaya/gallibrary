"use strict";		
$(document).ready(function() {
	
	// set spanish to hide by default at page load
	$('[lang=es]').hide();
	
	// hide spanish, show english when english is clicked
	$("#english").click(function(event) {
		$('[lang=es]').hide();
		$('[lang=en]').show();
		
		// cancel default link behavior
		event.preventDefault();
	});
	
	// hide english, show spanish when spanish is clicked
	$("#spanish").click(function(event) {
		$('[lang=en]').hide();
		$('[lang=es]').show();
		
		// cancel default link behavior
		event.preventDefault();
	});
	
	// open print dialog box when print is clicked
	$("#print").click(function(event) {
		window.print();
		
		// cancel default link behavior
		event.preventDefault();
	});
	
	// get current year for copyrigt statement in footer
	// function getThisYear() {
		// var date = new Date();
		// var year = date.getFullYear();
		// document.write(year);
		// return year;
	// }

	

});	// end ready

