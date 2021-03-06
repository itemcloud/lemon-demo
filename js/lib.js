//DOM Constants
var md5_stamp;

var domId = function (id) {
	return document.getElementById(id);
}

var OmniBox = class {
	constructor(class_array, parent_div) {
		this.class_array = class_array;
		this.active_class = Object.keys(class_array)[0];
		this.parent_div = parent_div;
	}
	
	toggle (class_id) {
		this.active_class = class_id;
		document.getElementById(this.parent_div).innerHTML = this.class_form_HTML(this.class_array[class_id]);
	}

    checkInput () {
	var class_form = this.class_array[this.active_class];

	for (var i = 0; i < class_form.nodes.length; i++) {
	    var node = class_form.nodes[i];
	    var value = document.getElementById('itc_' + node['node_name'] + '_txt').value;
	    
	    if(node['required'] && !value) {
		var item_name = node['node_name']; //node['node_name'].charAt(0).toUpperCase() + node['node_name'].slice(1);

		domId('alertbox').innerHTML = "Adding a " + item_name + " is required for new " + class_form['class_name'] + ".";
	    	domId('alertbox').className = "alertbox-show";
		return false;
	    }	    
	}

	domId('itc_OmniBoxForm').submit();
    }
	
	class_form_HTML (class_form) {
	    var form_input = "<input type=\"hidden\" name=\"itc_class_id\" value=\"" + class_form['class_id'] + "\"/>";
	    var form_id = " id=\"itc_OmniBoxForm\"";
	    var functions = " action=\"add.php\" method=\"post\"";	
			
		for (var i = 0; i < class_form.nodes.length; i++) {
			var node = class_form.nodes[i];
			
			if(class_form['types'].length > 0 && node['node_name'] == "file") {
				var functions = " action=\"add.php\" method=\"post\" enctype=\"multipart/form-data\"";

				var types = class_form['types'];
				form_input += "<input type=\"file\" class=\"item-tools\" name=\"itc_" + node['node_name'] + "\" id=\"itc_" + node['node_name'] + "_txt\" accept=\"";

				//accepted filetypes
				form_input += types.join();
				form_input += "\"/><div><small>Choose " + types.join() + " only.</small></div>";
				form_input += "<hr />";

			} else {
				if(!node['required']) {
					var domid = "itc_" + node['node_name'];
					var domid_add = "itc_add_" + node['node_name'] + "_" + class_form['class_id'];

					var show = "this.style.display='none';"
						  + "domId('" + domid + "').style.display = 'block'";
					var hide = "domId('" + domid_add + "').style.display = 'block';"
						  + "domId('" + domid + "').style.display = 'none';"
						  + "domId('" + domid + "_txt').value = '';";
					
					form_input += "<div id=\"" + domid_add + "\" onclick=\"" + show + "\"><a>+ <u>Add " + node['node_name'] + "</u></a></div>";
					form_input += "<div id=\"" + domid + "\" style=\"display: none\"><textarea id=\"" + domid + "_txt\" class=\"form wider\" name=\"itc_" + node['node_name'] + "\" onkeyup=\"auto_expand(this)\" maxlength=\"" + node['length']  + "\" style=\"vertical-align: bottom\"></textarea> <span onClick=\"" + hide + "\" class=\"item-tools\">x</span></div>";
					form_input += "<hr />";
				} else {
					form_input += "<textarea class=\"form wider\" id=\"itc_" + node['node_name'] + "_txt\" name=\"itc_" + node['node_name'] + "\" onkeyup=\"auto_expand(this)\" maxlength=\"" + node['length'] + "\"></textarea>";
					form_input += "<hr />";
				}
			}
			
		    var upload = "<input class=\"item-tools\" type=\"button\" name=\"checkInput\" onClick=\"OmniController.checkInput()\"  value=\"&#10004 SAVE\"/><br />";
		}

		var inactive = "_inactive";
		var toggleItemClass = "";
		var x;
		for (x in this.class_array) {
			var item_class = this.class_array[x];
			if(item_class['class_id'] == this.active_class) { inactive = ""; }	
			toggleItemClass += "<input class=\"item_tools" + inactive + "\" type=\"button\" onclick=\"OmniController.toggle('" + item_class['class_id'] + "')\" value=\"" + item_class['class_name'] + "\"/> ";
			inactive = "_inactive";	
		}
		
		var form_display = "<form" + form_id + functions + ">"
			+ form_input
			+ "<div style='float: right'>" + upload + "</div>"
			+ toggleItemClass
			+ "</form></div>";
			
		return form_display;
	}
}

