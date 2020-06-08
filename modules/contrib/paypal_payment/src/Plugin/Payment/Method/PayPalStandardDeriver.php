<?php

namespace Drupal\paypal_payment\Plugin\Payment\Method;

/**
 * Derives payment method plugin definitions based on configuration entities.
 */
class PayPalStandardDeriver extends PayPalBasicDeriver {

  /**
   * {@inheritdoc}
   */
  protected function getId(): string {
    return 'paypal_payment_standard';
  }

}
