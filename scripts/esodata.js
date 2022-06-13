window.uespEsoDataOnDocumentLoaded = function()
{
	var serverStatusDivs = jQuery(".uespEsoServerStatusRoot");
	
	if (serverStatusDivs.length > 0)
	{
		jQuery.ajax({
			url: "https://esolog.uesp.net/getEsoServerStatus.php",
			success: window.uespEsoDataOnReceiveServerStatus,
		});
	
	}
	
	setInterval(uespEsoDataUpdateGoldenVendor, 300000);
	setInterval(uespEsoDataUpdateServerStatus, 60000);
	uespEsoDataUpdateGoldenVendor(true);
	
	jQuery('.uespesoItemLink').hover(OnEsoItemLinkEnter, OnEsoItemLinkLeave);
}


window.uespEsoDataOnReceiveServerStatus = function (data)
{
	var serverStatusDivs = jQuery(".uespEsoServerStatusRoot");
	
	//console.log("uespEsoDataOnReceiveServerStatus", data);
	serverStatusDivs.html(serverStatusDivs.html() + data);
}


window.uespEsoDataIsGoldenVendorTime = function(date)
{
	var dayOfWeek = date.getUTCDay();
	var hour = date.getUTCHours();
	
	if (dayOfWeek == 6 || dayOfWeek == 0) return true;
	if (dayOfWeek == 1 && hour < 12) return true;
	
	return false;
}


window.uespEsoDataUpdateServerStatus = function(forceUpdate)
{
	var status = jQuery(".uespEsoServerStatusRoot");
	
	if (status.length == 0) return;
	
	jQuery.ajax({
		url: '//esolog.uesp.net/getEsoServerStatus.php',
		type: 'get',
		context: status,
		dataType: 'html',
		cache: false,
		success: OnReceiveUespEsoServerStatus,
		async:true,
	});
}


window.OnReceiveUespEsoServerStatus = function(data)
{
	jQuery(this).html(data);
}


