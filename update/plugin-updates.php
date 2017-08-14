<?php

function np_plugin_update($fromVersion, $toVersion) {

	$versions = array(
		'0' => 0,
		'1' => 1,  
		'1.0.1' => 2,
		'1.0.2' => 3,
		'1.0.3' => 4,
	);

	write_log('Upgrading version ' . $fromVersion . ' to version ' . $toVersion);

	if (!array_key_exists($fromVersion, $versions)) {
		write_log('Cannot find fromVersion ' . $fromVersion . ' in listed versions');
		return;
	}
	
	if (!array_key_exists($toVersion, $versions)) {
		write_log('Cannot find toVersion ' . $toVersion . ' in listed versions');
		return;
	}

	$fromIndex = $versions[$fromVersion];
	$toIndex = $versions[$toVersion];

	if ($fromIndex >= $toIndex) {
		write_log('From version already up to date with to-version, skipping further checks');
		return;
	}
	
	if ($toIndex >= 4) {
		write_log('Upgrading API URL for REST endpoints');
		// from version 1.0.3 onwards REST endpoint is used instead of old style url form encoded endpoint
		NPSettings::updateApiUrl('https://api.nuapay.com/v1/emandates');
	}

	NPSettings::updatePluginVersion($toVersion);
}
