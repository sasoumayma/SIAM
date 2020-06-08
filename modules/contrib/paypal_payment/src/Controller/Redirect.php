<?php

namespace Drupal\paypal_payment\Controller;

use Drupal\Core\Access\AccessResult;
use Drupal\Core\Controller\ControllerBase;
use Drupal\payment\Entity\PaymentInterface;
use Drupal\paypal_payment\Plugin\Payment\Method\PayPalBasic;
use Exception;
use PayPal\Api\Payment;
use PayPal\Api\PaymentExecution;
use PayPal\Rest\ApiContext;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Handles the "redirect" route.
 */
class Redirect extends ControllerBase {

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
   * @param \Drupal\payment\Entity\PaymentInterface $payment
   *
   * @return \Drupal\Core\Access\AccessResult
   */
  public function access(PaymentInterface $payment): AccessResult {
    return AccessResult::allowedIf($this->verify($payment));
  }

  /**
   * {@inheritdoc}
   */
  private function verify(PaymentInterface $payment) {
    /** @var PayPalBasic $payment_method */
    $payment_method = $payment->getPaymentMethod();
    return (
      $payment->getOwnerId() === $this->currentUser->id() &&
      $this->request->get('paymentId') === $payment_method->getPaymentId()
    );
  }

  /**
   * PayPal is redirecting the visitor here after the payment process. At this
   * point we don't know the status of the payment yet so we can only load
   * the payment and give control back to the payment context.
   *
   * @param PaymentInterface $payment
   * @return Response
   */
  public function execute(PaymentInterface $payment): Response {
    $paymentId = $this->request->get('paymentId');
    $payerID = $this->request->get('PayerID');

    /** @var PayPalBasic $payment_method */
    $payment_method = $payment->getPaymentMethod();
    /** @var ApiContext $api_context */
    $api_context = $payment_method->getApiContext($payment_method::PAYPAL_CONTEXT_TYPE_REDIRECT);

    $p = Payment::get($paymentId, $api_context);
    $execution = new PaymentExecution();
    $execution->setPayerId($payerID);
    try {
      $p->execute($execution, $api_context);
      $payment_method->doCapturePayment();
    } catch (Exception $ex) {
      // TODO: Error handling
    }

    return $this->getResponse($payment);
  }

  /**
   * @param \Drupal\payment\Entity\PaymentInterface $payment
   *
   * @return \Symfony\Component\HttpFoundation\Response
   */
  public function cancel(PaymentInterface $payment): Response {
    return $this->getResponse($payment);
  }

  /**
   * @param \Drupal\payment\Entity\PaymentInterface $payment
   *
   * @return \Symfony\Component\HttpFoundation\Response
   */
  private function getResponse(PaymentInterface $payment): Response {
    $response = $payment->getPaymentType()->getResumeContextResponse();
    return $response->getResponse();
  }

}
