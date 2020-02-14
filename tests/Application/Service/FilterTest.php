<?php

namespace App\Tests\Application\Service;

use App\Application\Service\Filter;
use PHPUnit\Framework\TestCase;

final class FilterTest extends TestCase
{
    /** @var Filter $filter */
    private $filter;

    public function setUp(): void
    {
        $this->filter = new Filter();
    }

    /** @dataProvider provider */
    public function testCanPrepareFilters(array $filters)
    {
        $this->filter->prepare($filters);

        $this->assertCount(count($filters), $this->filter->getAll());

        $newFilters = [
            'one' => '10'
        ];

        $this->filter->prepare($newFilters);

        $this->assertCount(count($newFilters), $this->filter->getAll());
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

    /** @dataProvider provider */
    public function testCanRemoveFilter(array $filters)
    {
        $this->filter->prepare($filters);

        $this->assertSame(reset($filters), $this->filter->get(key($filters)));

        $this->filter->remove(key($filters));

        $this->assertNull($this->filter->get(key($filters)));
    }

    /** @dataProvider provider */
    public function testCanGetFilters(array $filters)
    {
        $this->filter->prepare($filters);

        $this->assertCount(count($filters), $this->filter->getAll());
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

    public function provider()
    {
        return [
            'one element array' => [
                [
                    'one' => '10'
                ]
            ],
            'two elements array' => [
                [
                    'one' => '10',
                    'two' => '20'
                ]
            ],
            'three elements array' => [
                [
                    'one' => '10',
                    'two' => '20',
                    'three' => '30'
                ]
            ]
        ];
    }
}
