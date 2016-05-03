<?php

use Behat\Behat\Context\Context;
use Behat\Behat\Tester\Exception\PendingException;
use Behat\Behat\Context\SnippetAcceptingContext;
use Behat\Gherkin\Node\PyStringNode;
use Behat\Gherkin\Node\TableNode;

use RetailExpress\SkyLink\Sales\Payments\PaymentMethodId;
use ValueObjects\StringLiteral\StringLiteral;

trait PaymentMethodFeatureContext
{
    private $paymentMethodRepository;

    private $paymentMethods = [];

    /**
     * @When I get all payment methods
     */
    public function iGetAllPaymentMethods()
    {
        $this->paymentMethods = $this->paymentMethodRepository->all($this->salesChannelId);
    }

    /**
     * @Then I should see there are :arg1 payment methods
     */
    public function iShouldSeeThereArePaymentMethods($count)
    {
        $paymentMethodsCount = count($this->paymentMethods);

        if ((int) $count !== $paymentMethodsCount) {
            throw new Exception("There were {$paymentMethodsCount} outlets.");
        }
    }

    /**
     * @Then payment method :arg1 is known as :arg2
     */
    public function paymentMethodIsKnownAs($paymentMethodId, $name)
    {
        foreach ($this->paymentMethods as $paymentMethod) {
            if (!$paymentMethod->getId()->sameValueAs(new PaymentMethodId($paymentMethodId))) {
                continue;
            }

            if ($paymentMethod->getName()->sameValueAs(new StringLiteral($name))) {
                return;
            }

            throw new Exception("The payment method's name was {$paymentMethod->getName()}.");
        }

        throw new Exception("Could not find payment method {$paymentMethodId}.");
    }
}
