<?php

namespace App\Tests\Application\Service;

use App\Application\Service\Filter;
use PHPUnit\Framework\TestCase;

class FilterTest extends TestCase
{
    /** @var Filter $filter */
    private $filter;

    public function setUp()
    {
        $this->filter = new Filter();
    }

    public function testCanPrepareFilters()
    {
        $filters = [
            'offset' => '20',
            'limit' => '10',
        ];

        $this->filter->prepare($filters);

        $this->assertCount(2, $this->filter->getAll());

        $filters = [
            'limit' => '20'
        ];

        $this->filter->prepare($filters);

        $this->assertCount(1, $this->filter->getAll());
    }

    public function testCanGetFilter()
    {
        $this->filter->add('offset', '20');

        $this->assertSame('20', $this->filter->get('offset'));
    }

    public function testCanAddFilter()
    {
        $this->filter->add('offset', '20');

        $this->assertCount(1, $this->filter->getAll());
    }

    public function testCanRemoveFilter()
    {
        $filters = [
            'offset' => '20',
            'limit' => '10',
        ];

        $this->filter->prepare($filters);

        $this->assertSame('10', $this->filter->get('limit'));

        $this->filter->remove('limit');

        $this->assertNull($this->filter->get('limit'));
    }

    public function testCanGetFilters()
    {
        $filters = [
            'offset' => '20',
            'limit' => '10',
        ];

        $this->filter->prepare($filters);

        $this->assertCount(2, $this->filter->getAll());
    }

    public function testHasFilter()
    {
        $this->assertFalse($this->filter->hasFilter('limit'));

        $this->filter->add('limit', '10');

        $this->assertTrue($this->filter->hasFilter('limit'));
    }

    public function testHasFilters()
    {
        $this->assertFalse($this->filter->hasFilters());

        $this->filter->add('limit', '10');

        $this->assertTrue($this->filter->hasFilters());
    }

    public function testCanClearFilters()
    {
        $this->filter->add('limit', '10');

        $this->assertTrue($this->filter->hasFilters());

        $this->filter->clear();

        $this->assertFalse($this->filter->hasFilters());
    }
}