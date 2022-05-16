
Plugin: UESP ESO Data
Author: Dave Humphrey (dave@uesp.net)
License: Apache v2

This plugin adds skill and item tooltips for ESO using the UESP.net data services.

Supported Shortcodes:

uesp_esoskillbar
==========================================
	ex: [uesp_esoskillbar version="" 1="dragonknight/ardent-flame/dragonknight-standard" 2="alliance-war/assault/aggressive-horn" ...]
	
	Display a standard ESO skillbar with up to 6 skills.
	     1-6: The skills to display in the format:
	                    CLASS/SKILL-LINE/SKILL-NAME
	          in lowercase with all spaces converted to dashes (-). 
	     version: Optional field to specify a game update ("33", "34pts", etc...).
	
	See example at: https://blog.uesp.net/test-eso-skillbar/ 