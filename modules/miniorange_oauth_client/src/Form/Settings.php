<?php

namespace Drupal\miniorange_oauth_client\Form;
use Drupal\Core\Form\FormBase;
use Drupal\miniorange_oauth_client\Utilities;
use Symfony\Component\HttpFoundation\RedirectResponse;

class Settings extends FormBase
{
    public function getFormId() {
        return 'miniorange_oauth_client_settings';
    }
/**
 * Showing Settings form.
 */
 public function buildForm(array $form, \Drupal\Core\Form\FormStateInterface $form_state) {
    global $base_url;
    $baseUrlValue = \Drupal::config('miniorange_oauth_client.settings')->get('miniorange_oauth_client_base_url');

    $attachments['#attached']['library'][] = 'miniorange_oauth_client/miniorange_oauth_client.admin';

    $form['markup_library'] = array(
        '#attached' => array(
            'library' => array(
                "miniorange_oauth_client/miniorange_oauth_client.admin",
                "miniorange_oauth_client/miniorange_oauth_client.style_settings",
                "miniorange_oauth_client/miniorange_oauth_client.slide_support_button",
            )
        ),
    );

    $form['header_top_style_1'] = array('#markup' => '<div class="mo_oauth_table_layout_1">');

    $form['markup_top'] = array(
         '#markup' => '<div class="mo_oauth_table_layout mo_oauth_container">',
    );

    $form['markup_top_vt_start'] = array(
         '#markup' => '<b><h3>SIGN IN SETTINGS</h3></b><hr><br/>'
    );

     $prefixname = '<div class="mo_oauth_row"><div class="mo_oauth_name">';
     $suffixname = '</div>';

     $prefixvalue = '<div class="mo_oauth_value">';
     $suffixvalue = '</div></div>';
     $module_path = \Drupal::service('extension.list.module')->getPath('miniorange_oauth_client');

     $form['miniorange_oauth_client_base_url_title'] = array(
         '#markup' => '<b>Base URL:</b> <div class="mo_oauth_tooltip"><img src="'.$base_url.'/'. $module_path . '/includes/images/info.png" alt="info icon" height="15px" width="15px"></div><div class="mo_oauth_tooltiptext"><b>Note: </b>You can customize base URL here. (For eg: https://www.xyz.com or http://localhost/abc)</div> ',
         '#prefix' => $prefixname,
         '#suffix' => $suffixname,
     );

    $form['miniorange_oauth_client_base_url'] = array(
        '#type' => 'textfield',
        '#default_value' => $baseUrlValue,
        '#attributes' => array('id'=>'mo_oauth_vt_baseurl','style' => 'width:73%;','placeholder' => 'Enter Base URL'),
        '#prefix' => $prefixvalue,
        '#suffix' => $suffixvalue,
    );

    $form['miniorange_oauth_client_siginin1'] = array(
        '#type' => 'submit',
        '#id' => 'button_config_center',
        '#value' => t('Update'),
        '#suffix' => '<br><hr>',
    );

    $form['miniorange_oauth_force_auth'] = array(
        '#type' => 'checkbox',
        '#title' => t('Protect website against anonymous access <a href="' . $base_url . '/admin/config/people/miniorange_oauth_client/licensing"><b>[Premium, Enterprise]</b></a>'),
        '#disabled' => TRUE,
        '#description' => t('<b>Note: </b>Users will be redirected to your OAuth server for login in case user is not logged in and tries to access website.<br><br>'),
    );

    $form['miniorange_oauth_auto_redirect'] = array(
        '#type' => 'checkbox',
        '#title' => t('Check this option if you want to <b> Auto-redirect to OAuth Provider/Server </b><a href="' . $base_url . '/admin/config/people/miniorange_oauth_client/licensing"><b>[Premium, Enterprise]</b></a>'),
        '#disabled' => TRUE,
        '#description' => t('<b>Note: </b>Users will be redirected to your OAuth server for login when the login page is accessed.<br><br>'),
    );

    $form['miniorange_oauth_enable_backdoor'] = array(
        '#type' => 'checkbox',
        '#title' => t('Check this option if you want to enable <b>backdoor login </b><a href="' . $base_url . '/admin/config/people/miniorange_oauth_client/licensing"><b>[Premium, Enterprise]</b></a>'),
        '#disabled' => TRUE,
        '#description' => t('<b>Note:</b> Checking this option creates a backdoor to login to your Website using Drupal credentials<br> incase you get locked out of your OAuth server.
                <b>Note down this URL: </b>Available in <a href="' . $base_url . '/admin/config/people/miniorange_oauth_client/licensing"><b>Premium, Enterprise</b></a> versions of the module.<br><br><br><br>'),
    );

    $form['markup_bottom_vt_start_auto_create_users'] = array(
        '#markup' => '<b><h3>Auto Create Users</h3></b><hr><p>This feature provides you with an option to automatically create a user if the user is not already present in Drupal</p>'
    );

    $form['miniorange_oauth_disable_autocreate_users'] = array(
        '#type' => 'checkbox',
        '#title' => t('Check this option if you want to disable <b>auto creation</b> of users if user does not exist.<a href="' . $base_url . '/admin/config/people/miniorange_oauth_client/licensing"><b>[Standard, Premium, Enterprise]</b></a>'),
	    '#disabled' => TRUE,
      );

     $form['markup_bottom_vt_start'] = array(
         '#markup' => '<br><br><b><h3>DOMAIN & PAGE RESTRICTION</h3></b><hr><br/>'
     );

     $form['miniorange_oauth_client_white_list_url_title'] = array(
         '#markup' => '<b>Allowed Domains</b> &nbsp;&nbsp;&nbsp;<a href="' . $base_url . '/admin/config/people/miniorange_oauth_client/licensing"><b>[Enterprise]</b></a>  <div class="mo_oauth_tooltip"><img src="'.$base_url.'/'. $module_path . '/includes/images/info.png" alt="info icon" height="15px" width="15px"></div><div class="mo_oauth_tooltiptext"><b>Note: </b> Enter <b>semicolon(;) separated</b> domains to allow SSO. Other than these domains will not be allowed to do SSO</div>',
         '#prefix' => $prefixname,
         '#suffix' => $suffixname,
     );

     $form['miniorange_oauth_client_white_list_url'] = array(
         '#type' => 'textfield',
         '#attributes' => array('style' => 'width:73%','placeholder' => 'Enter semicolon(;) separated domains (Eg. xxxx.com; xxxx.com)'),
         '#disabled' => TRUE,
         '#prefix' => $prefixvalue,
         '#suffix' => $suffixvalue,
     );

     $form['miniorange_oauth_client_black_list_url_title'] = array(
         '#markup' => '<b>Restricted Domains</b> <a href="' . $base_url . '/admin/config/people/miniorange_oauth_client/licensing"><b>[Enterprise]</b></a>  <div class="mo_oauth_tooltip"><img src="'.$base_url.'/'. $module_path . '/includes/images/info.png" alt="info icon" height="15px" width="15px"></div><div class="mo_oauth_tooltiptext"><b>Note: </b> Enter <b>semicolon(;) separated</b> domains to allow SSO. Other than these domains will not be allowed to do SSO</div>',
         '#prefix' => $prefixname,
         '#suffix' => $suffixname,
     );

     $form['miniorange_oauth_client_black_list_url'] = array(
         '#type' => 'textfield',
         '#attributes' => array('style' => 'width:73%','placeholder' => 'Enter semicolon(;) separated domains (Eg. xxxx.com; xxxx.com)'),
         '#disabled' => TRUE,
         '#prefix' => $prefixvalue,
         '#suffix' => $suffixvalue,
     );

     $form['miniorange_oauth_client_page_restrict_url_title'] = array(
         '#markup' => '<b>Page Restriction</b>&nbsp; <a href="' . $base_url . '/admin/config/people/miniorange_oauth_client/licensing"><b>[Enterprise]</b></a>  <div class="mo_oauth_tooltip"><img src="'.$base_url.'/'. $module_path . '/includes/images/info.png" alt="info icon" height="15px" width="15px"></div><div class="mo_oauth_tooltiptext"><b>Note: </b> Enter <b>semicolon(;) separated</b> URLs to restrict unauthorized access</div>',
         '#prefix' => $prefixname,
         '#suffix' => $suffixname,
     );

     $form['miniorange_oauth_client_page_restrict_url'] = array(
         '#type' => 'textfield',
         '#attributes' => array('style' => 'width:73%','placeholder' => 'Enter semicolon(;) separated page URLs (Eg. xxxx.com/yyy; xxxx.com/yyy)'),
         '#disabled' => TRUE,
         '#prefix' => $prefixvalue,
         '#suffix' => $suffixvalue,
     );

    $form['miniorange_oauth_client_siginin'] = array(
            '#type' => 'button',
            '#id' => 'button_config_center',
            '#value' => t('Save Configuration'),
            '#disabled' => TRUE,
    );

    $form['mo_header_style_end'] = array('#markup' => '</div>');

    Utilities::nofeaturelisted($form, $form_state);

    $form['mo_markup_div_imp']=array('#markup'=>'</div>');
    Utilities::AddSupportButton($form, $form_state);
    return $form;
 }

 public function submitForm(array &$form, \Drupal\Core\Form\FormStateInterface $form_state) {
    $baseUrlvalue = trim($form['miniorange_oauth_client_base_url']['#value']);
     if(!empty($baseUrlvalue) && filter_var($baseUrlvalue, FILTER_VALIDATE_URL) == FALSE) {
         \Drupal::messenger()->adderror(t('Please enter a valid URL'));
         return;
     }
    \Drupal::configFactory()->getEditable('miniorange_oauth_client.settings')->set('miniorange_oauth_client_base_url', $baseUrlvalue)->save();
    \Drupal::messenger()->addMessage(t('Attribute Mapping saved successfully.'));
 }

 public function saved_support(array &$form, \Drupal\Core\Form\FormStateInterface $form_state) {

        $email = trim($form['miniorange_oauth_client_email_address']['#value']);
        $phone = $form['miniorange_oauth_client_phone_number']['#value'];
        $query = trim($form['miniorange_oauth_client_support_query']['#value']);
        Utilities::send_support_query($email, $phone, $query);
    }

    public function rfd(array &$form, \Drupal\Core\Form\FormStateInterface $form_state) {
        global $base_url;
        $response = new RedirectResponse($base_url."/admin/config/people/miniorange_oauth_client/request_for_demo");
        $response->send();
    }
}