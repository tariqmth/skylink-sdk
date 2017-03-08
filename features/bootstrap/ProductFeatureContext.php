<?php

use RetailExpress\SkyLink\Sdk\Catalogue\Attributes\AttributeCode;
use RetailExpress\SkyLink\Sdk\Catalogue\Eta\EtaQty;
use RetailExpress\SkyLink\Sdk\Catalogue\Products\ProductId;
use ValueObjects\StringLiteral\StringLiteral;

trait ProductFeatureContext
{
    private $attributeRepository;

    private $brandAttribute;

    private $etaRepository;

    private $productRepository;

    private $productIds = [];

    private $product;

    /**
     * @When I get all brands
     */
    public function iGetAllBrands()
    {
        $attributeCode = AttributeCode::fromNative('brand');

        $this->brandAttribute = $this->attributeRepository->find($attributeCode, $this->salesChannelId);
    }

    /**
     * @Then I should see there are :arg1 brands
     */
    public function iShouldSeeThereAreBrands($count)
    {
        $brandsCount = count($this->brandAttribute->getOptions());

        if ((int) $count !== $brandsCount) {
            throw new Exception("There were {$brandsCount} brands.");
        }
    }

    /**
     * @When I find the ETA for :arg1 products with id :arg2
     */
    public function iFindTheEtaForProductsWithId($qty, $productId)
    {
        $this->eta = $this->etaRepository->find(
            new ProductId($productId),
            new EtaQty($qty),
            $this->salesChannelId
        );
    }

    /**
     * @Then I should see the ETA is in the future
     */
    public function iShouldSeeTheEtaIsInTheFuture()
    {
        if (null === $this->eta) {
            throw new Exception("Expected an ETA to exist but there was none.");
        }
    }

    /**
     * @Then I should see there is no ETA
     */
    public function iShouldSeeThereIsNoEta()
    {
        if (null !== $this->eta) {
            throw new Exception("Expected no ETA to exist but there was one.");
        }
    }

    /**
     * @When I get all product ids
     */
    public function iGetAllProductIds()
    {
        $this->productIds = $this->productRepository->allIds($this->salesChannelId);
    }

    /**
     * @Then I can see there are :arg1 product ids
     */
    public function iCanSeeThereAreProductIds($count)
    {
        $productIdsCount = count($this->productIds);

        if ((int) $count !== $productIdsCount) {
            throw new Exception("There were {$productIdsCount} product ids.");
        }
    }

    /**
     * @When I find the product with id :arg1
     */
    public function iFindTheProductWithId($productId)
    {
        $this->product = $this->productRepository->find(
            new ProductId($productId),
            $this->salesChannelId
        );

        if (null === $this->product) {
            throw new Exception("Failed to retrieve product with ID \"{$productId}\".");
        }
    }

    /**
     * @Then I should see that its sku is :arg1
     */
    public function iShouldSeeThatItsSkuIs($expectedSku)
    {
        $actualSku = $this->product->getSku();

        if (!$actualSku->sameValueAs(new StringLiteral($expectedSku))) {
            throw new Exception("SKU \"{$actualSku}\" was found.");
        }
    }
}
