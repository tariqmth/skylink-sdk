<?php

use RetailExpress\SkyLink\Sdk\Catalogue\Attributes\AttributeCode;
use RetailExpress\SkyLink\Sdk\Catalogue\Eta\EtaQty;
use RetailExpress\SkyLink\Sdk\Catalogue\Products\ProductId;
use RetailExpress\SkyLink\Sdk\Catalogue\Products\Matrix;
use RetailExpress\SkyLink\Sdk\Catalogue\Products\SimpleProduct;
use RetailExpress\SkyLink\Sdk\Exceptions\Catalogue\Products\ProductHasNoQtyForOutletException;
use RetailExpress\SkyLink\Sdk\Outlets\OutletId;
use ValueObjects\Number\Integer;
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

        $this->brandAttribute = $this->attributeRepository->find($attributeCode);
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
    }

     /**
     * @Then I should see the product does not exist
     */
    public function iShouldSeeTheProductDoesNotExist()
    {
        if (null !== $this->product) {
            throw new Exception("Product with ID \"{$productId}\" exists.");
        }
    }

    /**
     * @Then I should see the product exists
     */
    public function iShouldSeeTheProductExists()
    {
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

    /**
     * @Then I should see that its manufacturer sku is :arg1
     */
    public function iShouldSeeThatItsManufacturerSkuIs($expectedManufacturerSku)
    {
        $actualManufacturerSku = $this->product->getManufacturerSku();

        if (!$actualManufacturerSku->sameValueAs(new StringLiteral($expectedManufacturerSku))) {
            throw new Exception("Manufacturer SKU \"{$actualManufacturerSku}\" was found.");
        };
    }

    /**
     * @Then I should see that we have :arg1 available and :arg2 on order
     */
    public function iShouldSeeThatWeHaveAvailableAndOnOrder(
        $expectedQtyAvailable,
        $expectedQtyOnOrder
    ) {
        $actualQtyAvailable = $this->product->getInventoryItem()->getQtyAvailable();
        $actualQtyOnOrder = $this->product->getInventoryItem()->getQtyOnOrder();

        if (!$actualQtyAvailable->sameValueAs(new Integer($expectedQtyAvailable))) {
            throw new Exception("There was/were \"{$actualQtyAvailable}\" available.");
        }

        if (!$actualQtyOnOrder->sameValueAs(new Integer($expectedQtyOnOrder))) {
            throw new Exception("There was/were \"{$actualQtyOnOrder}\" on order.");
        }
    }

    /**
     * @Then I should see that outlet :arg1 has :arg2 available
     */
    public function iShouldSeeThatOutletHasAvailable($expectedOutletId, $expectedQty)
    {
        $actualOutletQty = $this->product->getInventoryItem()->getOutletQty(
            new OutletId($expectedOutletId)
        );

        $actualQty = $actualOutletQty->getQty();

        if (!$actualQty->sameValueAs(new Integer($expectedQty))) {
            throw new Exception("Outlet \"{$expectedOutletId}\" has \"{$actualQty}\" available.");
        }
    }

    /**
     * @Then I should see that it has no stock for outlet :arg1
     */
    public function iShouldSeeThatItHasNoStockForOutlet($expectedOutletId)
    {
        try {
            $this->product->getInventoryItem()->getOutletQty(new OutletId($expectedOutletId));
        } catch (ProductHasNoQtyForOutletException $e) {
            return;
        }

        throw new Exception("It appears there was stock for outlet \"{$expectedOutletId}\".");
    }

    /**
     * @Then it is a matrix that contains :arg1 products
     */
    public function itIsAMatrixThatContainsProducts($count)
    {
        if (!$this->product instanceof Matrix) {
            if ($this->product instanceof SimpleProduct) {
                throw new Exception("Product with ID \"{$this->product->getId()}\" is a simple product, not a matrix.");
            }

            throw new Exception(sprintf(
                'Product with ID "%s" is an instance of "%s"',
                $this->product->getId(),
                get_class($this->product)
            ));
        }

        $productsCount = count($this->product->getProducts());

        if ($productsCount !== (int) $count) {
            throw new Exception("Matrix contains \"{$productsCount}\" products.");
        }
    }
}
