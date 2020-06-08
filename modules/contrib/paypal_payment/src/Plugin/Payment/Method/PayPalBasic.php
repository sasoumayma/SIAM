<?php

namespace Drupal\paypal_payment\Plugin\Payment\Method;

use Drupal\Core\Url;
use Drupal\payment\OperationResult;
use Drupal\payment\Plugin\Payment\Method\Basic;
use Drupal\payment\Response\Response;
use Exception;
use PayPal\Api\Amount;
use PayPal\Api\Item;
use PayPal\Api\ItemList;
use PayPal\Api\Payer;
use PayPal\Api\Payment;
use PayPal\Api\RedirectUrls;
use PayPal\Api\Transaction;
use PayPal\Rest\ApiContext;

/**
 * Abstract class for PayPal payment methods.
 */
abstract class PayPalBasic extends Basic {

  const PAYPAL_CONTEXT_TYPE_ADMIN    = 'admin';
  const PAYPAL_CONTEXT_TYPE_CREATE   = 'create';
  const PAYPAL_CONTEXT_TYPE_WEBHOOK  = 'webhook';
  const PAYPAL_CONTEXT_TYPE_REDIRECT = 'redirect';

  const PAYPAL_DEFAULT_CURRENCY = 'USD';

  /**
   * @var \Drupal\payment\Response\Response
   */
  protected $paymentExecutionResult;

  /**
   * @return string
   */
  abstract public function getWebhookUrl(): string;

  /**
   * @return string
   */
  abstract public function getWebhookId(): string;

  /**
   * @param string $type
   * @return \PayPal\Rest\ApiContext
   */
  abstract public function getApiContext($type): ApiContext;

  /**
   * @param $paymentId
   *
   * @throws \Drupal\Core\Entity\EntityStorageException
   */
  private function setPaymentId($paymentId) {
    $this->configuration['paymentID'] = $paymentId;
    $this->getPayment()->save();
  }

  /**
   * @return mixed|null
   */
  public function getPaymentId() {
    return $this->configuration['paymentID'] ?? NULL;
  }

  /**
   * {@inheritdoc}
   */
  public function getPaymentExecutionResult() {
    return new OperationResult($this->paymentExecutionResult);
  }

  /**
   * {@inheritdoc}
   */
  protected function doExecutePayment() {
    parent::doExecutePayment();
    $payer = new Payer();
    $payer->setPaymentMethod('paypal');

    $itemList = new ItemList();
    $totalAmount = 0;
    $currency = self::PAYPAL_DEFAULT_CURRENCY;
    foreach ($this->getPayment()->getLineItems() as $line_item) {
      $totalAmount += $line_item->getTotalAmount();
      $line_item_currency = $line_item->getCurrencyCode();

      $item = new Item();
      $item->setName($line_item->getName())
        ->setCurrency($line_item_currency)
        ->setQuantity($line_item->getQuantity())
        ->setPrice($line_item->getAmount());
      $itemList->addItem($item);

      if ($line_item_currency !== $currency) {
        if ($currency !== self::PAYPAL_DEFAULT_CURRENCY) {
          // This is the second time we are changing the currency which means
          // that our line items have mixed currencies. This aion't gonna work!

          # TODO: clarify with the payment maintainer how we should handle this
          $this->messenger()->addError($this->t('Mixed currencies detected which is not yet supported.'));
          return;
        }
        $currency = $line_item_currency;
      }
    }

    $redirectSuccess = new Url('paypal_payment.redirect.success',
      ['payment' => $this->getPayment()->id()], ['absolute' => TRUE]);
    $redirectCancel = new Url('paypal_payment.redirect.cancel',
      ['payment' => $this->getPayment()->id()], ['absolute' => TRUE]);

    $redirectUrls = new RedirectUrls();
    $redirectUrls->setReturnUrl($redirectSuccess->toString(TRUE)->getGeneratedUrl())
      ->setCancelUrl($redirectCancel->toString(TRUE)->getGeneratedUrl());

    $amount = new Amount();
    $amount->setCurrency($currency)
      ->setTotal($totalAmount);

    $transaction = new Transaction();
    $transaction->setAmount($amount)
      ->setItemList($itemList)
      ->setDescription($this->getPayment()->id())
      ->setInvoiceNumber($this->getPayment()->id())
      ->setNotifyUrl($this->getWebhookUrl());

    $payment = new Payment();
    $payment->setIntent('sale')
      ->setPayer($payer)
      ->setRedirectUrls($redirectUrls)
      ->setTransactions([$transaction]);

    try {
      $payment->create($this->getApiContext(self::PAYPAL_CONTEXT_TYPE_CREATE));
      $this->setPaymentId($payment->getId());
      $url = Url::fromUri($payment->getApprovalLink());
      $this->paymentExecutionResult = new Response($url);
    }
    catch (Exception $ex) {
      # TODO: clarify with the payment maintainer how we should handle Exceptions
      $this->paymentExecutionResult = NULL;
    }
  }

}
