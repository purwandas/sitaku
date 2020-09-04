function getDay(date){
	var res = date.split("-");
	return res[2];
}

function getMonth(date){
	var res = date.split("-");
	return res[1];
}

function getYear(date){
	var res = date.split("-");
	return res[0];
}

function convertMonth(month){
	if(month == "01") return "January";
	if(month == "02") return "February";
	if(month == "03") return "March";
	if(month == "04") return "April";
	if(month == "05") return "May";
	if(month == "06") return "June";
	if(month == "07") return "July";
	if(month == "08") return "August";
	if(month == "09") return "September";
	if(month == "10") return "October";
	if(month == "11") return "November";
	if(month == "12") return "December";
}

function getHour(time){
	var res = time.split(":");
	return res[0];
}

function getMinute(time){
	var res = time.split(":");
	return res[1];
}

function getSecond(time){
	var res = time.split(":");
	return res[2];
}

function dateFormat(datetime,format){
	var date = datetime.split(" ")[0]; 
	format = format.replace("%d",getDay(date));
	
	if(format.search("%F") >= 0){
		format = format.replace("%F",convertMonth(getMonth(date)));
	}else{
		format = format.replace("%m",getMonth(date));
	}
	
	if(format.search("%Y") >= 0){
		format = format.replace("%Y",getYear(date));
	}else{
		format = format.replace("%y",getYear(date).slice(-2));
	}
	
	return format;
}

function timeFormat(datetime,format){
	var time = datetime.split(" ")[1]; 
	format = format.replace("%H",getHour(time));
	format = format.replace("%i",getMinute(time));
	return format;
}