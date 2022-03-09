<?php

declare(strict_types=1);

namespace Pidia\Apps\Demo\Annotation\Route;

use Symfony\Component\Routing\Annotation\Route;

#[\Attribute(\Attribute::IS_REPEATABLE | \Attribute::TARGET_CLASS | \Attribute::TARGET_METHOD)]
class GetPost extends Route
{
    public function getMethods(): array
    {
        return [
            HttpMethod::GET->name,
            HttpMethod::POST->name,
        ];
    }
}
