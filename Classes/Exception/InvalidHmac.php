<?php
declare(strict_types=1);

namespace Netlogix\EsiRendering\Exception;

use Neos\Flow\Security\Exception\AccessDeniedException;

class InvalidHmac extends AccessDeniedException
{

    protected $statusCode = 403;

}
