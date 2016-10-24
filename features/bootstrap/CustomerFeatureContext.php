<?php

use Behat\Behat\Context\Context;
use Behat\Behat\Tester\Exception\PendingException;
use Behat\Behat\Context\SnippetAcceptingContext;
use Behat\Gherkin\Node\PyStringNode;
use Behat\Gherkin\Node\TableNode;

use RetailExpress\SkyLink\Sdk\Customers\BillingContact;
use RetailExpress\SkyLink\Sdk\Customers\Customer;
use RetailExpress\SkyLink\Sdk\Customers\CustomerId;
use RetailExpress\SkyLink\Sdk\Customers\NewsletterSubscription;
use RetailExpress\SkyLink\Sdk\Customers\ShippingContact;
use ValueObjects\StringLiteral\StringLiteral;
use ValueObjects\Web\EmailAddress;

trait CustomerFeatureContext
{
    private $customerRepository;

    private $customer;

    private $pendingCustomerInformation = [];

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
        $name = $this->customer->getBillingContact()->getName();
        $actualFirstName = $name->getFirstName();
        $actualLastName = $name->getLastName();

        if (!$actualFirstName->sameValueAs(new StringLiteral($expectedFirstName))) {
            throw new Exception("The customer's first name was \"{$actualFirstName}\".");
        }

        if (!$actualLastName->sameValueAs(new StringLiteral($expectedLastName))) {
            throw new Exception("The customer's first name was \"{$actualLastName}\".");
        }
    }

    /**
     * @Then I should see their email is :arg1
     */
    public function iShouldSeeTheirEmailIs($expectedEmailAddress)
    {
        $actualEmailAddress = $this->customer->getBillingContact()->getEmailAddress();

        if (!$actualEmailAddress->sameValueAs(new EmailAddress($expectedEmailAddress))) {
            throw new Exception("The customer's email was \"{$actualEmailAddress}\".");
        }
    }

    /**
     * @Then I should see they work for :arg1
     */
    public function iShouldSeeTheyWorkFor($expectedCompanyName)
    {
        $actualCompanyName = $this->customer->getBillingContact()->getCompanyName();

        if ($actualCompanyName->isEmpty()) {
            throw new Exception('The customer does not work for any company.');
        }

        if (!$actualCompanyName->sameValueAs(new StringLiteral($expectedCompanyName))) {
            throw new Exception("The customer works for \"{$actualCompanyName}\".");
        }
    }

    /**
     * @Then I should see their billing contact is:
     */
    public function iShouldSeeTheirBillingContactIs(PyStringNode $expectedBillingAddress)
    {
        $actualBillingAddress = (string) $this->customer->getBillingContact();

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
        $actualPhoneNumber = $this->customer->getBillingContact()->getPhoneNumber();

        if (!$actualPhoneNumber->sameValueAs(new StringLiteral($expectedPhoneNumber))) {
            throw new Exception("The customer's phone number was \"{$actualPhoneNumber}\".");
        }
    }

    /**
     * @Given I use a unique email based on :arg1 and a password :arg2
     */
    public function iUseAUniqueEmailBasedOnAndAPassword($emailAddress, $password)
    {
        // We'll append the current date to the email recipient
        $emailAddress = preg_replace('/(.*)@(.*)/', '$1+'.date('Y-m-d-H-i-s').'@$2', $emailAddress);

        $this->pendingCustomerInformation = [
            'emailAddress' => $emailAddress,
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
            new StringLiteral($password),
            BillingContact::fromNative($firstName, $lastName, $emailAddress),
            ShippingContact::fromNative(),
            new NewsletterSubscription(true)
        );
    }

    /**
     * @Then I should be able to add the customer to Retail Express
     */
    public function iShouldBeAbleToAddTheCustomerToRetailExpress()
    {
        $this->customerRepository->add($this->customer);
    }
}
