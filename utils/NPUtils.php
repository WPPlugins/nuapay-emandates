<?php
class NPUtils {
	
	public static function render($name, array $args = array()) {
		foreach ($args as $key => $val) {
			$$key = $val;
		}
		$file = NUAPAY_PLUGIN_DIR . 'views/'. $name . '.php';
		include($file);
	}
	
	public static function i18($text, array $args = array()) {
		return vsprintf(__($text, NUAPAY_TEXT_DOMAIN), $args);
	}

	public static function dump($data) {
		$output = highlight_string("<?php\n" . var_export($data, true), true);
		echo '<p>';
		echo preg_replace('/&lt;\\?php<br \\/>/', '', $output, 1);
		echo '</p>';
	}
}