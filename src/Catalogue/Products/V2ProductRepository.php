<?php

namespace RetailExpress\SkyLink\Sdk\Catalogue\Products;

use RetailExpress\SkyLink\Sdk\Apis\V2\Api as V2Api;
use RetailExpress\SkyLink\Sdk\ValueObjects\SalesChannelId;
use Sabre\Xml\Reader as XmlReader;

class V2ProductRepository implements ProductRepository
{
    private $matrixPolicyMapper;

    private $v2ProductDeserializer;

    private $api;

    public function __construct(
        MatrixPolicyMapper $matrixPolicyMapper,
        V2ProductDeserializer $v2ProductDeserializer,
        V2Api $api
    ) {
        $this->matrixPolicyMapper = $matrixPolicyMapper;
        $this->v2ProductDeserializer = $v2ProductDeserializer;
        $this->api = $api;
    }

    public function allIds(
        SalesChannelId $salesChannelId
    ) {
        $rawResponse = $this->api->call('GetProductIdsByChannel', [
            'ChannelId' => $salesChannelId->toNative(),
        ]);

        $xmlService = $this->api->getXmlService();
        $xmlService->elementMap = [
            '{}Product' => ProductId::class,
        ];
        $parsedResponse = $xmlService->parse($rawResponse);
        $flattenedParsedResponse = array_flatten($parsedResponse);

        $productIds = array_filter($flattenedParsedResponse, function ($payload) {
            return $payload instanceof ProductId;
        });

        return array_values($productIds);
    }

    public function find(
        ProductId $productId,
        SalesChannelId $salesChannelId
    ) {
        $rawResponse = $this->api->call('GetProductsByChannel', [
            'ProductIds' => [$productId->toNative()],
            'ChannelId' => $salesChannelId->toNative(),
        ]);

        $xmlService = $this->api->getXmlService();
        $xmlService->elementMap = [
            '{}Product' => function (XmlReader $xmlReader) {
                return $this->v2ProductDeserializer->xmlDeserialize($xmlReader);
            },
        ];
        $parsedResponse = $xmlService->parse($rawResponse);
        $flattenedParsedResponse = array_flatten($parsedResponse);

        $products = array_values(array_filter($flattenedParsedResponse, function ($payload) {
            return $payload instanceof SimpleProduct;
        }));

        // If there is more than one product, we're dealing with a product matrix
        if (count($products) > 1) {
            return $this->buildProductMatrix($products);
        } elseif (count($products) === 1) {
            return current($products);
        }
    }

    public function findSpecific(
        ProductId $productId,
        SalesChannelId $salesChannelId
    ) {
        $product = $this->find($productId, $salesChannelId);

        if (null === $product) {
            return null;
        }

        if (!$product instanceof CompositeProduct) {
            return $product;
        }

        return array_first($product->getProducts(), function ($key, Product $associatedProduct) use ($productId) {
            return $associatedProduct->getId()->sameValueAs($productId);
        });
    }

    private function buildProductMatrix(array $products)
    {
        $firstProduct = current($products);

        $matrixPolicy = $this->matrixPolicyMapper->getPolicyForProductType($firstProduct->getProductType());

        $matrixProduct = new Matrix($matrixPolicy, $products);

        foreach ($products as $product) {
            $product->setMatrixProduct($matrixProduct);
        }

        return $matrixProduct;
    }
}
