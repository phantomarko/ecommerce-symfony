<?php

namespace App\Tests\unit\Domain\Product\Model;

use App\Domain\Product\Model\Product;
use PHPUnit\Framework\TestCase;

class ProductTest extends TestCase
{
    private $uuid;
    private $name;
    private $description;
    private $price;
    private $priceWithVat;
    private $product;

    public function setUp()
    {
        $this->uuid = 'uuid';
        $this->name = 'name';
        $this->description = 'description';
        $this->price = 2.88;
        $this->priceWithVat = 288.88;

        $this->product = new Product(
            $this->uuid,
            $this->name,
            $this->description,
            $this->price,
            $this->priceWithVat
        );
    }

    public function testUuid()
    {
        $this->assertSame($this->product->uuid(), $this->uuid);
    }

    public function testName()
    {
        $this->assertSame($this->product->name(), $this->name);
    }

    public function testDescription()
    {
        $this->assertSame($this->product->description(), $this->description);
    }

    public function testPrice()
    {
        $this->assertSame($this->product->price(), $this->price);
    }

    public function testPriceWithVat()
    {
        $this->assertSame($this->product->priceWithVat(), $this->priceWithVat);
    }
}
