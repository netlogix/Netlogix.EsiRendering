<?php
declare(strict_types=1);

namespace Netlogix\EsiRendering\Service;

use Exception;
use Neos\ContentRepository\Domain\Model\NodeInterface;
use Neos\Flow\Annotations as Flow;
use Neos\Flow\Security\Cryptography\HashService;
use const PHP_EOL;

/**
 * @Flow\Scope("singleton")
 */
class HmacService
{

    /**
     * @var HashService
     * @Flow\Inject
     */
    protected $hashService;

    public function generateHmac(string $fusionPath, NodeInterface $node): string
    {
        return $this->hashService->generateHmac(self::prepareHmacString($fusionPath, $node));
    }

    public function validateHmac(string $fusionPath, NodeInterface $node, string $hmac): bool
    {
        try {
            return $this->hashService->validateHmac(self::prepareHmacString($fusionPath, $node), $hmac);
        } catch (Exception $e) {
        }

        return false;
    }

    private static function prepareHmacString(string $fusionPath, NodeInterface $node): string
    {
        return join(PHP_EOL, [$fusionPath, $node->getContextPath()]);
    }

}
