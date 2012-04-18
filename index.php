<?php 

include('IPv6_Mangler.class.php');

echo "<html><head><link rel='stylesheet' type='text/css' href='greyscale.css' /></head><body>";

echo "<form action='' method='post'>IPv6 Address: <input type='text' name='ipv6_address' id='ipv6_address' value='" . $_POST['ipv6_address'] . "'/>";
echo "<select name='res_count'>";
for($i = 25; $i < 200; $i = $i + 25) {
    if($_POST['res_count'] == $i) {
		echo "<option selected value=$i>$i</option>";
	} else {
		echo "<option value=$i>$i</option>";
	}
}
echo "</select><input type='submit'></form>";

if(array_key_exists('ipv6_address', $_POST) && array_key_exists('res_count', $_POST)) {
	try {
		// Set the Address from POST
		$ipv6_address = $_POST['ipv6_address'];
		
		// Collapse the IP to short form
		$ipv6_address_short = IPv6_Mangler::CollapseIPv6Address($ipv6_address);
		
		// Re-Expand it
		$ipv6_address_long = IPv6_Mangler::ExpandIPv6Address($ipv6_address);
		
		// Validate Long form
		$ipv6_validation_long = (IPv6_Mangler::ValidateIPv6Address($ipv6_address_long)) ? "YES" : "NO";
		
		// Validate Short form
		$ipv6_validation_short = (IPv6_Mangler::ValidateIPv6Address($ipv6_address_short)) ? "YES" : "NO";
		
		echo "<table><caption>$ipv6_address</caption><thead><tr><th>Long Form</th><th>Valid IP (Long)?</th><th>Short Form</th><th>Valid IP (Short)?</th></tr></thead><tbody>";
		echo "<tr><td>$ipv6_address_long</td><td>$ipv6_validation_long</td><td>$ipv6_address_short</td><td>$ipv6_validation_short</td></tr>";
		
		for($i = 1; $i <= $_POST['res_count']; $i++) {
			// Increment the IP address
			$ipv6_address = IPv6_Mangler::IncrementIPv6Address($ipv6_address);			
			
			// Collapse the IP to short form
			$ipv6_address_short = IPv6_Mangler::CollapseIPv6Address($ipv6_address);
			
			// Re-Expand it
			$ipv6_address_long = IPv6_Mangler::ExpandIPv6Address($ipv6_address);
			
			// Validate Long form
			$ipv6_validation_long = (IPv6_Mangler::ValidateIPv6Address($ipv6_address_long)) ? "YES" : "NO";
			
			// Validate Short form
			$ipv6_validation_short = (IPv6_Mangler::ValidateIPv6Address($ipv6_address_short)) ? "YES" : "NO";	
	
			echo "<tr><td>$ipv6_address_long</td><td>$ipv6_validation_long</td><td>$ipv6_address_short</td><td>$ipv6_validation_short</td></tr>";
		}
	} catch (Exception $e) {
			die("Error: " . __METHOD__ . " - " . $e->getMessage() . " for $ipv6_address or $ipv6_address_short");
			
	}
	echo "</tbody></table>";
}	
echo "</body></html>";