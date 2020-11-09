<?php

namespace App\Paginator;

use Hateoas\Representation\PaginatedRepresentation;

interface PaginatorInterface
{
    public function paginate(string $route, array $data, int $page, int $limit): PaginatedRepresentation;
}
