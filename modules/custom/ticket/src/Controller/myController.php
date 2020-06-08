<?php
/**
  * @file
  * Contains \Drupal\ticket\Controller\ticketController' 
  */
 
namespace Drupal\ticket\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\node\Entity\Node;
use Drupal\commerce_price\Price;
use Drupal\commerce_order\Entity\OrderItem;
use Drupal\commerce_order\Entity\Order;
use Drupal\Core\Cache\CacheableMetadata;
use Drupal\commerce\commerce_product;
use Drupal\commerce;
use Drupal\commerce_cart;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Drupal\commerce_cart\CartProviderInterface;
use Drupal\commerce_cart\CartManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Url;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Drupal\faq_ask\Utility;
use Drupal\commerce_product\Entity\ProductVariation;
use Drupal\commerce_product\Entity\Product;
use \Drupal\Core\Entity\Query\QueryInterface;
use \Drupal\Core\Entity\EntityInterface;


class myController extends ControllerBase {
  
     public function content($order_id) {
       include('vendor/autoload.php');
       $mpdf = new \Mpdf\Mpdf();
       
       // Define the Header
       
       $html='<div style="text-align: center; font-weight: bold;">
         SOUS LE HAUT PATRONAGE DE SA MAJESTE LE ROI MOHAMMED VI
         <img src="/themes/magazine_lite/images/logo.PNG" /> 
         </div>';
       $mpdf->WriteHTML($html);
     
       $order = Order::load($order_id);
       $mpdf->WriteHTML('<p>    </p>');

       //  Order_id
       $mpdf->WriteHTML("Order id : ".$order->id());
       // Order_number
       $mpdf->WriteHTML("Order number : ".$order->getOrderNumber()); 
       //Prix total payé
       $total_price = $order->getTotalprice();
       $mpdf->WriteHTML( "Prix total payé  : ".$total_price);
       //Email user
       $mail = $order->get('mail')->value;
       $mpdf->WriteHTML( "Email  : ".$mail);

       
      //  $currency = $order->getTotalprice()->getCurrencyCode();
      //  $mpdf->WriteHTML( "Prix  : ".$currency);
      $mpdf->WriteHTML('<p>    </p>');

      $html='<div style=" color: green;">
      -------------------------------------------------------------------------------------------------------------------------------------------------------
      </div>';
    $mpdf->WriteHTML($html);

       foreach ($order->getItems() as $key => $order_id) 
       {
        $mpdf->WriteHTML('<p>    </p>');
        $mpdf->WriteHTML('<p>    </p>');
        
          // Type du ticket (name)
           $product_variation = $order_id->getPurchasedEntity();
           $typeTicket = $product_variation->get('title')->getValue()[0]['value'];
           $product_id = $product_variation->get('product_id')->getValue()[0]['target_id'];
           $mpdf->WriteHTML('Type du ticket : '.$typeTicket);
           
          // Quantité commandée
           $quantity = $order_id->getQuantity();
           $mpdf->WriteHTML("Quantité : ".$quantity);
 
          // Prix total par type
           $totalPrice = $order_id->getTotalPrice();
           $mpdf->WriteHTML( "Prix total par type : ".$totalPrice);

           //Etat commande
           $state = $order->get('state')->value;
           $mpdf->WriteHTML( "Etat de la commande : ".$state);

            //Getting Taxonomy
            $storage = \Drupal::entityTypeManager()->getStorage('commerce_product');
            $products = $storage->load($product_id);
            $categorie_id= $products->get('field_product_category')->getValue()[0]['target_id'];
            $taxonomy= \Drupal\taxonomy\Entity\Term::load($categorie_id)->get('name')->value;
            $mpdf->WriteHTML( "Date de validite du ticket : ".$taxonomy);

        
          //dump($products);
           
            
       }
       // Define the Footer 
       $mpdf->SetHTMLFooter('
       <table width="100%">
       <tr>
        <td width="33%">{DATE j-m-Y}</td>
        <td width="33%" align="center">{PAGENO}/{nbpg}</td>
        <td width="33%" style="text-align: right;">15éme Edition SIAM</td>
        <div class="barcodecell"><barcode code="152020" type="I25" class="barcode" height="0.66"/></div>
       </tr>
       </table>');
       
      $mpdf->Output();
  }

}