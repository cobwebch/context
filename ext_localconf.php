<?php
if (!defined ('TYPO3_MODE')) {
 	die ('Access denied.');
}

// Register context loader with tslib_fe hook
$TYPO3_CONF_VARS['SC_OPTIONS']['tslib/class.tslib_fe.php']['configArrayPostProc'][$_EXTKEY] = 'Cobweb\\Context\\ContextLoader->loadContext';
