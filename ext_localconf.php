<?php

defined('TYPO3') || die();

$GLOBALS['TYPO3_CONF_VARS']['SYS']['Objects'][GeorgRinger\News\Controller\NewsController::class] = [
    'className' => StarterTeam\StarterTwigNews\Controller\NewsController::class,
];
