<?php

declare(strict_types=1);

namespace StarterTeam\StarterTwigNews\Controller;

use StarterTeam\StarterTwigNews\View\NewsDateMenuTwigView;
use StarterTeam\StarterTwigNews\View\NewsDetailTwigView;
use StarterTeam\StarterTwigNews\View\NewsListTwigView;

class NewsController extends \GeorgRinger\News\Controller\NewsController
{
    /**
     * Setting the default view object class to ListView will enable the Twig templates
     */
    protected $defaultViewObjectName = NewsListTwigView::class;

    public function initializeDetailAction(): void
    {
        $this->defaultViewObjectName = NewsDetailTwigView::class;
    }

    public function initializeDateMenuAction(): void
    {
        $this->defaultViewObjectName = NewsDateMenuTwigView::class;
    }
}
