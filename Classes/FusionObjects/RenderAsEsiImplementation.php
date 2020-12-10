<?php

namespace Netlogix\EsiRendering\FusionObjects;

use Neos\ContentRepository\Domain\Model\NodeInterface;
use Neos\Flow\Mvc\ActionRequest;
use Neos\Fusion\FusionObjects\AbstractFusionObject;

/**
 * Render an ESI tag instead of the actual element
 */
class RenderAsEsiImplementation extends AbstractFusionObject
{

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

        if (!$contextNode->getContext()->getWorkspace()->isPublicWorkspace()) {
            $this->runtime->pushContextArray(array_merge($this->runtime->getCurrentContext(), [
                'node' => $contextNode
            ]));
            $result = $this->fusionValue($contentPath);
            $this->runtime->popContext();
            return $result;
        }

        $context = array_merge($this->runtime->getCurrentContext(), [
            'esiIdentifier' => $this->fusionValue('cacheIdentifier'),
            'contextNode' => $contextNode,
            'arguments' => [
                'fusionPath' => $this->path . '/' . $contentPath,
                'node' => $contextNode
            ]
        ]);
        $this->runtime->pushContextArray($context);
        $result = $this->runtime->evaluate('esiRendering');
        $this->runtime->popContext();

        return $result;
    }

}
