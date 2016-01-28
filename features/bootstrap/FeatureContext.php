<?php

use Behat\Behat\Context\Context;
use Behat\Behat\Tester\Exception\PendingException;
use Behat\Behat\Context\SnippetAcceptingContext;
use Behat\Gherkin\Node\PyStringNode;
use Behat\Gherkin\Node\TableNode;
use Dotenv\Dotenv;
use Ramsey\Uuid\Uuid;
use RetailExpress\SkyLink\Apis\V2 as V2Api;
use RetailExpress\SkyLink\Customers\CustomerId;
use RetailExpress\SkyLink\Customers\V2CustomerRepository;
use RetailExpress\SkyLink\Products\ProductId;
use RetailExpress\SkyLink\Products\V2ProductRepository;
use RetailExpress\SkyLink\SalesChannelId;

/**
 * Defines application features from the specific context.
 */
class FeatureContext implements Context, SnippetAcceptingContext
{
    private $customerRepository;

    private $customer;

    private $productRepository;

    private $salesChannelId;

    private $product;

    /**
     * Initializes context.
     *
     * Every scenario gets its own context instance.
     * You can also pass arbitrary arguments to the
     * context constructor through behat.yml.
     */
    public function __construct()
    {
        // Load environment variables for sensitive credentials used in testing
        (new Dotenv(__DIR__.'/../..'))->load();

        $api = new V2Api(
            Uuid::fromString(getenv('V2_API_CLIENT_ID')),
            getenv('V2_API_DATABASE'),
            getenv('V2_API_USERNAME'),
            getenv('V2_API_PASSWORD')
        );

        // Initialise the Retail Express V2 Product Repository
        $this->customerRepository = new V2CustomerRepository($api);
        $this->productRepository = new V2ProductRepository($api);
    }

    /**
     * @When I find the customer with id :arg1
     */
    public function iFindTheCustomerWithId($customerId)
    {
        $this->customer = $this->customerRepository->find(new CustomerId($customerId));
    }

    /**
     * @Then I should see that their first name is :arg1
     */
    public function iShouldSeeThatTheirFirstNameIs($firstName)
    {
        if ($this->customer->getFirstName() !== $firstName) {
            throw new Exception("The customer's first name was \"{$firstName}\".");
        }
    }

    /**
     * @Then I should see that their last name is :arg1
     */
    public function iShouldSeeThatTheirLastNameIs($lastName)
    {
        if ($this->customer->getLastName() !== $lastName) {
            throw new Exception("The customer's last name was \"{$lastName}\".");
        }
    }

    /**
     * @Given I am connected to sales channel :arg1
     */
    public function iAmConnectedToSalesChannel($salesChannelId)
    {
        $this->salesChannelId = new SalesChannelId((int) $salesChannelId);
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

        if ($this->product === null) {
            throw new Exception("Failed to retrieve product with ID \"{$productId}\".");
        }
    }

    /**
     * @Then I should see that its sku is :arg1
     */
    public function iShouldSeeThatItsSkuIs($sku)
    {
        if ($this->product->getSku() !== $sku) {
            throw new Exception("SKU \"{$this->product->getSku()}\" was found.");
        }
    }
}
