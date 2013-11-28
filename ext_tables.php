<?php
if (!defined('TYPO3_MODE')) {
	die ('Access denied.');
}

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerPlugin(
	$_EXTKEY,
	'Quicknavigation',
	'QuickNavigation'
);

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addStaticFile($_EXTKEY, 'Configuration/TypoScript', 'Structured Quick Navigation');

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addLLrefForTCAdescr('tx_quicknav_domain_model_quicknavigationitem', 'EXT:quicknav/Resources/Private/Language/locallang_csh_tx_quicknav_domain_model_quicknavigationitem.xlf');
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::allowTableOnStandardPages('tx_quicknav_domain_model_quicknavigationitem');
$TCA['tx_quicknav_domain_model_quicknavigationitem'] = array(
	'ctrl' => array(
		'title'	=> 'LLL:EXT:quicknav/Resources/Private/Language/locallang_db.xlf:tx_quicknav_domain_model_quicknavigationitem',
		'label' => 'name',
		'tstamp' => 'tstamp',
		'crdate' => 'crdate',
		'cruser_id' => 'cruser_id',
		'dividers2tabs' => TRUE,
		'sortby' => 'sorting',
		'versioningWS' => 2,
		'versioning_followPages' => TRUE,

		'origUid' => 't3_origuid',
		'languageField' => 'sys_language_uid',
		'transOrigPointerField' => 'l10n_parent',
		'transOrigDiffSourceField' => 'l10n_diffsource',

		'delete' => 'deleted',
		'enablecolumns' => array(
			'disabled' => 'hidden',
			'starttime' => 'starttime',
			'endtime' => 'endtime',
		),
		'searchFields' => 'name,shortcut,category,',
		'dynamicConfigFile' => \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath($_EXTKEY) . 'Configuration/TCA/QuickNavigationItem.php',
		'iconfile' => \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extRelPath($_EXTKEY) . 'Resources/Public/Icons/tx_quicknav_domain_model_quicknavigationitem.gif'
	),
);

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::makeCategorizable(
	$_EXTKEY,
	'tx_quicknav_domain_model_quicknavigationitem',
	$fieldName = 'category',
	$options = array(
		'fieldConfiguration' => array(
			'maxitems' => 1,
			'foreign_table_where' => ' AND sys_category.pid=###CURRENT_PID###'
			//'foreign_table_where' => 'sys_category.pid=###CURRENT_PID### ORDER BY sys_category.sorting ASC'
		),
	)
);
\TYPO3\CMS\Core\Utility\GeneralUtility::loadTCA('tx_quicknav_domain_model_quicknavigationitem');

// TODO doesn't work yet because of a bug
$GLOBALS['TCA']['tx_quicknav_domain_model_quicknavigationitem']['columns']['category']['label'] = 'LLL:EXT:lang/locallang_tca.xlf:sys_category';

?>