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
}


window.uespEsoDataOnReceiveServerStatus = function (data)
{
	var serverStatusDivs = jQuery(".uespEsoServerStatus");
	
	//console.log("uespEsoDataOnReceiveServerStatus", data);
	serverStatusDivs.html(serverStatusDivs.html() + data);
}


jQuery(uespEsoDataOnDocumentLoaded);