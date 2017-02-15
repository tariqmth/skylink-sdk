<?php

require_once __DIR__.'/CustomerFeatureContext.php';
require_once __DIR__.'/ProductFeatureContext.php';

use Behat\Behat\Context\Context;
use Behat\Behat\Context\SnippetAcceptingContext;
use Dotenv\Dotenv;
use RetailExpress\SkyLink\Sdk\Apis\V2 as V2Api;
use RetailExpress\SkyLink\Sdk\Catalogue\Attributes\V2AttributeRepository;
use RetailExpress\SkyLink\Sdk\Catalogue\Eta\V2EtaRepository;
use RetailExpress\SkyLink\Sdk\Catalogue\Products\MatrixPolicyMapper;
use RetailExpress\SkyLink\Sdk\Catalogue\Products\V2ProductDeserializer;
use RetailExpress\SkyLink\Sdk\Catalogue\Products\V2ProductRepository;
use RetailExpress\SkyLink\Sdk\Customers\PriceGroups\V2PriceGroupRepository;
use RetailExpress\SkyLink\Sdk\Customers\V2CustomerRepository;
use RetailExpress\SkyLink\Sdk\Loyalty\FakeLoyaltyRepository;
use RetailExpress\SkyLink\Sdk\Outlets\V2OutletRepository;
use RetailExpress\SkyLink\Sdk\Sales\Orders\V2OrderRepository;
use RetailExpress\SkyLink\Sdk\Sales\Payments\V2PaymentMethodRepository;
use RetailExpress\SkyLink\Sdk\Sales\Payments\V2PaymentRepository;
use RetailExpress\SkyLink\Sdk\ValueObjects\SalesChannelId;
use RetailExpress\SkyLink\Sdk\Vouchers\V2VoucherRepository;

/**
 * Defines application features from the specific context.
 */
class FeatureContext implements Context, SnippetAcceptingContext
{
    use CustomerFeatureContext;
    use LoyaltyFeatureContext;
    use OrderFeatureContext;
    use OutletFeatureContext;
    use PaymentMethodFeatureContext;
    use PriceGroupFeatureContext;
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

        $api = V2Api::fromNative(
            getenv('V2_API_URL'),
            getenv('V2_API_CLIENT_ID'),
            getenv('V2_API_USERNAME'),
            getenv('V2_API_PASSWORD')
        );

        // Initialise the Retail Express V2 Product Repository
        $this->attributeRepository = new V2AttributeRepository($api);
        $this->etaRepository = new V2EtaRepository($api);
        $this->productRepository = new V2ProductRepository(
            new MatrixPolicyMapper(),
            new V2ProductDeserializer(),
            $api
        );
        $this->customerRepository = new V2CustomerRepository($api);
        $this->priceGroupRepository = new V2PriceGroupRepository($api);
        $this->loyaltyRepository = new FakeLoyaltyRepository();
        $this->outletRepository = new V2OutletRepository($api);
        $this->orderRepository = new V2OrderRepository($api);
        $this->paymentMethodRepository = new V2PaymentMethodRepository($api);
        $this->paymentRepository = new V2PaymentRepository($api);
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
