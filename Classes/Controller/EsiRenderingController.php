<?php
declare(strict_types=1);

namespace Netlogix\EsiRendering\Controller;

use Neos\ContentRepository\Domain\Model\NodeInterface;
use Neos\Flow\Mvc\Controller\ActionController;
use Neos\Neos\View\FusionView;
use Neos\Flow\Annotations as Flow;
use Netlogix\EsiRendering\Exception\InvalidHmac;
use Netlogix\EsiRendering\Service\HmacService;

class EsiRenderingController extends ActionController
{

    /**
     * @var HmacService
     * @Flow\Inject
     */
    protected $hmacService;

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
     * @param string $hmac
     */
    public function indexAction(string $fusionPath, NodeInterface $node, string $hmac)
    {
        if (!$this->hmacService->validateHmac($fusionPath, $node, $hmac)) {
            throw new InvalidHmac('The given hmac was incorrect', 1615143488);
        }

        $this->view->setFusionPath($fusionPath);
        $this->view->assign('value', $node);
    }
}
