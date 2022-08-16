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
	
	
	public static function EnqueueResources()
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
	
	
	public static function SkillShortCode( $attrs, $content, $tag )
	{
		$isMobile = wp_is_mobile();
		$output = "<div class=\"has-text-align-center\"><div class=\"uespEsoSkillBar\">";
		
		$version = $attrs['version'];
		if ($version == null || $version == '') $version = "current";
		$version = preg_replace('/[^0-9a-z_]/i', '', $version);
		
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
				$output .= "<img src=\"$src\" skillname=\"$skillName\" ismobile=\"$isMobile\" version=\"$version\" class=\"uespEsoSkillIcon\" />";
				if (!$isMobile) $output .= "</a>";
				$output .= "</div>";
			}
		}
		
		$output .= "</div></div>";
		return $output;
	}
	
	
	public static function ServerStatusShortCode ( $attrs, $content, $tag )
	{
		$content = trim($content);
		if ($content != "") $content = "<h1>$content</h1>";
		
		$output = "<div class='uespEsoServerStatusRoot'>$content</div>";
		return $output;
	}
	
	
	function StatusSectionText()
	{
    	echo '<p>Manually set the status of Twitch Drops and Events.</p>';
	}
	
	
	function ShowTwitchDropsSetting()
	{
	    $options = get_option( 'uespesodata_settings' );
	    echo "<input id='uespesodata_settings_twitchdrops' name='uespesodata_settings[twitchdrops]' type='checkbox' value='1' " .  checked($options['twitchdrops'] == 1, true, false) . " />";
	}
	
	
	function ShowIngameEventsSetting()
	{
	    $options = get_option( 'uespesodata_settings' );
	    echo "<input id='uespesodata_settings_ingameevents' name='uespesodata_settings[ingameevents]' type='checkbox' value='1' " .  checked($options['ingameevents'] == 1, true, false) . " />";
	}
	
	
	function RegisterSettings()
	{
		add_options_page("UESP ESO Data", "UESP ESO Data", "manage_options", "UespEsoDataOptionsMenu", 'CUespEsoWordPressPlugin::OptionsMenu');
		
	    register_setting( 'uespesodata_settings', 'uespesodata_settings');
	    add_settings_section( 'status_settings', 'Status Settings', 'StatusSectionText', 'UespEsoDataOptionsMenu' );
	
    	add_settings_field( 'uespesodata_settings_twitchdrops', 'Twitch Drops', 'CUespEsoWordPressPlugin::ShowTwitchDropsSetting', 'UespEsoDataOptionsMenu', 'status_settings' );
		add_settings_field( 'uespesodata_settings_ingameevents', 'Ingame Events', 'CUespEsoWordPressPlugin::ShowIngameEventsSetting', 'UespEsoDataOptionsMenu', 'status_settings' );
	}
	
	
	public static function OptionsMenu()
	{
		 ?>
		<h2>UESP ESO Data Plugin Settings</h2>
		<form action="options.php" method="post">
			<?php 
			settings_fields( 'uespesodata_settings' );
			do_settings_sections( 'UespEsoDataOptionsMenu' ); ?>
			<input name="submit" class="button button-primary" type="submit" value="<?php esc_attr_e( 'Save' ); ?>" />
		</form>
		<?php
	}
	
	
	public static function TwitchDropsStatusShortCode( $attrs, $content, $tag )
	{
		$showLink = $attrs['link'];
		if ($showLink == null) $showLink = true;
		
		$options = get_option( 'uespesodata_settings' );
		$twitchDrops = $options['twitchdrops'];
		$output = "";
		
		if ($showLink) $output = '<a href="https://deltiasgaming.com/twitch-drops">';
		
		if ($twitchDrops == 1)
		{
			$output .= '<div class="uespEsoServer"><div class="uespEsoServerTitle">Twitch Drops:</div> <div class="uespEsoStatusUp">Active</div></div>';
		}
		else
		{
			$output .= '<div class="uespEsoServer"><div class="uespEsoServerTitle">Twitch Drops:</div> <div class="uespEsoStatusDown">None</div></div>';
		}
		
		if ($showLink) $output .= '</a>';
		return $output;
	}
	
	
	public static function IngameEventsStatusShortCode( $attrs, $content, $tag )
	{
		$showLink = $attrs['link'];
		if ($showLink == null) $showLink = true;
		
		$options = get_option( 'uespesodata_settings' );
		$twitchDrops = $options['ingameevents'];
		$output = "";
		
		if ($showLink) $output = '<a href="https://deltiasgaming.com/in-game-events">';
		
		if ($twitchDrops == 1)
		{
			$output .= '<div class="uespEsoServer"><div class="uespEsoServerTitle">Ingame Events:</div> <div class="uespEsoStatusUp">Active</div></div>';
		}
		else
		{
			$output .= '<div class="uespEsoServer"><div class="uespEsoServerTitle">Ingame Events:</div> <div class="uespEsoStatusDown">None</div></div>';
		}
		
		if ($showLink) $output .= '</a>';
		return $output;
	}
	
	
	public static function EndeavorShortCode( $attrs, $content, $tag )
	{
		$showAll = intval($attrs['showall']);
		
		$output = file_get_contents("https://esolog.uesp.net/getEndeavorHtml.php?showall=$showAll");
		
		return $output;
	}
	
	
	public static function GoldenVendorShortCode( $attrs, $content, $tag )
	{
		$showAll = intval($attrs['showall']);
		
		$output = file_get_contents("https://esolog.uesp.net/getGoldenVendorHtml.php?showall=$showAll");
		
		return $output;
	}
	
};


add_action( 'wp_enqueue_scripts', 'CUespEsoWordPressPlugin::EnqueueResources' );
add_action( 'admin_menu', 'CUespEsoWordPressPlugin::RegisterSettings' );

add_shortcode('uesp_esoskillbar', 'CUespEsoWordPressPlugin::SkillShortCode');
add_shortcode('uesp_esoserverstatus', 'CUespEsoWordPressPlugin::ServerStatusShortCode');
add_shortcode('uesp_esotwitchdrops', 'CUespEsoWordPressPlugin::TwitchDropsStatusShortCode');
add_shortcode('uesp_esoingameevents', 'CUespEsoWordPressPlugin::IngameEventsStatusShortCode');
add_shortcode('uesp_esoendeavors', 'CUespEsoWordPressPlugin::EndeavorShortCode');
add_shortcode('uesp_esogoldenvendor', 'CUespEsoWordPressPlugin::GoldenVendorShortCode');


