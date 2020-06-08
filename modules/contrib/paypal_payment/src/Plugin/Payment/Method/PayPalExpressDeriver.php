<?php

namespace Drupal\paypal_payment\Plugin\Payment\Method;

/**
 * Derives payment method plugin definitions based on configuration entities.
 */
class PayPalExpressDeriver extends PayPalBasicDeriver {

  /**
   * {@inheritdoc}
   */
  protected function getId(): string {
    return 'paypal_payment_express';
  }

}
