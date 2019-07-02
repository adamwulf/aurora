
function getMaxDOM(yy, m){
	var mtend = new Array(31, ((yy % 4 == 0 && yy % 100 != 0) || yy % 400 == 0 ? 29 : 28), 31, 30, 31, 30, 31, 31, 30, 31, 30, 31);
	return mtend[m-1];
}

function getDOW(d){
	var day_array = new Array("Su", "Mo", "Tu", "We", "Th", "Fr", "Sa");
	if(xNum(d)){
		return day_array[d];
	}
}


function buildMonth(obj_name, yy, mm, view, day_url, week_url, month_url){
	var MINUTE = 60 * 1000;
	var HOUR = MINUTE * 60;
	var DAY = HOUR * 24;
	var WEEK = DAY * 7;

	if(xStr(obj_name) && xNum(yy) && xNum(mm)){
		var months = new Array("Jan","Feb","Mar","Apr","May","Jun","Jul","Aug","Sep","Oct","Nov","Dec");
		var mtend = new Array(31, ((yy % 4 == 0 && yy % 100 != 0) || yy % 400 == 0 ? 29 : 28), 31, 30, 31, 30, 31, 31, 30, 31, 30, 31);
		var obj = xGetElementById(obj_name);
		var newDate = new Date(yy,mm-1,1,0,0,0);
		var offset = newDate.getDay() -1;

		var numrows = Math.ceil((offset + mtend[mm-1] + 1) / 7.0);
		var numcells = numrows * 7;
		var i;
		var table = document.createElement('TABLE');
		table.setAttribute("class", "month_selector_table");

		
		var row;
		var cell;
		while(obj.childNodes.length > 0){
			obj.removeChild(obj.childNodes[0]);
		}
		
		var mprev_year = (mm == 1)  ? (yy - 1) : yy;
		var mnext_year = (mm == 12) ? (yy + 1) : yy;
		var mprev_month = (mm == 1)  ? 12 : (mm-1);
		var mnext_month = (mm == 12) ? 1  : (mm+1);
		
		var yprev_year = yy - 1;
		var ynext_year = yy + 1;
		var yprev_month = mm;
		var ynext_month = mm;
		
		var safari_table_rows = new Array();
		
		var topr = document.createElement('TR');
		var titlem = document.createElement('TD');
		var titley = document.createElement('TD');
		titlem.setAttribute("class", "month_selector_title");
		titlem.setAttribute("colspan", "3");
		titlem.setAttribute("align", "left");
		titley.setAttribute("class", "month_selector_title border_right");
		titley.setAttribute("colspan", "4");
		titley.setAttribute("align", "right");

		var month_prev = document.createElement('A');
		if(!xSafari){
			month_prev.setAttribute("href", "javascript:;");
			if(xDef(day_url) && xStr(day_url) && xDef(week_url) && xStr(week_url) && xDef(month_url) && xStr(month_url)){
				month_prev.setAttribute("onClick", "buildMonth(\"" + obj_name + "\"," + mprev_year + "," + mprev_month + ",\"" + view + "\",\"" + day_url + "\",\"" + week_url + "\",\"" + month_url + "\");");
			}else{
				month_prev.setAttribute("onClick", "buildMonth(\"" + obj_name + "\"," + mprev_year + "," + mprev_month + ")");
			}
		}else{
			if(xDef(day_url) && xStr(day_url) && xDef(week_url) && xStr(week_url) && xDef(month_url) && xStr(month_url)){
				month_prev.setAttribute("href", "javascript:buildMonth(\'" + obj_name + "\'," + mprev_year + "," + mprev_month + ",\'" + view + "\',\'" + day_url + "\',\'" + week_url + "\',\'" + month_url + "\');");
			}else{
				month_prev.setAttribute("href", "javascript:buildMonth(\'" + obj_name + "\'," + mprev_year + "," + mprev_month + ")");
			}
		}
		month_prev.appendChild(document.createTextNode("<"));
		var month_next = document.createElement('A');
		if(!xSafari){
			month_next.setAttribute("href", "javascript:;");
			if(xDef(day_url) && xStr(day_url) && xDef(week_url) && xStr(week_url) && xDef(month_url) && xStr(month_url)){
				month_next.setAttribute("onClick", "buildMonth(\"" + obj_name + "\"," + mnext_year + "," + mnext_month + ",\"" + view + "\",\"" + day_url + "\",\"" + week_url + "\",\"" + month_url + "\");");
			}else{
				month_prev.setAttribute("onClick", "buildMonth(\"" + obj_name + "\"," + mprev_year + "," + mprev_month + ")");
			}
		}else{
			if(xDef(day_url) && xStr(day_url) && xDef(week_url) && xStr(week_url) && xDef(month_url) && xStr(month_url)){
				month_next.setAttribute("href", "javascript:buildMonth(\'" + obj_name + "\'," + mnext_year + "," + mnext_month + ",\'" + view + "\',\'" + day_url + "\',\'" + week_url + "\',\'" + month_url + "\');");
			}else{
				month_prev.setAttribute("href", "javascript:buildMonth(\'" + obj_name + "\'," + mprev_year + "," + mprev_month + ")");
			}
		}
		month_next.appendChild(document.createTextNode(">"));
		titlem.appendChild(month_prev);
		titlem.appendChild(document.createTextNode(" " + months[mm-1] + " "));
		titlem.appendChild(month_next);

		var year_prev = document.createElement('A');
		if(!xSafari){
			year_prev.setAttribute("href", "javascript:;");
			if(xDef(day_url) && xStr(day_url) && xDef(week_url) && xStr(week_url) && xDef(month_url) && xStr(month_url)){
				year_prev.setAttribute("onClick", "buildMonth(\"" + obj_name + "\"," + yprev_year + "," + yprev_month + ",\"" + view + "\",\"" + day_url + "\",\"" + week_url + "\",\"" + month_url + "\");");
			}else{
				year_prev.setAttribute("onClick", "buildMonth(\"" + obj_name + "\"," + yprev_year + "," + yprev_month + ")");
			}
		}else{
			if(xDef(day_url) && xStr(day_url) && xDef(week_url) && xStr(week_url) && xDef(month_url) && xStr(month_url)){
				year_prev.setAttribute("href", "javascript:buildMonth(\'" + obj_name + "\'," + yprev_year + "," + yprev_month + ",\'" + view + "\',\'" + day_url + "\',\'" + week_url + "\',\'" + month_url + "\');");
			}else{
				year_prev.setAttribute("href", "javascript:buildMonth(\'" + obj_name + "\'," + yprev_year + "," + yprev_month + ")");
			}
		}
		year_prev.appendChild(document.createTextNode("<"));
		var year_next = document.createElement('A');
		if(!xSafari){
			year_next.setAttribute("href", "javascript:;");
			if(xDef(day_url) && xStr(day_url) && xDef(week_url) && xStr(week_url) && xDef(month_url) && xStr(month_url)){
				year_next.setAttribute("onClick", "buildMonth(\"" + obj_name + "\"," + ynext_year + "," + ynext_month + ",\"" + view + "\",\"" + day_url + "\",\"" + week_url + "\",\"" + month_url + "\");");
			}else{
				year_next.setAttribute("onClick", "buildMonth(\"" + obj_name + "\"," + ynext_year + "," + ynext_month + ")");
			}
		}else{
			if(xDef(day_url) && xStr(day_url) && xDef(week_url) && xStr(week_url) && xDef(month_url) && xStr(month_url)){
				year_next.setAttribute("href", "javascript:buildMonth(\'" + obj_name + "\'," + ynext_year + "," + ynext_month + ",\'" + view + "\',\'" + day_url + "\',\'" + week_url + "\',\'" + month_url + "\');");
			}else{
				year_next.setAttribute("href", "javascript:buildMonth(\'" + obj_name + "\'," + ynext_year + "," + ynext_month + ")");
			}
		}

		year_next.appendChild(document.createTextNode(">"));
		titley.appendChild(year_prev);
		titley.appendChild(document.createTextNode(" " + yy + " "));
		titley.appendChild(year_next);

		topr.appendChild(titlem);
		topr.appendChild(titley);
		
		if(!xSafari){
			table.appendChild(topr);
		}else{
			safari_table_rows[safari_table_rows.length] = topr;
		}

		if(xDef(view) && xStr(view)){
			var navr = document.createElement('TR');
			var navcell = document.createElement('TD');
			navcell.setAttribute("class", "month_selector_title border_right");
			navcell.setAttribute("colspan", "7");
			navcell.setAttribute("align", "center");
			
			var day_link = document.createElement('A');
			if(!xSafari){
				day_link.setAttribute("href", "javascript:;");
				day_link.setAttribute("onClick", "buildMonth(\"" + obj_name + "\"," + yy + "," + mm + ",\"day\",\"" + day_url + "\",\"" + week_url + "\",\"" + month_url + "\");");
			}else{
				day_link.setAttribute("href", "javascript:buildMonth(\'" + obj_name + "\'," + yy + "," + mm + ",\'day\',\'" + day_url + "\',\'" + week_url + "\',\'" + month_url + "\');");
			}
			var day_text;
			if(view == "day"){
				day_text = document.createElement('U');
				day_text.appendChild(document.createTextNode("day"));
			}else{
				day_text = document.createTextNode("day")
			}
			day_link.appendChild(day_text);
			navcell.appendChild(day_link);
			navcell.appendChild(document.createTextNode("   "));

			var week_link = document.createElement('A');
			if(!xSafari){
				week_link.setAttribute("href", "javascript:;");
				week_link.setAttribute("onClick", "buildMonth(\"" + obj_name + "\"," + yy + "," + mm + ",\"week\",\"" + day_url + "\",\"" + week_url + "\",\"" + month_url + "\");");
			}else{
				week_link.setAttribute("href", "javascript:buildMonth(\'" + obj_name + "\'," + yy + "," + mm + ",\'week\',\'" + day_url + "\',\'" + week_url + "\',\'" + month_url + "\');");
			}
			var week_text;
			if(view == "week"){
				week_text = document.createElement('U');
				week_text.appendChild(document.createTextNode("week"));
			}else{
				week_text = document.createTextNode("week")
			}
			week_link.appendChild(week_text);
			navcell.appendChild(week_link);
			navcell.appendChild(document.createTextNode("   "));
			
			var month_link = document.createElement('A');
			if(!xSafari){
				month_link.setAttribute("href", "javascript:;");
				month_link.setAttribute("onClick", "buildMonth(\"" + obj_name + "\"," + yy + "," + mm + ",\"month\",\"" + day_url + "\",\"" + week_url + "\",\"" + month_url + "\");");
			}else{
				month_link.setAttribute("href", "javascript:buildMonth(\'" + obj_name + "\'," + yy + "," + mm + ",\'month\',\'" + day_url + "\',\'" + week_url + "\',\'" + month_url + "\');");
			}
			var month_text;
			if(view == "month"){
				month_text = document.createElement('U');
				month_text.appendChild(document.createTextNode("month"));
			}else{
				month_text = document.createTextNode("month")
			}
			month_link.appendChild(month_text);
			navcell.appendChild(month_link);
			
			navr.appendChild(navcell);

			if(!xSafari){
				table.appendChild(navr);
			}else{
				safari_table_rows[safari_table_rows.length] = navr;
			}
		}
		
		var url;
		var day_node;
		var m_text;
		var d_text;
		var start_url = false;
		if(view == "month"){
			start_url = month_url;
		}else
		if(view == "week"){
			start_url = week_url;
		}else
		if(view == "day"){
			start_url = day_url;
		}
		
		var day_of_week = new Array("S", "M", "T", "W", "R", "F", "S");
		row = document.createElement('TR');
		if(!xSafari){
			table.appendChild(row);
		}else{
			safari_table_rows[safari_table_rows.length] = row;
		}
		for(i=0;i<7;i++){
			cell = document.createElement('TD');
			cell.setAttribute("class", "month_selector_title border_right");
			//cell.setAttribute("style", "border-right: 1px solid black;");
			cell.setAttribute("align", "center");
			day_node = document.createTextNode(day_of_week[i]);
			cell.appendChild(day_node);
			row.appendChild(cell);
		}
		for(i=0;i<numcells;i++){
			if((i % 7) == 0){
				row = document.createElement('TR');
				if(view == "week"){
					row.setAttribute("class", "month_selector_week");
					row.id = "row" + (i + "");
					row.className="month_selector_week";
				}
				if(!xSafari){
					table.appendChild(row);
				}else{
					safari_table_rows[safari_table_rows.length] = row;
				}
			}
			cell = document.createElement('TD');
			cell.id = "cell" + (i + "");
			today = new Date();
			if(i > offset && (i-offset) <= mtend[mm-1]){
				if(today.getDate() == (i-offset) &&
				   today.getMonth() == (mm-1) &&
				   (today.getYear()%100) == (yy%100)){
					cell.setAttribute("class", "month_selector_today");
					if(xIE4Up){
						cell.setAttribute("onMouseOver", "xGetElementById('" + cell.id + "').style.background='#939393';");
						cell.setAttribute("onMouseOut", "xGetElementById('" + cell.id + "').style.background='#B8B8B8';");
					}
				}else if(view == "day"){
					cell.setAttribute("class", "month_selector_day");
					if(xIE4Up){
						cell.setAttribute("onMouseOver", "xGetElementById('" + cell.id + "').style.background='#939393';");
						cell.setAttribute("onMouseOut", "xGetElementById('" + cell.id + "').style.background='#ffffff';");
					}
				}else if(view == "week"){
					cell.setAttribute("class", "month_week_day");
					if(xIE4Up){
						cell.setAttribute("onMouseOver", "xGetElementById('" + row.id + "').style.background='#939393';");
						cell.setAttribute("onMouseOut", "xGetElementById('" + row.id + "').style.background='#ffffff';");
					}
				}else{
					cell.setAttribute("class", "month_month_day");
				}
				if(xDef(start_url) && xStr(start_url)){
					m_text = mm;
					if(m_text < 10){
						m_text = "0" + m_text;
					}
					d_text = i-offset;
					if(d_text < 10){
						d_text = "0" + d_text;
					}
					
					
					url = substr_replace(start_url, "%y", yy);
					url = substr_replace(url, "%m", m_text);
					url = substr_replace(url, "%d", d_text);
					day_node = document.createTextNode((i - offset));
					cell.setAttribute("onClick", "window.open(\"" + url + "\", \"_self\");");
					if(xIE4Up){
						cell.style.cursor = "hand";
					}else{
						cell.setAttribute("style", "cursor: pointer;");
					}
				}else{
					day_node = document.createTextNode((i - offset));
					
				}
				if(xSafari){
					link_node = document.createElement("A");
					link_node.setAttribute("href", url);
					link_node.appendChild(day_node);
					day_node = link_node;
				}
				cell.appendChild(day_node);
			}else{
				cell.appendChild(document.createTextNode(" "));
				// if we're in the top row
				if(i < 7){
					cell.setAttribute("colspan", offset+1);
					i = offset;
					cell.setAttribute("class", "month_selector_other_month_cell_top");
				}else{
					cell.setAttribute("class", "month_selector_other_month_cell_bottom");
				}
			}
			row.appendChild(cell);
		}

		
		if(xSafari && xDef(obj.innerHTML)){
			var temp = "<table class='month_selector_table' cellpadding='0' cellspacing='0' border='0'>";
			var attr;
			var row;
			for(var i=0;i<safari_table_rows.length;i++){
				attr = "";
				row = safari_table_rows[i];
				if(xStr(row.getAttribute("class"))){
					attr = "class=\'" + row.getAttribute("class") + "\'";
				}
				temp += "<tr " + attr + ">" + row.innerHTML + "</tr>";
			}
			temp += "</table>";
			obj.innerHTML = temp;
		}else
		if(xIE4Up && xDef(obj.innerHTML) && xDef(table.outerHTML)){
			obj.innerHTML = table.outerHTML;
		}else{
			obj.appendChild(table);
		}
		
		return true;
	}else{
		alert("invalid parameters to buildMonth");
	}
}
