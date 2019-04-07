//Client::Login
function sendLoginForm (go) {
	var email = document.getElementById('LOGIN_email').value;
	var pass  = document.getElementById('LOGIN_pass').value;

	if(!isValidEmail(email) || !isValidPassword(pass)){
		alert('#@$%&?! Please provide a valid email address.');
		return false;
	}
	
	var callback = function (x) {
		if(x.responseText == 'FAIL') {
			alert('There is not an account for this email and passcode.');
			return false;
		}
		window.location = go;	
	}
	
	var XObj;
	try { XObj = new XMLHttpRequest(); }
	catch(e) { XObj = new ActiveXObject(Microsoft.XMLHTTP); }
	
	XObj.onreadystatechange = function () {
		if(XObj.readyState == 4) {
		    if(callback) {
				callback(XObj);
			}
		}
	}

	XObj.open('POST','./php/db/login.php?e=' + email + '&p=' + pass + '&t=' + md5_stamp, true);
	XObj.send(null);
}

//Config::Logout
function logout () {
	
	var callback = function (x) {
            window.location = "./";
	}
	
	var XObj;
	try { XObj = new XMLHttpRequest(); }
	catch(e) { XObj = new ActiveXObject(Microsoft.XMLHTTP); }
	
	XObj.onreadystatechange = function () {
		if(XObj.readyState == 4) {
			if(callback) {
				callback(XObj);
			}
		}
	}
			
	XObj.open('POST','./php/db/logout.php?t=' + md5_stamp, true);
	XObj.send(null);
}

//Config::Register
function sendForm (div) {
	var email = document.getElementById('REG_email').value;
	var pass  = document.getElementById('REG_pass').value;
	var cpass = document.getElementById('REG_cpass').value;
	var agree = document.getElementById('REG_agree').checked;

	//if(hasWhiteSpace(username) || hasSpecialChars(username)){
		//alert('#@$%&?! Username can not contain spaces or special characters.');
		//return false;
	//}
	
	if(!isValidEmail(email)) {
		alert('Enter a valid email to continue: me@email.com');
		return false;
	}
	
	if(!isValidPassword(pass)) {
		alert('Passcode must contain 6-20 characters.');
		return false;
	}
	
	if(pass != cpass) {
		alert('Your passcode confirmation does not match.');
		return false;
	}
	
	if(cpass != pass || !email || !pass || !cpass) {
		alert('You must enter a username, valid email and create a new passcode.');
		return false;
	}
	
	if(!agree) {
		alert('You must agree to the terms of service.');
		return false;	
	}

	var callback = function (x) {
		if(x.responseText == 'ACTIVE') {
			alert('There is already an account for this email. If you forgot your passcode send an email to editors@bigups.tv and we will create one for you.');
			return false;
		}
		
		if(x.responseText == 'FAIL') {
			alert('Wha gwan!? Something went wrong.');
			return false;
		}
	    
		window.location = "./";		
	}
	
	var XObj;
	try { XObj = new XMLHttpRequest(); }
	catch(e) { XObj = new ActiveXObject(Microsoft.XMLHTTP); }
	
	XObj.onreadystatechange = function () {
		if(XObj.readyState == 4) {
			if(callback) {
				callback(XObj);
			}
		}		
	}		

	XObj.open('POST','./php/db/reg.php?e=' + email + '&p=' + pass + '&t=' + md5_stamp, true);
	XObj.send(null);
}

//DISPLAY:Register
function joinForm (div) {
    var email = "";
    if(document.getElementById('joinEmail')) {
	email = document.getElementById('joinEmail').value;
    }
	
    var login_form = "<div style='float: left; width: 540px;'>";
    login_form += "<div style='text-align: left; color: #999; margin: 70px 70px'>";
	
    login_form += "<div style='font-size: 32px; margin-bottom: 20px; width: 100%'>SIGN IN</div>";
    login_form += "<form><div>Email</div><div><input id='LOGIN_email' class='form' value=''/></div>";
    login_form += "<div>Passcode</div><div><input id='LOGIN_pass' type='password' class='form' value=''/></div>";
	login_form += "<div><a>Forgot passcode?</a></div>";
	login_form += "<div><input class='form_button' type=\"button\" onClick=\"sendLoginForm('./')\" value=\"CONNECT\"/></div></form>";
	login_form += "<div style='width: 80px; text-align: center; color: #DCDCDC; font-size: 10px; margin: 0 auto; padding-top: 20px'></div><div class='arrow-down' style='margin: 0 auto'></div>";
						
	login_form += "</div>";
	login_form += "</div>";
	
	var join_form = "<div style='float: left; width: 540px;'>";
	join_form += "<div style='text-align: left; color: #999; margin: 70px 70px;'>";
	
	join_form += "<div style='font-size: 32px; margin-bottom: 20px; width: 100%'>Create a Test Account</div><small>Test Accounts may be subject to deletion at any time. Our project is in active development. Any items created using the test service will not be available to export.</small><br /><br />";
	join_form += "<div>Email</div><div><input id='REG_email' class='form' value='" + email + "'/></div>";
	join_form += "<div>Passcode</div><div><input id='REG_pass' type='password' class='form' value=''/></div>";
	join_form += "<div>Confirm Passcode</div><div><input id='REG_cpass' type='password' class='form' value=''/></div>";

	join_form += "<div style='font-size: 12px'><input id=\"REG_agree\" type=\"checkbox\" style=\"vertical-align: middle\"/>I agree to the <a onClick=\"alert('SERVICE AGREEMENT. Your account is provided 100% free of charge. "
	+ " By uploading videos, photos, music and posting in community forums you are declaring that you have ownership or consent to distribute the content. Any copyright violations are solely"
	+ " the responsibility of the account owner. We reserve the right to terminate any account at any time for violations of this agreement and any other behaviour that is deemed inappropriate."
	+ " We are not liable for any damages or lost revenue caused by temporary interruptions in service or uploading errors.')\">Service Agreement</a> and <a onclick=\"alert('We are a tech"
	+ " company that respects your privacy and will never sell your data to third party advertisers. This site uses cookies to provide better services. You may request your account to be closed"
	+ " at any time. All pertinent files will be removed from both public and private severs.')\">Privacy Policy</a>.</div>";

	join_form += "<div><input class='form_button' type=\"button\" onClick=\"sendForm('" + div + "')\" value=\"CREATE ACCOUNT\"/></div>";
	
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
