<?php

use Behat\Behat\Context\Context;
use Behat\Behat\Tester\Exception\PendingException;
use Behat\Behat\Context\SnippetAcceptingContext;
use Behat\Gherkin\Node\PyStringNode;
use Behat\Gherkin\Node\TableNode;

use RetailExpress\SkyLink\Vouchers\VoucherCode;
use ValueObjects\Number\Real;

trait VoucherFeatureContext
{
    private $voucherRepository;

    private $voucher;

    /**
     * @When I find the voucher with code :arg1
     */
    public function iFindTheVoucherWithCode($voucherCode)
    {
        $this->voucher = $this->voucherRepository->find(new VoucherCode($voucherCode));

        if (null === $this->voucher) {
            throw new Exception("No voucher exists with code {$voucherCode}.");
        }
    }

    /**
     * @Then the balance should be :arg1
     */
    public function theBalanceShouldBe($expectedBalance)
    {
        $expectedBalance = new Real($expectedBalance);
        $actualBalance = $this->voucher->getBalance();

        if (!$actualBalance->sameValueAs($expectedBalance)) {
            throw new Exception("Expected balance to be {$expectedBalance}, but got {$actualBalance}.");
        }
    }
}
