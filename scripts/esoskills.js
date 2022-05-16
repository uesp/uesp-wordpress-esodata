

window.EsoShowPopupSkillTooltip = function(skillHtml, parent)
{
	var popupElement = jQuery("#esovsPopupSkillTooltip");
	
	if (popupElement.length == 0)
	{
		jQuery("body").append('<div id="esovsPopupSkillTooltip"></div>');
		popupElement = jQuery("#esovsPopupSkillTooltip");
	}
	
	if (skillHtml == null)
	{
		popupElement.hide();
		return;
	}
	
	popupElement.html(skillHtml);
	popupElement.show();
	
	AdjustEsoSkillPopupTooltipPosition(popupElement, jQuery(parent));
}


window.AdjustEsoSkillPopupTooltipPositionMobile = function (tooltip, parent)
{
	if (tooltip == null) return;
	if (tooltip[0] == null) return;
	if (parent == null) return;
	
	var windowWidth = jQuery(window).width();
	var windowHeight = jQuery(window).height();
	var toolTipWidth = tooltip.width();
	var toolTipHeight = tooltip.height();
	var elementHeight = parent.height();
	var elementWidth = parent.width();
	
	var top = parent.offset().top + elementHeight + 10;
	var left = windowWidth/2 - toolTipWidth/2;
	
	tooltip.offset({ top: top, left: left });
}


window.AdjustEsoSkillPopupTooltipPosition = function (tooltip, parent)
{
	if (tooltip == null) return;
	if (tooltip[0] == null) return;
	if (parent == null) return;
	
	var isMobile = parent.attr("isMobile");
	if (isMobile) return window.AdjustEsoSkillPopupTooltipPositionMobile(tooltip, parent)
	
	var windowWidth = jQuery(window).width();
	var windowHeight = jQuery(window).height();
	var toolTipWidth = tooltip.width();
	var toolTipHeight = tooltip.height();
	var elementHeight = parent.height();
	var elementWidth = parent.width();
	
	var top = parent.offset().top - toolTipHeight/2 + elementHeight/2;
	var left = parent.offset().left + parent.outerWidth() + 3;
	
	tooltip.offset({ top: top, left: left });
	
	var viewportTooltip = tooltip[0].getBoundingClientRect();
	
	if (viewportTooltip.bottom > windowHeight) 
	{
		var deltaHeight = viewportTooltip.bottom - windowHeight + 10;
		top = top - deltaHeight
	}
	else if (viewportTooltip.top < 0)
	{
		var deltaHeight = viewportTooltip.top - 10;
		top = top - deltaHeight
	}
	
	if (viewportTooltip.right > windowWidth) 
	{
		left = left - toolTipWidth - parent.width() - 28;
	}
	
	tooltip.offset({ top: top, left: left });
	viewportTooltip = tooltip[0].getBoundingClientRect();
	
	if (viewportTooltip.left < 0)
	{
		var el = jQuery('<i/>').css('display','inline').insertBefore(parent[0]);
		var realOffset = el.offset();
		el.remove();
		
		left = realOffset.left - toolTipWidth - 3;
		tooltip.offset({ top: top, left: left });
	}
}


function OnEsoDataSkillClientHover(e)
{
	var element = jQuery(this).find(".uespEsoSkillIcon");
	var skillid = element.attr("skillid");
	var skillName = element.attr("skillname");
	var isMobile = element.attr("isMobile");
	
	if ((skillid == null || skillid == "") && (skillName == null || skillName == "")) 
	{
		return;
	}
	
	jQuery.ajax({
		url: '//esolog.uesp.net/skillTooltip.php',
		data:  { 'id' : skillid, 'name' : skillName, 'includelink' : isMobile },
		type: 'get',
		context: element,
		dataType: 'html',
		cache: false,
		success: OnReceiveEsoDataSkillClientData,
		async:true,
	});
	
	//EsoShowPopupSkillTooltip(skillData, jQuery(this)[0]);
}


function OnReceiveEsoDataSkillClientData(skillData)
{
	EsoShowPopupSkillTooltip(skillData, jQuery(this));
}


function OnEsoDataSkillClientLeave(e)
{
	var popupElement = jQuery("#esovsPopupSkillTooltip");
	popupElement.hide();
}


function EsoDataSkillClientOnReady()
{
	
	/*if (g_EsoSkillIsMobile)
	{
		jQuery(".eso_skill_tooltip").click(function(e) {
			setTimeout(function() {	OnEsoSkillClientHover.bind(this, e); }, 250); 
			e.preventDefault(); 
			e.stopPropagation(); 
			return false; });
	} */
	
	jQuery(".eso_skill_tooltip").hover(OnEsoDataSkillClientHover, OnEsoDataSkillClientLeave);
	jQuery(".uespEsoSkillIconDiv").hover(OnEsoDataSkillClientHover, OnEsoDataSkillClientLeave);
}



jQuery( EsoDataSkillClientOnReady );