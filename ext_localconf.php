<?php
if (!defined('TYPO3_MODE')) {
	die ('Access denied.');
}

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
	'Visol.' . $_EXTKEY,
	'Quicknavigation',
	array(
		'QuickNavigationItem' => 'render, getData',
		
	),
	// non-cacheable actions
	array(
		'QuickNavigationItem' => '',
		
	)
);



?>