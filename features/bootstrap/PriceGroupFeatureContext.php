<?php

use RetailExpress\SkyLink\Sdk\Customers\PriceGroups\PriceGroupKey;
use ValueObjects\StringLiteral\StringLiteral;

trait PriceGroupFeatureContext
{
    private $priceGroupRepository;

    private $priceGroups = [];

    /**
     * @When I get all price groups
     */
    public function iGetAllPriceGroups()
    {
        $this->priceGroups = $this->priceGroupRepository->all();
    }

    /**
     * @Then I should see there are :arg1 price groups
     */
    public function iShouldSeeThereArePriceGroups($count)
    {
        $priceGroupsCount = count($this->priceGroups);

        if ((int) $count !== $priceGroupsCount) {
            throw new Exception("There were {$priceGroupsCount} price groups.");
        }
    }

    /**
     * @Then I should see that :arg1 price group :arg2 is :arg3
     */
    public function iShouldSeeThatPriceGroupIs($priceGroupType, $priceGroupId, $name)
    {
        $priceGroupKey = PriceGroupKey::fromNative($priceGroupType, $priceGroupId);

        foreach ($this->priceGroups as $priceGroup) {
            if (!$priceGroup->getKey()->sameValueAs($priceGroupKey)) {
                continue;
            }

            if ($priceGroup->getName()->sameValueAs(new StringLiteral($name))) {
                return null;
            }

            throw new Exception("The price group's name was {$priceGroup->getName()}.");
        }

        throw new Exception("Could not find \"{$priceGroupType}\" price group {$priceGroupId}.");
    }
}
