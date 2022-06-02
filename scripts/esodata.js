window.uespEsoDataOnDocumentLoaded = function()
{
	var serverStatusDivs = jQuery(".uespEsoServerStatus");
	
	if (serverStatusDivs.length > 0)
	{
		jQuery.ajax({
			url: "https://esolog.uesp.net/getEsoServerStatus.php",
			success: window.uespEsoDataOnReceiveServerStatus,
		});
	
	}
	
	setInterval(uespEsoDataUpdateGoldenVendor, 60000);
	uespEsoDataUpdateGoldenVendor(true);
}


window.uespEsoDataOnReceiveServerStatus = function (data)
{
	var serverStatusDivs = jQuery(".uespEsoServerStatus");
	
	//console.log("uespEsoDataOnReceiveServerStatus", data);
	serverStatusDivs.html(serverStatusDivs.html() + data);
}


window.uespEsoDataIsGoldenVendorTime = function(date)
{
	var dayOfWeek = date.getUTCDay();
	var hour = date.getUTCHours();
	
	if (dayOfWeek == 6 || dayOfWeek == 0) return true;
	if (dayOfWeek == 5 && hour >= 0) return true;
	if (dayOfWeek == 1 && hour < 12) return true;
	
	return false;
}


window.uespEsoDataUpdateGoldenVendor = function(forceUpdate)
{
	var element = jQuery("#uespEsoGoldenVendorStatus");
	var oldStatus = element.hasClass("uespEsoStatusUp");
	var today = new Date();
	var newStatus = uespEsoDataIsGoldenVendorTime(today);
	
	if (forceUpdate === true || newStatus != oldStatus)
	{
		element.removeClass("uespEsoStatusDown");
		element.removeClass("uespEsoStatusUp");
		
		if (newStatus)
		{
			element.text("Active");
			element.addClass("uespEsoStatusUp");
		}
		else
		{
			element.text("Inactive");
			element.addClass("uespEsoStatusDown");
		}
	}
	
}


jQuery(uespEsoDataOnDocumentLoaded);