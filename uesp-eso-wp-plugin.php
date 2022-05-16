<?php
/*
	Plugin Name: UESP ESO Data Plugin
	Plugin URI: https://uesp.net
	Description: Loads and displays ESO skill and item tooltips from UESP.net. 
	Version: 0.1
	Author: Daveh
	Author URI: https://uesp.net/wiki/User:Daveh
	License: MIT
*/

class CUespEsoWordPressPlugin
{
	
	public static $ICON_BASE_URL = "https://esoicons.uesp.net/uespskills";
	public static $DEST_BASE_URL = "https://en.uesp.net/wiki/Online:";
	
	
	public static function uespEsoDataEnqueueResources()
	{
		wp_enqueue_style( 'esoskills', 'https://esolog.uesp.net/resources/esoskills_embed.css' );
		wp_enqueue_style( 'esoskillclient', 'https://esolog.uesp.net/resources/esoSkillClient.css' );
		
		wp_enqueue_script( 'esoskills', plugin_dir_url(__FILE__) . 'scripts/esoskills.js', array( 'jquery' ) );
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
				$skillName = preg_replace('#[ :"<>]#', '-', $skillName);
				
				$src = self::$ICON_BASE_URL . "/$version/$skillName.png";
				
				$result = preg_match('#(.*)/(.*)/(.*)#', $skillName, $matches);
				
				if ($result)
				{
					$articleName = preg_replace('#-#', ' ', $matches[3]);
					$articleName = ucwords($articleName);
				}
				else
				{
					$articleName = preg_replace('#-#', ' ', $skillName);
					$articleName = ucwords($articleName);
				}
				
				$destUrl = self::$DEST_BASE_URL . $articleName;
				
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
	
};

add_action( 'wp_enqueue_scripts', 'CUespEsoWordPressPlugin::uespEsoDataEnqueueResources' );
add_shortcode('uesp_esoskillbar', 'CUespEsoWordPressPlugin::uespEsoDataSkillShortCode');

