<?php

namespace App\Infrastructure\Product\Service;

use App\Application\Product\Command\GetProducts\GetProductsCommand;
use App\Infrastructure\Common\Exception\InvalidSymfonyRequestException;
use Symfony\Component\HttpFoundation\Request;

class SymfonyRequestToGetProductsCommand
{
    public function convert(Request $request): GetProductsCommand
    {
        $requestDecoded = $request->query->all();
        if (!is_array($requestDecoded) || !$this->isValidRequest($requestDecoded)) {
            throw new InvalidSymfonyRequestException();
        } else {
            return new GetProductsCommand(
                !empty($requestDecoded['taxonomyUuid'])
                    ? $requestDecoded['taxonomyUuid']
                    : null,
                !empty($requestDecoded['minimumPrice'])
                    ? $requestDecoded['minimumPrice']
                    : null,
                !empty($requestDecoded['maximumPrice'])
                    ? $requestDecoded['maximumPrice']
                    : null,
                !empty($requestDecoded['text'])
                    ? $requestDecoded['text']
                    : null
            );
        }
    }

    private function isValidRequest(array $request): bool
    {
        return (
            (
                empty($request['minimumPrice'])
                || (!empty($request['minimumPrice']) && is_numeric($request['minimumPrice']))
            )
            &&
            (
                empty($request['maximumPrice'])
                || (!empty($request['maximumPrice']) && is_numeric($request['maximumPrice']))
            )
        );
    }
}