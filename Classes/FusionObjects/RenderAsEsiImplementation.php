<?php

namespace Netlogix\EsiRendering\FusionObjects;

use Neos\ContentRepository\Domain\Model\NodeInterface;
use Neos\Flow\Mvc\ActionRequest;
use Neos\Flow\Security\Context;
use Neos\Fusion\FusionObjects\AbstractFusionObject;
use Neos\Flow\Annotations as Flow;
use Netlogix\EsiRendering\Service\HmacService;

/**
 * Render an ESI tag instead of the actual element
 */
class RenderAsEsiImplementation extends AbstractFusionObject
{

    /**
     * @var HmacService
     * @Flow\Inject
     */
    protected $hmacService;

    /**
     * @var Context
     * @Flow\Inject
     */
    protected $securityContext;

    /**
     * @return mixed
     */
    public function evaluate()
    {
        $request = $this->getRuntime()->getControllerContext()->getRequest();
        if (!$request instanceof ActionRequest) {
            return $this->fusionValue('content');
        }

        $contentPath = 'content';
        /** @var NodeInterface $contextNode */
        $contextNode = $this->fusionValue('node');

        if (!$this->shouldRenderAsEsi()) {
            $this->runtime->pushContextArray(array_merge($this->runtime->getCurrentContext(), [
                'node' => $contextNode
            ]));
            $result = $this->fusionValue($contentPath);
            $this->runtime->popContext();

            return $result;
        }

        $fusionPath = $this->path . '/' . $contentPath;

        $context = array_merge($this->runtime->getCurrentContext(), [
            'esiIdentifier' => $this->fusionValue('cacheIdentifier'),
            'contextNode' => $contextNode,
            'arguments' => [
                'esiIdentifier' => $this->fusionValue('cacheIdentifier'),
                'fusionPath' => $fusionPath,
                'node' => $contextNode,
                'hmac' => $this->hmacService->generateHmac($fusionPath, $contextNode),
            ]
        ]);
        $this->runtime->pushContextArray($context);
        $result = $this->runtime->evaluate('esiRendering');
        $this->runtime->popContext();

        return $result;
    }

    private function shouldRenderAsEsi(): bool
    {
        /** @var NodeInterface $contextNode */
        $contextNode = $this->fusionValue('node');

        if ($contextNode instanceof NodeInterface && !$contextNode->getContext()->getWorkspace()->isPublicWorkspace()) {
            // $contextNode is in a personal or internal Workspace, don't render as ESI
            return false;
        }

        if ($this->securityContext->canBeInitialized() && $this->securityContext->getAccount() !== null) {
            // Account found in context and might be viewing data protected by policy, don't render as ESI
            return false;
        }

        return true;
    }

}
