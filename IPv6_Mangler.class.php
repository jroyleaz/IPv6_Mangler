<?php

class IPv6_Mangler {
	
	public static $ipv6_filter = "/(([0-9a-f]{0,4}):{1,2}){1,7}([0-9a-z]){1,4}/";
	public static $ipv6_loopback = "/([0]{1,4}:){7}([0]{0,3}[1])/";
	public static $ipv6_unspecified = "/([0]{1,4}:){7}([0]{0,3}[1])/";

	public static function ValidateIPv6Address($ipv6_address) {
		// Validate using the inet_pton built-in, remove subnet mask if any
		if(@inet_pton(IPv6_Mangler::_removeSubnetMask($ipv6_address))) {
		//if(preg_match(IPv6_Mangler::$ipv6_filter, $ipv6_address)) {
			return TRUE;
		} else {
			// Otherwise throw an exception for catching
			//throw new Exception(__METHOD__ . inet_pton(IPv6_Mangler::_removeSubnetMask($ipv6_address)));
			return FALSE;
		}
	}

	public static function IncrementIPv6Address($ipv6_address) {
		try {
			
			// Validate $ipv6_address
			IPv6_Mangler::ValidateIPv6Address($ipv6_address);
			
			// Make sure the address isn't compressed
			$ipv6_address = IPv6_Mangler::ExpandIPv6Address($ipv6_address);
			
			// Break it into component hextets (remove subnet mask if any)
			$hextets = preg_split('/:/', IPv6_Mangler::_removeSubnetMask($ipv6_address));
			
			// Loop through the index from right to left
			for($i = 7; ; $i--) {
				// If the hextet isn't already "maxed" at 'ffff' then do the needful
				if($hextets[$i] != "ffff") {
					// flip to dec, add 1, flip to hex, zeropad on the left
					//$hextets[$i] = str_pad((dechex(hexdec($hextets[$i]) + 1)), 4,"0",STR_PAD_LEFT);
					$hextets[$i] = dechex(hexdec($hextets[$i]) + 1);
					break;
				} // Implicit else, continue on to the next block to the left
			}
			
			// Return the results, stitched back together
			return implode(":", $hextets);
			
		} catch (Exception $e) {
			
			// If anything went wrong, die and try to explain what/why
			die("<p>Error: " . $e->getMessage() . " for $ipv6_address</p>");
		}
	}
	
	public static function CollapseIPv6Address($ipv6_address) {
		
		if(preg_match(IPv6_Mangler::$ipv6_unspecified, $ipv6_address)) {
			$ipv6_address = "::";
		} elseif(preg_match(IPv6_Mangler::$ipv6_loopback, $ipv6_address)) {
			$ipv6_address = "::1";	
		} elseif(preg_match('/::/', $ipv6_address)) {
			$ipv6_address = $ipv6_address;	
		}
		
		try {
			// Validate $ipv6_address
			IPv6_Mangler::ValidateIPv6Address($ipv6_address);
			
			// Remove the double 0000 blocks, if any (only do one block though, per the RFC)
			$ipv6_address = preg_replace('/:0{1,4}:0{1,4}:/', '::', $ipv6_address, 1);
			
			// Replace any leading zeros with single zero
			$ipv6_address = preg_replace('/:0{1,3}/', ':', $ipv6_address);
			
		} catch (Exception $e) {
			// If anything went wrong, die and try to explain what/why
			die(__METHOD__ . " : " . $e->getMessage());
		}
		
		// Return the collapsed address
		return strtolower($ipv6_address);
	}
	
	public static function ExpandIPv6Address($ipv6_address) {
		// Two edge cases for the loopback and unspecified address
		switch($ipv6_address) {
			case "::":
				return "0:0:0:0:0:0:0:0";
				break;
			case "::1":	
				return "0:0:0:0:0:0:0:1";
				break;
			default:
				// If there are any ::'s, replace them with :0:0:
				return strtolower(preg_replace('/::/', ':0:0:', $ipv6_address, 1));
				break;
		}		
	}
	
	private static function _removeSubnetMask($ipv6_address) {
		// Internatl function to clean up the subnet mask if left on, also strtolower since the IPv6 RFC states that address are in lowercase
		return strtolower(preg_replace('/\/[0-9]*$/', '', trim($ipv6_address)));
	}
	
}

?>