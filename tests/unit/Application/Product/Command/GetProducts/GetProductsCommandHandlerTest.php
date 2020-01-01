<?php

namespace App\Tests\unit\Application\Product\Command\GetProducts;

use App\Application\Product\Command\GetProducts\GetProductsCommand;
use App\Application\Product\Command\GetProducts\GetProductsCommandHandler;
use App\Application\Product\Service\GetProductsCommandToProductFiltersConverter;
use App\Application\Product\Service\ProductToArrayConverter;
use App\Domain\Product\Model\Product;
use App\Domain\Product\Repository\ProductFilters;
use App\Domain\Product\Repository\ProductRepositoryInterface;
use PHPUnit\Framework\TestCase;

class GetProductsCommandHandlerTest extends TestCase
{
    public function testHandle()
    {
        $command = $this->prophesize(GetProductsCommand::class);
        $filters = $this->prophesize(ProductFilters::class);
        $commandToProductFiltersConverter = $this->prophesize(GetProductsCommandToProductFiltersConverter::class);
        $commandToProductFiltersConverter->convert($command)->willReturn($filters->reveal());
        $product = $this->prophesize(Product::class);
        $productRepository = $this->prophesize(ProductRepositoryInterface::class);
        $productRepository->findByFilters($filters->reveal())->willReturn([
            $product
        ]);
        $productToArrayConverter = $this->prophesize(ProductToArrayConverter::class);
        $productToArrayConverter->toArray($product)->willReturn([
            'uuid' => 'uuid',
            'name' => 'name',
            'description' => 'description',
            'price' => floatval(100),
            'priceWithVat' => floatval(121),
            'taxonomyName' => 'taxonomy'
        ]);

        $handler = new GetProductsCommandHandler(
            $productRepository->reveal(),
            $productToArrayConverter->reveal(),
            $commandToProductFiltersConverter->reveal()
        );
        $products = $handler->handle($command->reveal());

        $this->assertIsArray($products);
        $this->assertCount(1, $products);
    }
}
