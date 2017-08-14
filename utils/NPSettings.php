<?php
class NPSettings {
	
	const API_URL = 'api_url';
	const EMANDATE_URL = 'emandate_url';
	const PLUGIN_VER = 'NUAPAY_PLUGIN_VERSION';
	
	public static function updatePluginVersion($value) {
		update_option(self::PLUGIN_VER, $value);
	}
	
	public static function getPluginVersion() {
		return get_option(self::PLUGIN_VER, '0');
	}
	
	public static function updateApiUrl($value) {
		$options = get_option('np_form_options');
		$options[self::API_URL] = $value; 
		update_option('np_form_options', $options);
	}
	
	public static function getApiUrl() {
		$options = get_option('np_form_options');
		return $options[self::API_URL];
	}
}