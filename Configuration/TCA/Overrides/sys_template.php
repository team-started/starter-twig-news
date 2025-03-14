<?php

use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;

defined('TYPO3') || die();

ExtensionManagementUtility::addStaticFile(
    'starter_twig_news',
    'Configuration/TypoScript',
    'Starter Twig News'
);
