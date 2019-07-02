function toggleDiv(divName) {
	thisDiv = document.getElementById(divName);
	if (thisDiv) {
		if (thisDiv.style.display == "none") {
			thisDiv.style.display = "block";
		}
		else {
			thisDiv.style.display = "none";
		}
	}
	else {
		errorString = "Error: Could not locate div with id: " + divName;
		alert(errorString);
	}
}

function toggleMenuSection(unique) {
	action = "toggleType = toggleDiv('div_" + unique + "');";
	eval(action);
	action = "thisImage = document.getElementById('img_" + unique + "');";
	eval(action);
	if (document.getElementById('div_' + unique).offsetHeight > 0) {
		thisImage.src = "http://www.alexking.org/images/menu_tree_open.gif";
	}
	else {
		thisImage.src = "http://www.alexking.org/images/menu_tree_closed.gif";
	}
}

function flashArchives() {
	for (i = 0; i < 8; i++) {
		if (i % 2 == 0) {
			setTimeout("document.getElementById('archives').style.background='#569';", i*150);
		}
		else {
			setTimeout("document.getElementById('archives').style.background='#89c';", i*150);
		}
	}
}

function splashCaption(thisLi) {
	var captions = new Array("tasks"
	                        ,"photos"
	                        ,"hacks"
	                        ,"ritm"
	                        );
	if (thisLi) {
		for (i = 0; i < captions.length; i++) {
			if ("splash_" + captions[i] == thisLi.id) {
				document.getElementById("splash_text").innerHTML = thisLi.childNodes[1].innerHTML;
//				document.getElementById("splash_" + captions[i]).childNodes[0].childNodes[0].src = "images/clear.gif";
			}
			else {
//				document.getElementById("splash_" + captions[i]).childNodes[0].childNodes[0].src = "home/splash_screen.gif";
			}
		}
	}
	else {
		for (i = 0; i < captions.length; i++) {
//			document.getElementById("splash_" + captions[i]).childNodes[0].childNodes[0].src = "images/clear.gif";
		}
		document.getElementById("splash_text").innerHTML = "Click for more information.";
	}
		
}

function activateSubNav(id) {
	for (i = 0; i < tabs.length; i++) {
		if (document.getElementById(tabs[i])) {
			if (id == 'init') {
				var sURL = location.href;
				var anchorLocation = sURL.indexOf('#');
				if (anchorLocation > -1) {
					var tab = sURL.substring((anchorLocation + 1), sURL.length);
					activateSubNav(tab);
				}
				else {
					activateSubNav('wp_100');
				}
				i = tabs.length;
			}
			else if (id == tabs[i]) {
				document.getElementById(tabs[i]).style.display = "block";
				document.getElementById(tabs[i] + "_tab").className += " active";
			}
			else {
				document.getElementById(tabs[i]).style.display = "none";
				document.getElementById(tabs[i] + "_tab").className = document.getElementById(tabs[i] + "_tab").className.replace(/active/g, "");
			}
		}
	}
}

function slvBanner() {
	var posts = slvGetCookie('wplastvisit_posts');
	var comments = slvGetCookie('wplastvisit_comments');
	if (posts == null || comments == null) {
		return false;
	}
	var banner = '';
	if (posts == 1) {
		banner += posts + ' new post and ';
	}
	else {
		banner += posts + ' new posts and ';
	}
	if (comments == 1) {
		banner += comments + ' new comment since your last visit.';
	}
	else {
		banner += comments + ' new comments since your last visit.';
	}
	document.write(banner);
}

function slvShowNewIndicator(date) {
	if (parseInt(date) > parseInt(slvGetCookie('wplastvisit'))) {
		document.write('<img src="/blog/ak-img/new.gif" alt="New" title="New since your last visit." />');
	}
}

function slvGetCookie(name) {

/**
 * Read the JavaScript cookies tutorial at:
 *   http://www.netspade.com/articles/javascript/cookies.xml
 */

	var dc = document.cookie;
	var prefix = name + "=";
	var begin = dc.indexOf("; " + prefix);
	if (begin == -1)
	{
		begin = dc.indexOf(prefix);
		if (begin != 0) return null;
	}
	else
	{
		begin += 2;
	}
	var end = document.cookie.indexOf(";", begin);
	if (end == -1)
	{
		end = dc.length;
	}
	return unescape(dc.substring(begin + prefix.length, end));
}	

function email(a, b, c, d, show) { // try to avoid spam trollers, intentionally complex
	if (!show) {
		show = a + b + c + d;
	}
	e_string = "<a href=\"ma" + "ilto:" + a + b + c + d + "\">" + show + "</a>";
	document.write(e_string);
}
