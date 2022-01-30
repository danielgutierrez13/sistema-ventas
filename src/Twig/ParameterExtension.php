<?php

declare(strict_types=1);


namespace Pidia\Apps\Demo\Twig;


use Pidia\Apps\Demo\Utils\Parameter;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

final class ParameterExtension extends AbstractExtension
{
    public function getFilters(): array
    {
        return [
            new TwigFilter('parameterAlmacenAlias', [$this, 'parameterAlmacenAliasFilter']),
            new TwigFilter('parameterPergaminoAlmacenAlias', [$this, 'parameterPergaminoAlmacenAliasFilter']),
        ];
    }

    public function parameterAlmacenAliasFilter(int $almacenId): string
    {
        return (new Parameter())->almacenAlias($almacenId);
    }

    public function parameterPergaminoAlmacenAliasFilter(int $almacenId): string
    {
        return (new Parameter())->almacenPergaminoAlias($almacenId);
    }
}
