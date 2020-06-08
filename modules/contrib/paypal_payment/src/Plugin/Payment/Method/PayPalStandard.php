<?php

namespace Drupal\paypal_payment\Plugin\Payment\Method;

use Drupal\payment\Annotations\PaymentMethod;
use PayPal\Rest\ApiContext;

/**
 * PayPal Standard payment method.
 *
 * @PaymentMethod(
 *   deriver = "\Drupal\paypal_payment\Plugin\Payment\Method\PayPalStandardDeriver",
 *   id = "paypal_payment_standard",
 *   operations_provider = "\Drupal\paypal_payment\Plugin\Payment\Method\PayPalStandardOperationsProvider",
 * )
 */
class PayPalStandard extends PayPalBasic {

  /**
   * {@inheritdoc}
   */
  public function getWebhookUrl(): string {
    // TODO: Implement getWebhookUrl() method.
    return '';
  }

  /**
   * {@inheritdoc}
   */
  public function getWebhookId(): string {
    // TODO: Implement getWebhookId() method.
    return '';
  }

  /**
   * {@inheritdoc}
   */
  public function getApiContext($type): ApiContext {
    // TODO: Implement getApiContext() method.
  }

}
