<?php
	# Automaticky odhlasuje iba provozniho a majitele
	# ohlasovani cisnika a kuchare nedava smysl.
	function timeout() {
		#$time = time ();
		$timeout = time () + 900;
	    #if (empty ( $_SESSION ['timeout'] ) || !isset ( $_SESSION ['timeout'] )) {
	        $_SESSION ['timeout'] = $timeout;
	    #}
	}
?>