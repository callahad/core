<?php

//detect if we can switch on naming guidelines. We won't do it on conflicts.
//it's a bit spaghetti, but hey.
$state = OCP\Config::getSystemValue('ldapIgnoreNamingRules', 'unset');
if($state === 'unset') {
	OCP\Config::setSystemValue('ldapIgnoreNamingRules', false);
}

$installedVersion = OCP\Config::getAppValue('files_sharing', 'installed_version');
$enableRawMode = false;
if (version_compare($installedVersion, '0.4.1', '<')) {
	$enableRawMode = true;
}

$configPrefixes = OCA\user_ldap\lib\Helper::getServerConfigurationPrefixes(true);
$ldap = new OCA\user_ldap\lib\LDAP();
foreach($configPrefixes as $config) {
	$connection = new OCA\user_ldap\lib\Connection($ldap, $config);
	$value = \OCP\Config::getAppValue('user_ldap',
									  $config.'ldap_uuid_attribute', 'auto');
	\OCP\Config::setAppValue('user_ldap',
							 $config.'ldap_uuid_user_attribute', $value);
	\OCP\Config::setAppValue('user_ldap',
							 $config.'ldap_uuid_group_attribute', $value);

	$value = \OCP\Config::getAppValue('user_ldap',
									  $config.'ldap_expert_uuid_attr', 'auto');
	\OCP\Config::setAppValue('user_ldap',
							 $config.'ldap_expert_uuid_user_attr', $value);
	\OCP\Config::setAppValue('user_ldap',
							 $config.'ldap_expert_uuid_group_attr', $value);

	if($enableRawMode) {
		\OCP\Config::setAppValue('user_ldap', $config.'ldap_user_filter_mode', 1);
		\OCP\Config::setAppValue('user_ldap', $config.'ldap_login_filter_mode', 1);
		\OCP\Config::setAppValue('user_ldap', $config.'ldap_group_filter_mode', 1);
	}
}