class OmniEditBox extends OmniBox {

	set_active_item (item_array) {
		this.item_array = item_array;
	}
		
 	class_form_HTML (class_form) {
	    var item = JSON.parse(this.item_array);		
	    var form_input = "<input type=\"hidden\" name=\"itc_class_id\" value=\"" + class_form['class_id'] + "\"/>";
	    form_input += "<input type=\"hidden\" name=\"itc_edit_item\" value=\"" + item['item_id'] + "\"/>";
	    var form_id = " id=\"itc_OmniBoxForm\"";
	    var functions = " action=\"index.php\" method=\"post\"";
			
		for (var i = 0; i < class_form.nodes.length; i++) {
			var node = class_form.nodes[i];
			
			if(class_form['types'].length > 0 && node['node_name'] == "file") {
			    if(item[node['node_name']]) {
				form_input += "<input class=\"item-tools\" name=\"itc_" + node['node_name'] + "\" id=\"itc_" + node['node_name'] + "_txt\" type=\"hidden\" value=\"" + item[node['node_name']] + "\"/>";
				form_input += item[node['node_name']];
				form_input += "<hr />";
				} else { 
					//NOT USED - Add file while editing
					var functions = " action=\"index.php\" method=\"post\" enctype=\"multipart/form-data\"";

					var types = class_form['types'];
					form_input += "<input type=\"file\" class=\"item-tools\" name=\"itc_" + node['node_name'] + "_txt\" id=\"itc_" + node['node_name'] + "\" accept=\"";

					//accepted filetypes
					form_input += types.join();
					form_input += "\"/><div><small>Choose " + types.join() + " only.</small></div>";
					form_input += "<hr />";
				}
				
			} else {
				if(!node['required'] && !item[node['node_name']]) {
					var domid = "itc_" + node['node_name'];
					var domid_add = "itc_add_" + node['node_name'] + "_" + class_form['class_id'];

					var show = "this.style.display='none';"
						  + "domId('" + domid + "').style.display = 'block'";
					var hide = "domId('" + domid_add + "').style.display = 'block';"
						  + "domId('" + domid + "').style.display = 'none';"
						  + "domId('" + domid + "_txt').value = '" + item[node['node_name']] + "';";
					
					form_input += "<div id=\"" + domid_add + "\" onclick=\"" + show + "\"><a>+ <u>Add " + node['node_name'] + "</u></a></div>";
					form_input += "<div id=\"" + domid + "\" style=\"display: none\"><textarea id=\"" + domid + "_txt\" class=\"form wider\" name=\"itc_" + node['node_name'] + "\" onkeyup=\"auto_expand(this)\" maxlength=\"" + node['length']  + "\" style=\"vertical-align: bottom\">" + item[node['node_name']] + "</textarea> <span onClick=\"" + hide + "\" class=\"item-tools\">x</span></div>";
					form_input += "<hr />";
				} else {
					form_input += "<textarea class=\"form wider\" id=\"itc_" + node['node_name'] + "_txt\" name=\"itc_" + node['node_name'] + "\" onkeyup=\"auto_expand(this)\" maxlength=\"" + node['length'] + "\">" + item[node['node_name']] + "</textarea>";
					form_input += "<hr />";
				}
			}
			
		    var upload = "<input onClick=\"window.history.back()\"  type=\"button\" class=\"item-tools\" value=\"&#10008; Cancel\"/>";
		    upload += "<input class=\"item-tools\" type=\"button\" name=\"checkInput\" onClick=\"OmniEditController.checkInput()\"  value=\"&#10004 SAVE\"/><br />";
		}

		var inactive = "_inactive";
		var toggleItemClass = "";
		var x;
		for (x in this.class_array) {
			var item_class = this.class_array[x];
			if(item_class['class_id'] == this.active_class) { inactive = ""; }	
			if(item_class['class_id'] == item['class_id']) {
				toggleItemClass += "<input class=\"item_tools" + inactive + "\" type=\"button\" onclick=\"OmniEditController.toggle('" + item_class['class_id'] + "')\" value=\"" + item_class['class_name'] + "\"/> ";
			}
			inactive = "_inactive";				
		}
		
		var form_display = "<form" + form_id + functions + ">"
			+ form_input
			+ "<div style='float: right'>" + upload + "</div>"
			+ toggleItemClass
			+ "</form></div>";
			
		return form_display;
	}
}

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
