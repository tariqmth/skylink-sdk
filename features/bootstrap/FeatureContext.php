<?php

require_once __DIR__.'/CustomerFeatureContext.php';
require_once __DIR__.'/ProductFeatureContext.php';

use Behat\Behat\Context\Context;
use Behat\Behat\Tester\Exception\PendingException;
use Behat\Behat\Context\SnippetAcceptingContext;
use Behat\Gherkin\Node\PyStringNode;
use Behat\Gherkin\Node\TableNode;

use Dotenv\Dotenv;
use Ramsey\Uuid\Uuid;
use RetailExpress\SkyLink\Apis\V2 as V2Api;
use RetailExpress\SkyLink\Catalogue\Attributes\V2AttributeRepository;
use RetailExpress\SkyLink\Catalogue\Products\MatrixPolicyMapper;
use RetailExpress\SkyLink\Catalogue\Products\V2ProductRepository;
use RetailExpress\SkyLink\Customers\V2CustomerRepository;
use RetailExpress\SkyLink\Outlets\V2OutletRepository;
use RetailExpress\SkyLink\Sales\Orders\V2OrderRepository;
use RetailExpress\SkyLink\Sales\Payments\V2PaymentMethodRepository;
use RetailExpress\SkyLink\ValueObjects\SalesChannelId;
use RetailExpress\SkyLink\Vouchers\V2VoucherRepository;

/**
 * Defines application features from the specific context.
 */
class FeatureContext implements Context, SnippetAcceptingContext
{
    use CustomerFeatureContext;
    use OrderFeatureContext;
    use OutletFeatureContext;
    use PaymentMethodFeatureContext;
    use ProductFeatureContext;
    use VoucherFeatureContext;

    private $salesChannelId;

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
        $this->attributeRepository = new V2AttributeRepository($api);
        $this->productRepository = new V2ProductRepository(new MatrixPolicyMapper(), $api);
        $this->customerRepository = new V2CustomerRepository($api);
        $this->outletRepository = new V2OutletRepository($api);
        $this->orderRepository = new V2OrderRepository($api);
        $this->paymentMethodRepository = new V2PaymentMethodRepository($api);
        $this->voucherRepository = new V2VoucherRepository($api);
    }

    /**
     * @Given I am connected to sales channel :arg1
     */
    public function iAmConnectedToSalesChannel($salesChannelId)
    {
        $this->salesChannelId = new SalesChannelId((int) $salesChannelId);
    }
}
