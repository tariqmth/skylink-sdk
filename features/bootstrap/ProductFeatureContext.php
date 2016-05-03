<?php

use Behat\Behat\Context\Context;
use Behat\Behat\Tester\Exception\PendingException;
use Behat\Behat\Context\SnippetAcceptingContext;
use Behat\Gherkin\Node\PyStringNode;
use Behat\Gherkin\Node\TableNode;

use RetailExpress\SkyLink\Catalogue\Attributes\AttributeCode;
use RetailExpress\SkyLink\Catalogue\Products\ProductId;
use ValueObjects\StringLiteral\StringLiteral;

trait ProductFeatureContext
{
    private $attributeRepository;

    private $brandAttribute;

    private $productRepository;

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
