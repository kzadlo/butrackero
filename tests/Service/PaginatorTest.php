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
        $this->assertEquals($this->paginator->getPage(), 1);
        $this->assertEquals($this->paginator->getLimit(), 25);
    }

    public function testCanGetPage()
    {
        $this->paginator->setPage(5);

        $this->assertEquals($this->paginator->getPage(), 5);
    }

    public function testCanGetLimit()
    {
        $this->paginator->setLimit(15);

        $this->assertEquals($this->paginator->getLimit(), 15);
    }

    public function testCanGetOffset()
    {
        $offsetWhenFirstPage = 0;
        $this->assertEquals($this->paginator->getOffset(), $offsetWhenFirstPage);

        $offsetWhenSecondPage = 25;
        $this->paginator->setPage(2);
        $this->assertEquals($this->paginator->getOffset(), $offsetWhenSecondPage);
    }

    public function testCalculateLastPage()
    {
        $allItems = 50;
        $this->assertEquals($this->paginator->calculateLastPage($allItems), 2);

        $allItems = 51;
        $this->assertEquals($this->paginator->calculateLastPage($allItems), 3);
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
        $this->assertEquals($this->paginator->nextPage(), 11);
    }

    public function testCanGetPreviousPage()
    {
        $this->paginator->setPage(10);
        $this->assertEquals($this->paginator->previousPage(), 9);
    }
}