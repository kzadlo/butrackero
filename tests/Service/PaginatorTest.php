<?php

namespace App\Tests\Service;

use App\Service\Paginator;
use App\Service\PaginatorInterface;
use PHPUnit\Framework\TestCase;

class PaginatorTest extends TestCase
{
    /** @var PaginatorInterface $paginator */
    private $paginator;

    public function setUp()
    {
        $page = 1;
        $limit = 25;

        $this->paginator = new Paginator($page, $limit);
    }

    public function testClassImplementsPaginatorInterface()
    {
        $this->assertInstanceOf(PaginatorInterface::class, $this->paginator);
    }

    public function testPageAndLimitAreSetInConstructor()
    {
        $this->assertSame(1, $this->paginator->getPage());
        $this->assertSame(25, $this->paginator->getLimit());
    }

    public function testCanGetPage()
    {
        $this->paginator->setPage(5);

        $this->assertSame(5, $this->paginator->getPage());
    }

    public function testCanGetLimit()
    {
        $this->paginator->setLimit(15);

        $this->assertSame(15, $this->paginator->getLimit());
    }

    public function testCanGetOffset()
    {
        $offsetWhenFirstPage = 0;
        $this->assertSame($offsetWhenFirstPage, $this->paginator->getOffset());

        $offsetWhenSecondPage = 25;
        $this->paginator->setPage(2);
        $this->assertSame($offsetWhenSecondPage, $this->paginator->getOffset());
    }

    public function testCalculateLastPage()
    {
        $allItems = 50;
        $this->assertSame(2, $this->paginator->calculateLastPage($allItems));

        $allItems = 51;
        $this->assertSame(3, $this->paginator->calculateLastPage($allItems));
    }

    public function testIsFirstPage()
    {
        $this->assertTrue($this->paginator->isFirstPage());

        $this->paginator->setPage(2);
        $this->assertFalse($this->paginator->isFirstPage());
    }

    public function testIsLastPage()
    {
        $this->assertFalse($this->paginator->isLastPage(2));

        $this->paginator->setPage(2);
        $this->assertTrue($this->paginator->isLastPage(2));
    }

    public function testCanGetNextPage()
    {
        $this->paginator->setPage(10);
        $this->assertSame(11, $this->paginator->nextPage());
    }

    public function testCanGetPreviousPage()
    {
        $this->paginator->setPage(10);
        $this->assertSame(9, $this->paginator->previousPage());
    }
}