window.uespEsoDataUpdateGoldenVendor = function(forceUpdate)
{
	var element = jQuery(".uespEsoGoldenVendorStatus");
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


window.EsoItemLinkPopup = null;
window.EsoItemLinkPopup_LastElement = null;
window.EsoItemLinkPopup_Visible = false;
window.EsoItemLinkPopup_CacheId = "";
window.EsoItemLinkPopup_Cache = { };


window.CreateEsoItemLinkPopup = function()
{
	EsoItemLinkPopup = jQuery('<div />').addClass('eso_item_link_popup').hide();
	jQuery('body').append(EsoItemLinkPopup);
}


window.ShowEsoItemLinkPopup = function (parent, itemId, level, quality, showSummary, intLevel, intType, itemLink, setCount, questId, collectId, enchantId, enchantIntLevel, enchantIntType, enchantFactor, potionData, extraData, version, extraArmor, trait, antiquityId, weaponTraitFactor)
{
	EsoItemLinkPopup_LastElement = parent;
	
	//var linkSrc = "//esoitem.uesp.net/itemLink.php?&embed";
	var linkSrc = "//esoitem.uesp.net/itemLinkImage.php?";
	var dataOk = false;
	
	if (antiquityId) { linkSrc += "&antiquityid=" + antiquityId; dataOk = true; }
	if (questId) { linkSrc += "&questid=" + questId; dataOk = true; }
	if (collectId) { linkSrc += "&collectid=" + collectId; dataOk = true; }
	if (itemId) { linkSrc += "&itemid=" + itemId; dataOk = true; }
	if (itemLink) { linkSrc += "&link=\'" + encodeURIComponent(itemLink) + "\'"; dataOk = true; }
	if (intLevel) linkSrc += "&intlevel=" + intLevel;
	if (intType) linkSrc += "&inttype=" + intType;
	if (level) linkSrc += "&level=" + level;
	if (quality) linkSrc += "&quality=" + quality;
	if (enchantId) linkSrc += "&enchantid=" + enchantId;
	if (enchantIntLevel) linkSrc += "&enchantintlevel=" + enchantIntLevel;
	if (enchantIntType) linkSrc += "&enchantinttype=" + enchantIntType;
	if (enchantFactor) linkSrc += "&enchantfactor=" + enchantFactor;
	if (potionData) linkSrc += "&potiondata=" + potionData;
	if (extraData) linkSrc += "&extradata=" + extraData;
	if (extraArmor) linkSrc += "&extraarmor=" + extraArmor;
	if (version) linkSrc += "&version=" + version;
	if (trait) linkSrc += "&trait=" + trait;
	if (weaponTraitFactor) linkSrc += "&weapontraitfactor=" + weaponTraitFactor;
	if (showSummary) linkSrc += "&summary";
	if (setCount != null && setCount >= 0) linkSrc += "&setcount=" + setCount;
	
	if (!dataOk) return false;
	
	if (EsoItemLinkPopup == null) CreateEsoItemLinkPopup();
	
	var position = jQuery(parent).offset();
	var width = jQuery(parent).width();
	EsoItemLinkPopup.css({ top: position.top-50, left: position.left + width });
	EsoItemLinkPopup_Visible = true;
	
	var cacheId = "";
	
	if (itemLink)
	{
		cacheId = itemLink.toString();
	}
	else if (intLevel && intType)
	{
		cacheId = itemId.toString() + "_INT_" + intLevel.toString() + "_" + intType.toString();		
	}
	else if (itemId) 
	{
		cacheId = itemId.toString();
	}
	else if (questId) 
	{
		cacheId = "Q-" + questId.toString();
	}
	else if (collectId) 
	{
		cacheId = "C-" + collectId.toString();
	}
	else if (antiquityId) 
	{
		cacheId = "A-" + antiquityId.toString();
	}
	
	if (level) cacheId += "-L" + level.toString();
	if (quality) cacheId += "-Q" + quality.toString();
	if (showSummary) cacheId += "-S";
	if (setCount) cacheId += "-SC" + setCount.toString();
	if (enchantFactor) cacheId += "-EF" + enchantFactor.toString();
	if (potionData) cacheId += "-PD" + potionData.toString();
	if (extraData) cacheId += "-EX" + extraData.toString();
	if (extraArmor) cacheId += "-AR" + extraArmor.toString();
	if (trait) cacheId += "-TR" + trait.toString();
	if (weaponTraitFactor) cacheId += "-WT" + weaponTraitFactor.toString();
	
	if (enchantId)
	{
		cacheId += "-E" + enchantId.toString() + "-" + enchantIntLevel.toString() + "-" + enchantIntType.toString();
	}
	
	EsoItemLinkPopup_CacheId = cacheId;
	
	if (cacheId != "" && EsoItemLinkPopup_Cache[cacheId] != null)
	{
		EsoItemLinkPopup.html(EsoItemLinkPopup_Cache[cacheId]);
		EsoItemLinkPopup.show();
		
		AdjustEsoItemLinkTooltipPosition(EsoItemLinkPopup, jQuery(parent));
	}
	else
	{
		EsoItemLinkPopup.empty();
		
		var img = jQuery("<img>").attr("src", linkSrc).one("load", function() {
			AdjustEsoItemLinkTooltipPosition(EsoItemLinkPopup, jQuery(parent));
		});
		
		EsoItemLinkPopup.html(img);
		
		if (EsoItemLinkPopup_Visible) EsoItemLinkPopup.show();
		if (cacheId != "" && cacheId == EsoItemLinkPopup_CacheId) EsoItemLinkPopup_Cache[cacheId] = img;
	}
}


window.AdjustEsoItemLinkTooltipPosition = function(tooltip, parent)
{
	var windowWidth = Math.max(document.documentElement.clientWidth, window.innerWidth || 0);
	var windowHeight = Math.max(document.documentElement.clientHeight, window.innerHeight || 0);
	var toolTipWidth = tooltip.width();
	var toolTipHeight = tooltip.height();
	var elementHeight = parent.height();
	var elementWidth = parent.width();
	var NARROW_WINDOW_WIDTH = 800;
	 
	var top = parent.offset().top - 150;
	var left = parent.offset().left + parent.outerWidth() + 3;
	
	if (windowWidth < NARROW_WINDOW_WIDTH)
	{
		top = parent.offset().top - 25 - toolTipHeight;
		left = parent.offset().left - toolTipWidth/2 + elementWidth/2;
	}
	 
	tooltip.offset({ top: top, left: left });
	 
	var viewportTooltip = tooltip[0].getBoundingClientRect();
	 
	if (viewportTooltip.bottom > windowHeight) 
	{
		var deltaHeight = viewportTooltip.bottom - windowHeight + 10;
		top = top - deltaHeight;
	}
	else if (viewportTooltip.top < 0)
	{
		var deltaHeight = viewportTooltip.top - 10;
		
		if (windowWidth < NARROW_WINDOW_WIDTH) deltaHeight = -toolTipHeight - elementHeight - 30;
		
		top = top - deltaHeight;
	}
		 
	if (viewportTooltip.right > windowWidth) 
	{
		var deltaLeft = -toolTipWidth - parent.width() - 28;
		
		if (windowWidth < NARROW_WINDOW_WIDTH)
		{
			deltaLeft = windowWidth - viewportTooltip.right - 10;
		}
		
		left = left + deltaLeft;
	}
	
	if (viewportTooltip.left < 0)
	{
		if (windowWidth < NARROW_WINDOW_WIDTH)
			left = left - viewportTooltip.left + 10;
		else
			left = left;
	}
	 
	tooltip.offset({ top: top, left: left });
	viewportTooltip = tooltip[0].getBoundingClientRect();
	 
	if (viewportTooltip.left < 0 )
	{
		//var el = jQuery('<i/>').css('display','inline').insertBefore(parent[0]);
		//var realOffset = el.offset();
		//el.remove();
		 
		//left = realOffset.left - toolTipWidth - 3;
		
		left = left - viewportTooltip.left + 10;
		
		tooltip.offset({ top: top, left: left });
	}
	 
}


window.HideEsoItemLinkPopup = function()
{
	EsoItemLinkPopup_Visible = false;
	if (EsoItemLinkPopup == null) return;
	EsoItemLinkPopup.hide();
}


window.OnEsoItemLinkEnter = function()
{
	var $this = jQuery(this);
	EsoItemLinkPopup_LastElement = $this;
	
	ShowEsoItemLinkPopup(EsoItemLinkPopup_LastElement, $this.attr('itemid'), $this.attr('level'), $this.attr('quality'), 
			$this.attr('summary'), $this.attr('intlevel'), $this.attr('inttype'), $this.attr('itemlink'), $this.attr('setcount'),
			$this.attr('questid'), $this.attr('collectid'), $this.attr('enchantid'), $this.attr('enchantintlevel'),
			$this.attr('enchantinttype'), $this.attr('enchantfactor'), $this.attr('potiondata'), $this.attr('extradata'),
			$this.attr('version'), $this.attr('extraarmor'), $this.attr('trait'), $this.attr('antiquityid'),
			$this.attr('weapontraitfactor'));
}


window.OnEsoItemLinkLeave = function()
{
	EsoItemLinkPopup_LastElement = null;
	HideEsoItemLinkPopup();
}


jQuery(uespEsoDataOnDocumentLoaded);