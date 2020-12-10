<?php

namespace Netlogix\EsiRendering\Controller;

/*
 * This file is part of the Netlogix.EsiRendering package.
 */

use Neos\ContentRepository\Domain\Model\NodeInterface;
use Neos\Flow\Mvc\Controller\ActionController;
use Neos\Neos\View\FusionView;

class EsiRenderingController extends ActionController
{

    /**
     * @var FusionView
     */
    protected $view;

    /**
     * @var array
     */
    protected $viewFormatToObjectNameMap = [
        'html' => FusionView::class
    ];

    /**
     * @param string $fusionPath
     * @param NodeInterface $node
     */
    public function indexAction(string $fusionPath, NodeInterface $node)
    {
        $this->view->setFusionPath($fusionPath);
        $this->view->assign('value', $node);
    }
}
