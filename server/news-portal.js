function loadIndex() {
	$('#indexBarTable').html(" ");
	var timeNow = Date.now();
	var jqxhr = $.getJSON( "scripts.json.php?" + timeNow, function() {
	})
	.done(function( data ) {
	 $.each( data, function( i, item ) {
		$( "<tr onClick=\"loadItem(" + item.id + ")\"><td>" + item.title + "</td><td class=\"cat_" + item.category + "\">" + item.category + "</td><td class=\"date\">" + formatTimestampAsDate(item.addeddate) + "</td></tr>").appendTo( "#indexBarTable" );
        if ( i === 9 ) {
          return false;
        }
	});
	})
	.fail(function() {
		console.log( "Couldn't load scripts" );
	})
	
	setTimeout(loadIndex, 300000);
}


function loadItem(id) {
	$('#scriptcopied').hide();
	var timeNow = Date.now();
	var jqxhr = $.getJSON( "scripts.json.php?" + timeNow, function() {
	})
	.done(function( data ) {
	 $.each( data, function( i, item ) {
		 if (item.id == id)
		 {
			 var metadata = "<p><span class=\"key\">Title:</span> <span class=\"value\">" + item.title + "</span></p>";
			 metadata += "<p><span class=\"key\">Embargo until:</span> <span class=\"value\"";
			 if ((item.embargo*1000) > Date.now()) {metadata += " style=\"color:red\"";} 
			 if (!item.audiofile) {item.audiofile = "N/A";}
			 if (!item.cart) {item.cart = "N/A";}
			 metadata += ">" + formatTimestampAsDate(item.embargo) + "</span></p>";
			 metadata += "<p><span class=\"key\">Category:</span> <span class=\"value cat_" + item.category + "\">" + item.category + "</span></p>";
			 metadata += "<p><span class=\"key\">Audio file:</span> <span class=\"value\">" + item.audiofile + " (<b>Cart</b>: " + item.cart + ")</span></p>";
			 metadata += "<p><span class=\"key\">Audio credit:</span> <span class=\"value\">" + item.audiocredit + "</span></p>";
			 metadata += "<p><span class=\"key\">Added By:</span> <span class=\"value\">" + item.addedby + "</span></p>";
			 metadata += "<p><span class=\"key\">Added Date:</span> <span class=\"value\">" + formatTimestampAsDate(item.addeddate) + "</span></p>";
			 metadata += "<p><span class=\"key\">Script first used:</span> <span class=\"value\" id=\"scriptUsedDate\">" + formatTimestampAsDate(item.scriptused) + "</span></p>";
			 $('#metaData').html(metadata);
			 if (item.scriptused < 1)
			 {
				var markUsedButtons = "<div class=\"button\"><a href=\"#\" onclick=\"markScriptUsed(" + item.id + ")\">Mark script used</a>&nbsp;</div>";
			 }
			 else 
			 {
				var markUsedButtons = "";
			 }
			 var usetext = item.text;
			 usetext = usetext.replace(/(?:\\r\\n|\\r|\\n)/g, '<br />');
			 $('#script').html("<p id=\"scripttext\">" + usetext + "</p>" + markUsedButtons + "</div>");
	
			 
			 return true;

		}
	});
	})
	
}

function formatTimestampAsDate(timestamp) {
	var tmpdate = new Date(timestamp*1000);
	if (tmpdate < 1001) {return "N/A";}
	var tmpdateText = tmpdate.getDate() + "/" + zeroPad(tmpdate.getMonth()+1) + "/" + (tmpdate.getYear()+1900) + " " + zeroPad(tmpdate.getHours()) + ":" + zeroPad(tmpdate.getMinutes());
	return tmpdateText;
}

function markScriptUsed(id) {
	// TODO: Ping back to db
	if ($('#scriptUsedDate').html() == "N/A")
	{
		$('#scriptUsedDate').html(formatTimestampAsDate(Date.now() / 1000));
		var req = $.ajax({
			url: "http://localhost:8002/server/mark_script_used.php?id=" + id,
			dataType: "jsonp",
			timeout: 5000,
			jsonpCallback: "handleResponse"
		});
	}
}

function handleResponse()
{
	console.write(response.message);
}

function zeroPad(number) {
	if (number < 10) {return "0" + number;}
	return number;
}