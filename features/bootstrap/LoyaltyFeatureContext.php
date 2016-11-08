<?php

use RetailExpress\SkyLink\Sdk\Customers\CustomerId;
use RetailExpress\SkyLink\Sdk\Loyalty\Loyalty;

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
