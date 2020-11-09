<?php

namespace App\Tests\Paginator;

use App\Paginator\Paginator;
use PHPUnit\Framework\TestCase;

class PaginatorTest extends TestCase
{
    public function testPaginate(): void
    {
        $data       = ['orange', 'banana', 'apple'];
        $collection = (new Paginator())->paginate('app_test', $data, 1, 60);

        self::assertSame(1, $collection->getPage());
        self::assertSame(3, $collection->getTotal());
        self::assertSame(60, $collection->getLimit());
        self::assertSame('app_test', $collection->getRoute());
    }
}
