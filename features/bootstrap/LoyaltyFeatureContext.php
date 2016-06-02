<?php

use Behat\Behat\Context\Context;
use Behat\Behat\Tester\Exception\PendingException;
use Behat\Behat\Context\SnippetAcceptingContext;
use Behat\Gherkin\Node\PyStringNode;
use Behat\Gherkin\Node\TableNode;

use RetailExpress\SkyLink\Customers\CustomerId;
use RetailExpress\SkyLink\Loyalty\Loyalty;

trait LoyaltyFeatureContext
{
    private $loyaltyRepository;

    private $currentLoyalty;

    /**
     * @Given I find the loyalty balance for customer with id :arg1
     */
    public function iFindTheLoyaltyBalanceForCustomerWithId($customerId)
    {
        $this->currentLoyalty = $this->loyaltyRepository->find(new CustomerId($customerId));
    }

    /**
     * @Then I can see the loyalty balance is :arg1
     */
    public function iCanSeeTheLoyaltyBalanceIs($expectedLoyalty)
    {
        if (null === $this->currentLoyalty) {
            throw new Exception('Unable to retrieve a loyalty balance.');
        }

        if (!$this->currentLoyalty->sameValueAs(new Loyalty($expectedLoyalty))) {
            throw new Exception("Loyalty balance was {$this->currentLoyalty}.");
        }
    }
}
