//DOM Constants
var md5_stamp;

//Display::functions
function popup(url) 
{
 var width  = 800;
 var height = 440;
 var left   = (screen.width  - width)/2;
 var top    = (screen.height - height)/2;
 var params = 'width='+width+', height='+height;
 params += ', top='+top+', left='+left;
 params += ', directories=no';
 params += ', location=no';
 params += ', menubar=no';
 params += ', resizable=no';
 params += ', scrollbars=no';
 params += ', status=no';
 params += ', toolbar=no';

 var newwin=window.open(url,'loading...'); //params
 newwin.opener = window;
 if (!window.focus) {window.focus()}
 newwin.opener.document.title = "loading...";
 return false;
}

function auto_expand(element) {
    element.style.height = "4px";
    element.style.height = (element.scrollHeight - 8)+"px";
}

function dom(id) {
	return document.getElementById(id);
}

function formatTime(millis) {
	var minutes = Math.floor(millis / 60);
	var seconds = ((millis % 60)).toFixed(0);
	return minutes + ":" + (seconds < 10 ? '0' : '') + seconds;
}

function holdDown(btn, action, start) {
	var t;
	
	var repeat = function () {
		action();
		t = setTimeout(repeat, start);
	}
	
	repeat();
	
    btn.onmousedown = function() {
        repeat();
    }

    btn.onmouseup = function () {
		clearTimeout(t);
    }
	
    btn.onmouseout = function () {
        clearTimeout(t);
    }
}
	
function make_date_time (date_time_string) {
	var string = date_time_string.split(" ");
	var date = make_date(string[0]);
	var time = make_time(string[1]);
	return date; 
}

function make_date (date_string) {
	var calender = date_string.split("-");
	var year = calender[0];
	var month = calender[1];
	var day = calender[2].split(" ");
	day = day[0];

	var month_name = new Array();
	month_name['01'] = "January";
	month_name['02'] = "Febraury";
	month_name['03'] = "March";
	month_name['04'] = "April";
	month_name['05'] = "May";
	month_name['06'] = "June";
	month_name['07'] = "July";
	month_name['08'] = "August";
	month_name['09'] = "September";
	month_name['10'] = "October";
	month_name['11'] = "November";
	month_name['12'] = "December";
	
	return month_name[month] + " " + day + ", " + year;
}

function make_time (time_string) {
	var day = time_string.split(":");
	var hour = day[0];
	var mins = day[1];
	var sec = day[2];
	
	if(hour > 12) {
		hour = hour - 12;
	}
	return parseInt(hour) + ":" + mins;
}
