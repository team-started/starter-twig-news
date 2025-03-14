<?php

use GeorgRinger\News\Controller\NewsController;

defined('TYPO3') || die();

$GLOBALS['TYPO3_CONF_VARS']['SYS']['Objects'][NewsController::class] = [
    'className' => StarterTeam\StarterTwigNews\Controller\NewsController::class,
];
