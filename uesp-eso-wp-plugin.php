<?php
/*
	Plugin Name: UESP ESO Data
	Plugin URI: https://uesp.net
	Description: Loads and displays ESO skill and item tooltips from UESP.net. 
	Version: 0.1
	Author: Daveh
	Author URI: https://uesp.net/wiki/User:Daveh
	License: MIT
	
	Made to be compatible/similar to the ESO-HUB plugin so minimum effort is needed to switch between the two.
*/

class CUespEsoWordPressPlugin
{
	
	public static $ICON_BASE_URL = "https://esoicons.uesp.net/uespskills";
	public static $DEST_BASE_URL = "https://en.uesp.net/wiki/Online:";
	
		/* Need to fix skill names with ' in them in order to get the correct wiki article name */
	public static $SKILLNAME_FIXUP = array(
			"mages-fury" => "Mages' Fury",
			"mages-wrath" => "Mages' Wrath",
			"vampires-bane" => "Vampire's Bane",
			"mountains-blessing" => "Mountain's Blessing",
			"assassins-blade" => "Assassin's Blade",
			"killers-blade" => "Killer's Blade",
			"hunters-eye" => "Hunter's Eye",
			"reapers-mark" => "Reaper's Mark",
			"hircines-bounty" => "Hircine's Bounty",
			"hircines-rage" => "Hircine's Rage",
			"hircines-fortitude" => "Hircine's Fortitude",
			"yffres-endurance" => "Y'ffre's Endurance",
			"lights-champion" => "Light's Champion",
			"natures-grasp" => "Nature's Grasp",
			"natures-embrace" => "Nature's Embrace",
			"natures-gift" => "Nature's Gift",
			"falcons-swiftness" => "Falcon's Swiftness",
			"winters-revenge" => "Winter's Revenge",
			"syrabanes-boon" => "Syrabane's Boon",
			"summoners-armor" => "Summoner's Armor",
			"scriers-patience" => "Scrier's Patience",
			"excavators-reserves" => "Excavator's Reserves",
			"rourkens-rebuke" => "Rourken's Rebuke",
			"malacaths-vengeance" => "Malacath's Vengeance",
			"pariahs-resolve" => "Pariah's Resolve"
	);
	
	
	public static function uespEsoDataEnqueueResources()
	{
		wp_enqueue_style( 'uespesoskills', 'https://esolog.uesp.net/resources/esoskills_embed.css' );
		wp_enqueue_style( 'uespesoskillclient', 'https://esolog.uesp.net/resources/esoSkillClient.css' );
		wp_enqueue_style( 'uespesodata', plugin_dir_url(__FILE__) . 'css/esodata.css' );
		
		wp_enqueue_script( 'uespesoskills', plugin_dir_url(__FILE__) . 'scripts/esoskills.js', array( 'jquery' ) );
		wp_enqueue_script( 'uespesodata', plugin_dir_url(__FILE__) . 'scripts/esodata.js', array( 'jquery' ) );
	}
	
	
	public static function fixupSkillName($skillName)
	{
		$newSkillName = self::$SKILLNAME_FIXUP[$skillName];
		if ($newSkillName != null) return $newSkillName;
		return $skillName;
	}
	
	
	public static function getWikiArticleUrl($skillName)
	{
		$result = preg_match('#(.*)/(.*)/(.*)#', $skillName, $matches);
		
		if ($result)
		{
			$articleName = self::fixupSkillName($matches[3]);
			$articleName = preg_replace('#-#', ' ', $articleName);
			$articleName = ucwords($articleName);
		}
		else
		{
			$articleName = self::fixupSkillName($skillName);
			$articleName = preg_replace('#-#', ' ', $articleName);
			$articleName = ucwords($articleName);
		}
		
		$destUrl = self::$DEST_BASE_URL . $articleName;
		return $destUrl;
	}
	
	
	public static function uespEsoDataSkillShortCode( $attrs, $content, $tag )
	{
		$isMobile = wp_is_mobile();
		$output = "<div class=\"has-text-align-center\"><div class=\"uespEsoSkillBar\">";
		
		$version = $attrs['version'];
		if ($version == null || $version == '') $version = "current";
		
		foreach ($attrs as $id => $value)
		{
			if ($id >= 1 && $id <= 6)
			{
				$skillName = strtolower($value);
				$skillName = str_replace('https://eso-hub.com/en/skills/', '', $skillName);
				$skillName = str_replace("'", '', $skillName);
				$skillName = preg_replace('#[ :"<>&]#', '-', $skillName);
				if ($skillName == 'n/a' || $skillName == '') continue;
				
				$isPassive = false;
				if (strstr($skillName, "racial/")) $isPassive = true;
				
				$src = self::$ICON_BASE_URL . "/$version/$skillName.png";
				
				$destUrl = self::getWikiArticleUrl($skillName);
				
				if ($isPassive)
					$output .= "<div class='uespEsoSkillIconDiv uespEsoSkillIconDivPassive'>";
				else
					$output .= "<div class='uespEsoSkillIconDiv'>";
				
				if (!$isMobile) $output .= "<a target=\"_blank\" href=\"$destUrl\">";
				$output .= "<img src=\"$src\" skillname=\"$skillName\" ismobile=\"$isMobile\" class=\"uespEsoSkillIcon\" />";
				if (!$isMobile) $output .= "</a>";
				$output .= "</div>";
			}
		}
		
		$output .= "</div></div>";
		return $output;
	}
	
	
	public static function uespEsoDataServerStatusShortCode ( $attrs, $content, $tag )
	{
		$content = trim($content);
		if ($content != "") $content = "<h1>$content</h1>";
		
		$output = "<div class='uespEsoServerStatus'>$content</div>";
		return $output;
	}
	
};

add_action( 'wp_enqueue_scripts', 'CUespEsoWordPressPlugin::uespEsoDataEnqueueResources' );
add_shortcode('uesp_esoskillbar', 'CUespEsoWordPressPlugin::uespEsoDataSkillShortCode');
add_shortcode('uesp_esoserverstatus', 'CUespEsoWordPressPlugin::uespEsoDataServerStatusShortCode');

