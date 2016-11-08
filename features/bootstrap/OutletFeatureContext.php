<?php

use Behat\Behat\Context\Context;
use Behat\Behat\Tester\Exception\PendingException;
use Behat\Behat\Context\SnippetAcceptingContext;
use Behat\Gherkin\Node\PyStringNode;
use Behat\Gherkin\Node\TableNode;

use RetailExpress\SkyLink\Sdk\Outlets\OutletId;
use ValueObjects\StringLiteral\StringLiteral;

trait OutletFeatureContext
{
    private $outletRepository;

    private $outlets = [];

    /**
     * @When I get all outlets
     */
    public function iGetAllOutlets()
    {
        $this->outlets = $this->outletRepository->all($this->salesChannelId);
    }

    /**
     * @Then I should see there are :arg1 outlets
     */
    public function iShouldSeeThereAreOutlets($count)
    {
        $outletsCount = count($this->outlets);

        if ((int) $count !== $outletsCount) {
            throw new Exception("There were {$outletsCount} outlets.");
        }
    }

    /**
     * @Then outlet :arg1 is known as :arg2
     */
    public function outletIsKnownAs($outletId, $name)
    {
        foreach ($this->outlets as $outlet) {
            if (!$outlet->getId()->sameValueAs(new OutletId($outletId))) {
                continue;
            }

            if ($outlet->getName()->sameValueAs(new StringLiteral($name))) {
                return null;
            }

            throw new Exception("The outlet's name was {$outlet->getName()}.");
        }

        throw new Exception("Could not find outlet {$outletId}.");
    }
}
