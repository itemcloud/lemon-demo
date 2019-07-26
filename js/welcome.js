//Client::Login
var sendLoginForm = function (go) {
	var email = document.getElementById('LOGIN_email').value;
	var pass  = document.getElementById('LOGIN_pass').value;

	if(!isValidEmail(email) || !isValidPassword(pass)){
		showAlertBox('#@$%&?! Please provide a valid email address.');
		return false;
	}
	domId('sendForm').submit();
}


function showAlertBox(message) {
	if(domId('alertbox')) {
		domId('alertbox').innerHTML = message;
		domId('alertbox').className = "alertbox-show";
	}
}

//Config::Logout
function logout () {
	domId('logoutForm').submit();
}

//Config::Register
function sendForm (div) {
	var email = document.getElementById('REG_email').value;
	var pass  = document.getElementById('REG_pass').value;
	var cpass = document.getElementById('REG_cpass').value;
	var agree = document.getElementById('REG_agree').checked;

	//if(hasWhiteSpace(username) || hasSpecialChars(username)){
		//showAlertBox('#@$%&?! Username can not contain spaces or special characters.');
		//return false;
	//}
	
	if(!isValidEmail(email)) {
		showAlertBox('Enter a valid email to continue: me@email.com');
		return false;
	}
	
	if(!isValidPassword(pass)) {
		showAlertBox('Passcode must contain 6-20 characters.');
		return false;
	}
	
	if(pass != cpass) {
		showAlertBox('Your passcode confirmation does not match.');
		return false;
	}
	
	if(cpass != pass || !email || !pass || !cpass) {
		showAlertBox('You must enter a username, valid email and create a new passcode.');
		return false;
	}
	
	if(!agree) {
		showAlertBox('You must agree to the terms of service.');
		return false;	
	}

	domId('joinForm').submit();
}

//DISPLAY:Register
function joinForm (div) {
    var email = "";
    if(document.getElementById('joinEmail')) {
	email = document.getElementById('joinEmail').value;
    }
	
    var login_form = "<div style='float: left; width: 540px;'>";
    login_form += "<div style='text-align: left; color: #999; margin: 70px 70px'>";
	
    login_form += "<div style='font-size: 32px; margin-bottom: 20px; width: 100%'>Sign In</div>";
    login_form += "<form id=\"sendForm\" action=\"./index.php?connect=1\" method=\"post\"><div>Email</div><div><input id='LOGIN_email' name='e' class='form' value=''/></div>";
    login_form += "<div>Passcode</div><div><input id='LOGIN_pass' name='p' type='password' class='form' value=''/></div>";
	login_form += "<div><a>Forgot passcode?</a></div>";
	login_form += "<div><input class='form_button' type=\"button\" onClick=\"sendLoginForm('./')\" value=\"CONNECT\"/></div></form>";
	login_form += "<div style='width: 80px; text-align: center; color: #DCDCDC; font-size: 10px; margin: 0 auto; padding-top: 20px'></div><div class='arrow-down' style='margin: 0 auto'></div>";
						
	login_form += "</div>";
	login_form += "</div>";
	
	var join_form = "<div style='float: left; width: 540px;'>";
	join_form += "<div style='text-align: left; color: #999; margin: 70px 70px;'>";

	join_form += "<div style='font-size: 32px; margin-bottom: 20px; width: 100%'>Create an Account</div><small>Get connected with the collective! Start learning from fellow artists and sharing with the community. All members can post notes, links, files, photos, audio. Your account will help you keep track of all your favorite things.</small><br /><br />";
	join_form += "<form id=\"joinForm\" action=\"./index.php?connect=1\" method=\"post\"><div>Email</div><div><input id='REG_email' name='e' class='form' value='" + email + "'/></div>";
	join_form += "<div>Passcode</div><div><input id='REG_pass' name='p' type='password' class='form' value=''/></div>";
	join_form += "<div>Confirm Passcode</div><div><input id='REG_cpass' type='password' class='form' value=''/></div>";
	join_form += "<input name='REG_new' type='hidden' value='1'/></div>";



	join_form += "<div style='font-size: 12px'><input id=\"REG_agree\" type=\"checkbox\" style=\"vertical-align: middle\"/>I agree to the <a onClick=\"alert('SERVICE AGREEMENT. Your account is provided 100% free of charge. "
	+ " By uploading videos, photos, music and posting in community forums you are declaring that you have ownership or consent to distribute the content. Any copyright violations are solely"
	+ " the responsibility of the account owner. We reserve the right to terminate any account at any time for violations of this agreement and any other behaviour that is deemed inappropriate."
	+ " We are not liable for any damages or lost revenue caused by temporary interruptions in service or uploading errors.')\">Service Agreement</a> and <a onclick=\"alert('We are a tech"
	+ " company that respects your privacy and will never sell your data to third party advertisers. This site uses cookies to provide better services. You may request your account to be closed"
	+ " at any time. All pertinent files will be removed from both public and private severs.')\">Privacy Policy</a>.</div>";

	join_form += "<div><input class='form_button' type=\"button\" onClick=\"sendForm('" + div + "')\" value=\"CREATE ACCOUNT\"/></div></form>";
	
	join_form += "</div>";
	join_form += "</div>";
	
	document.getElementById(div).innerHTML = login_form + join_form + "<div style='clear: both'></div>";
}

//DISPLAY::Register:functions
//Reusable functions
function hasWhiteSpace(s) {
	return s.indexOf(' ') >= 0;
}

function hasSpecialChars(str){
	return !str.match(/^[a-zA-Z0-9]+$/);
}

function isValidEmail(str) {
	 if (/^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/.test(str))  
	  {  
		return (true)  
	  }  
		return (false)  
}

function isValidPassword(str) {
	if(str.length<19 && str.length>5)
	{
		return true;
	}
		return false;
}
