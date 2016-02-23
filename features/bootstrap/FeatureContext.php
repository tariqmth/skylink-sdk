<?php

use Behat\Behat\Context\Context;
use Behat\Behat\Tester\Exception\PendingException;
use Behat\Behat\Context\SnippetAcceptingContext;
use Behat\Gherkin\Node\PyStringNode;
use Behat\Gherkin\Node\TableNode;
use Dotenv\Dotenv;
use Ramsey\Uuid\Uuid;
use RetailExpress\SkyLink\Apis\V2 as V2Api;
use RetailExpress\SkyLink\Customers\BillingAddress;
use RetailExpress\SkyLink\Customers\Customer;
use RetailExpress\SkyLink\Customers\CustomerId;
use RetailExpress\SkyLink\Customers\DeliveryAddress;
use RetailExpress\SkyLink\Customers\Email;
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

    private $pendingCustomerInformation = [];

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
     * @Then I should see that their full name is :arg1 :arg2
     */
    public function iShouldSeeThatTheirFullNameIs($expectedFirstName, $expectedLastName)
    {
        $actualFirstName = $this->customer->getBillingAddress()->getFirstName();
        $actualLastName = $this->customer->getBillingAddress()->getLastName();

        if ($actualFirstName !== $expectedFirstName) {
            throw new Exception("The customer's first name was \"{$actualFirstName}\".");
        }

        if ($actualLastName !== $expectedLastName) {
            throw new Exception("The customer's last name was \"{$actualLastName}\".");
        }
    }

    /**
     * @Then I should see their email is :arg1
     */
    public function iShouldSeeTheirEmailIs($expectedEmail)
    {
        $actualEmail = $this->customer->getEmail()->toString();

        if ($actualEmail !== $expectedEmail) {
            throw new Exception("The customer's email was \"{$actualEmail}\".");
        }
    }

    /**
     * @Then I should see they work for :arg1
     */
    public function iShouldSeeTheyWorkFor($expectedCompanyName)
    {
        $actualCompany = $this->customer->getBillingAddress()->getCompany();

        if ($actualCompany === null) {
            throw new Exception('The customer does not work for any company.');
        }

        $actualCompanyName = $actualCompany->getName();

        if ($actualCompanyName !== $expectedCompanyName) {
            throw new Exception("The customer works for \"{$actualCompanyName}\".");
        }
    }

    /**
     * @Then I should see their billing address is:
     */
    public function iShouldSeeTheirBillingAddressIs(PyStringNode $expectedBillingAddress)
    {
        $actualBillingAddress = $this->customer->getBillingAddress()->toString();

        if ($actualBillingAddress !== $expectedBillingAddress->getRaw()) {
            throw new Exception(<<<MESSAGE
The customer's address was:
{$actualBillingAddress}
MESSAGE
            );
        }
    }

    /**
     * @Then I should see they can be contacted by calling :arg1
     */
    public function iShouldSeeTheyCanBeContactedByCalling($expectedPhoneNumber)
    {
        $actualPhoneNumbers = $this->customer->getBillingAddress()->getPhones();

        if (count($actualPhoneNumbers) === 0) {
            throw new Exception('The customer does not have any phone numbers.');
        }

        if (!in_array($expectedPhoneNumber, $actualPhoneNumbers)) {
            $message = "The customer's phone numbers are:\n";

            foreach ($actualPhoneNumbers as $actualPhoneType => $actualPhoneNumber) {
                $message .= "- {$actualPhoneType}: {$actualPhoneNumber}\n";
            }

            throw new Exception($message);
        }
    }

    /**
     * @Given I use a unique email based on :arg1 and a password :arg2
     */
    public function iUseAUniqueEmailBasedOnAndAPassword($email, $password)
    {
        // We'll append the current date to the email recipient
        $email = preg_replace('/(.*)@(.*)/', '$1+'.date('Y-m-d-H-i-s').'@$2', $email);

        $this->pendingCustomerInformation = [
            'email' => new Email($email),
            'password' => $password,
        ];
    }

    /**
     * @Given I use :arg1 and :arg2 as the first and last name respectively
     */
    public function iUseAndAsTheFirstAndLastNameRespectively($firstName, $lastName)
    {
        $this->pendingCustomerInformation['firstName'] = $firstName;
        $this->pendingCustomerInformation['lastName'] = $lastName;
    }

    /**
     * @Then I should be able to register a customer based on these details
     */
    public function iShouldBeAbleToRegisterACustomerBasedOnTheseDetails()
    {
        extract($this->pendingCustomerInformation);

        $this->customer = Customer::register(
            $email,
            $password,
            BillingAddress::forIndividual($firstName, $lastName),
            DeliveryAddress::forIndividual($firstName, $lastName),
            true
        );
    }

    /**
     * @Then I should be able to add the customer to Retail Express
     */
    public function iShouldBeAbleToAddTheCustomerToRetailExpress()
    {
        $this->customerRepository->add($this->customer);
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
