<?php

namespace App\Paginator;

use Hateoas\Representation\CollectionRepresentation;
use Hateoas\Representation\PaginatedRepresentation;

class Paginator implements PaginatorInterface
{
    public function paginate(string $route, array $data, int $page, int $limit): PaginatedRepresentation
    {
        $totalItems    = count($data);
        $offset        = ($page - 1) * $limit;
        $numberOfPages = (int) ceil($totalItems / $limit);

        $collection = new CollectionRepresentation(
            array_slice($data, $offset, $limit),
        );

        return new PaginatedRepresentation(
            $collection,
            $route,
            [],
            $page,
            $limit,
            $numberOfPages,
            null,
            null,
            true,
            $totalItems
        );
    }
}
