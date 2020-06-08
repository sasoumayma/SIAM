<?php

namespace Drupal\paypal_payment\Controller;

use Drupal\Core\Access\AccessResult;
use Drupal\Core\Controller\ControllerBase;
use Drupal\payment\Payment;
use Drupal\paypal_payment\Plugin\Payment\Method\PayPalBasic;
use Exception;
use PayPal\Api\VerifyWebhookSignature;
use PayPal\Api\WebhookEvent;
use RuntimeException;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Handles the "webhook" route.
 */
class Webhook extends ControllerBase {

  /**
   * @var \Symfony\Component\HttpFoundation\Request
   */
  protected $request;

  /**
   * Redirect constructor.
   *
   * @param \Symfony\Component\HttpFoundation\Request $request
   */
  public function __construct(Request $request) {
    $this->request = $request;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    /** @noinspection NullPointerExceptionInspection */
    return new static(
      $container->get('request_stack')->getCurrentRequest()
    );
  }

  /**
   * @param $payment_method_id
   *
   * @return \Drupal\Core\Access\AccessResult
   */
  public function access($payment_method_id): AccessResult {
    return AccessResult::allowedIf($this->verify($payment_method_id));
  }

  /**
   * @param string $payment_method_id
   *
   * @return bool
   */
  private function verify(string $payment_method_id): bool {
    try {
      /** @var PayPalBasic $payment_method */
      $payment_method = Payment::methodManager()
        ->createInstance('paypal_payment_express:' . $payment_method_id);
      if (!($payment_method instanceof PayPalBasic)) {
        throw new RuntimeException('Unsupported web hook');
      }

      $body = $this->request->getContent();

      $resource = new VerifyWebhookSignature();
      $resource->setAuthAlgo($this->request->headers->get('paypal-auth-algo'));
      $resource->setCertUrl($this->request->headers->get('paypal-cert-url'));
      $resource->setTransmissionId($this->request->headers->get('paypal-transmission-id'));
      $resource->setTransmissionSig($this->request->headers->get('paypal-transmission-sig'));
      $resource->setTransmissionTime($this->request->headers->get('paypal-transmission-time'));
      $resource->setRequestBody($body);
      $resource->setWebhookId($payment_method->getWebhookId());

      $response = $resource->post($payment_method->getApiContext($payment_method::PAYPAL_CONTEXT_TYPE_WEBHOOK));
      if ($response->getVerificationStatus() === 'SUCCESS') {
        return TRUE;
      }

    } catch (Exception $ex) {
      // TODO: Error handling
    }

    return FALSE;
  }

  /**
   * PayPal calls this after the payment status has been changed.
   *
   * @param string $payment_method_id
   *
   * @return \Symfony\Component\HttpFoundation\Response
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   */
  public function execute(string $payment_method_id): Response {
    $webhook = new WebhookEvent($this->request->getContent());
    $event_type_status_map = [
      'PAYMENT.AUTHORIZATION.CREATED' => 'payment_authorized',
      'PAYMENT.AUTHORIZATION.VOIDED' => 'payment_authorization_failed',
      'PAYMENT.CAPTURE.COMPLETED' => 'payment_success',
      'PAYMENT.CAPTURE.REFUNDED' => 'payment_refunded',
      'PAYMENT.SALE.COMPLETED' => 'payment_success',
      'PAYMENT.SALE.REFUNDED' => 'payment_refunded',
    ];
    $resource = $webhook->getResource();
    $payment_id = $resource->invoice_number;

    $payment = $this->entityTypeManager()
      ->getStorage('payment')
      ->load($payment_id);
    if ($payment && $payment->getPaymentMethod()
        ->getPaymentId() === $resource->parent_payment) {
      $payment_status = Payment::statusManager()
        ->createInstance($event_type_status_map[$webhook->getEventType()]);
      $payment->setPaymentStatus($payment_status)->save();
    }

    return new Response();
  }

}
