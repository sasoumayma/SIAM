<?php

/**
 * @file
 * Contains \Drupal\miniorange_oauth_client\Form\MiniorangeGeneralSettings.
 */

namespace Drupal\miniorange_oauth_client\Form;
use Drupal\miniorange_oauth_client\mo_saml_visualTour;
use Drupal\miniorange_oauth_client\Utilities;
use Drupal\Core\Form\FormBase;
use Drupal\miniorange_oauth_client\MiniorangeOAuthClientSupport;
use Symfony\Component\HttpFoundation\RedirectResponse;

class MiniorangeMapping extends FormBase
{

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'miniorange_mapping';
  }


  public function buildForm(array $form, \Drupal\Core\Form\FormStateInterface $form_state)
  {
      global $base_url;

      $moTour = mo_saml_visualTour::genArray();
      $form['tourArray'] = array(
          '#type' => 'hidden',
          '#value' => $moTour,
      );

      $form['markup_library'] = array(
        '#attached' => array(
            'library' => array(
                "miniorange_oauth_client/miniorange_oauth_client.admin",
                "miniorange_oauth_client/miniorange_oauth_client.style_settings",
                "miniorange_oauth_client/miniorange_oauth_client.Vtour",
            )
        ),
      );

      $form['header_top_style_1'] = array('#markup' => '<div class="mo_oauth_table_layout_1">');

      $form['markup_top'] = array(
          '#markup' => '<div class="mo_oauth_table_layout mo_oauth_container">',
      );

      $form['markup_top_vt_start'] = array(
          '#markup' => '<h3>ATTRIBUTE MAPPING&nbsp;&nbsp; <a id="Restart_moTour" class="mo_oauth_btn mo_oauth_btn-primary-color mo_oauth_btn-large">Take a Tour</a></h3><hr><br>',
      );

      $prefixname = '<div class="mo_oauth_row"><div class="mo_oauth_name">';
      $suffixname = '</div>';

      $prefixvalue = '<div class="mo_oauth_value">';
      $suffixvalue = '</div></div>';

      $module_path = \Drupal::service('extension.list.module')->getPath('miniorange_oauth_client');

      $email_attr = \Drupal::config('miniorange_oauth_client.settings')->get('miniorange_oauth_client_email_attr_val');
      $name_attr =\Drupal::config('miniorange_oauth_client.settings')->get('miniorange_oauth_client_name_attr_val');

      $form['mo_vt_id_start1'] = array(
          '#markup' => '<div id = "mo_oauth_vt_attrn">',
      );

      $form['miniorange_oauth_client_email_attr_title'] = array(
          '#markup' => '<b>Email Attribute: </b> <div class="mo_oauth_tooltip"><img src="'.$base_url.'/'. $module_path . '/includes/images/info.png" alt="info icon" height="15px" width="15px"></div><div class="mo_oauth_tooltiptext">This field is mandatory for login</div>',
          '#prefix' => $prefixname,
          '#suffix' => $suffixname,
      );

      $form['miniorange_oauth_client_email_attr'] = array(
        '#type' => 'textfield',
        '#default_value' => $email_attr,
        '#attributes' => array('style' => 'width:73%;','placeholder' => 'Enter Email Attribute'),
        '#prefix' => $prefixvalue,
        '#suffix' => $suffixvalue,
      );

      $form['mo_vt_id_end1'] = array(
          '#markup' => '</div>',
      );

      $form['mo_vt_id_start2'] = array(
          '#markup' => '<div id = "mo_oauth_vt_attre">',
      );

      $form['miniorange_oauth_client_name_attr_title'] = array(
          '#markup' => '<b>Username Attribute: </b> <div class="mo_oauth_tooltip"><img src="'.$base_url.'/'. $module_path . '/includes/images/info.png" alt="info icon" height="15px" width="15px"></div><div class="mo_oauth_tooltiptext"><b>Note:</b> If this text field is empty, then by default email id will be the user\'s username</div>',
          '#prefix' => $prefixname,
          '#suffix' => $suffixname,
      );

      $form['miniorange_oauth_client_name_attr'] = array(
        '#type' => 'textfield',
        '#default_value' => $name_attr,
        '#attributes' => array('style' => 'width:73%;','placeholder' => 'Enter Username Attribute'),
        '#prefix' => $prefixvalue,
        '#suffix' => $suffixvalue,
      );

      $form['mo_vt_id_end2'] = array(
          '#markup' => '</div>',
      );

      $form['miniorange_oauth_client_attr_setup_button_2'] = array(
        '#type' => 'submit',
        '#id' => 'button_config_center',
        '#value' => t('Save Configuration'),
        '#submit' => array('::miniorange_oauth_client_attr_setup_submit'),
      );

      $form['markup_cam'] = array(
        '#markup' => '<br><h3>CUSTOM ATTRIBUTE MAPPING <b><a href="' . $base_url . '/admin/config/people/miniorange_oauth_client/licensing"> [STANDARD, PREMIUM, ENTERPRISE]</a></b></h3><hr><br>
                      <div class="mo_oauth_highlight_background_note_1">Add the Drupal field attributes in the Attribute Name textfield and add the OAuth Server attributes that you need to map with the drupal attributes in the OAuth Server Attribute Name textfield.
                       Drupal Field Attributes will be of type text. Add the machine name of the attribute in the Drupal Attribute textfield.
                      <b>For example: </b>If the attribute name in the drupal is name then its machine name will be field_name.</div><br>',
      );

      $form['miniorange_oauth_attr_name'] = array(
          '#type' => 'textfield',
          '#prefix' => '<div><table><tr><td>',
          '#suffix' => '</td>',
          '#id' => 'text_field1',
          '#title' => t('OAuth Server Attribute Name 1'),
          '#attributes' => array('style' => 'width:73%;background-color: hsla(0,0%,0%,0.08) !important;','placeholder' => 'Enter OAuth Server Attribute Name'),
          '#required' => FALSE,
          '#disabled' => TRUE,
      );
      $form['miniorange_oauth_server_name'] = array(
          '#type' => 'textfield',
          '#id' => 'text_field2',
          '#prefix' => '<td>',
          '#suffix' => '</td>',
          '#title' => t('Attribute Name 1'),
          '#attributes' => array('style' => 'width:73%;background-color: hsla(0,0%,0%,0.08) !important;','placeholder' => 'Enter Attribute Name'),
          '#required' => FALSE,
          '#disabled' => TRUE,
      );
      $form['miniorange_oauth_add_name'] = array(
          '#prefix' => '<td>',
          '#suffix' => '</td>',
          '#type' => 'button',
          '#disabled' => 'true',
          '#attributes' => array('style' => 'background-color: lightgreen;color:white'),
          '#value' => '+',
      );
      $form['miniorange_oauth_sub_name'] = array(
          '#prefix' => '<td>',
          '#suffix' => '</td></tr></table></div>',
          '#type' => 'button',
          '#disabled' => 'true',
          '#attributes' => array('style' => 'background-color: red;color:white'),
          '#value' => '-',
      );

      $form['markup_role'] = array(
        '#markup' => '<br><h3>Custom Role Mapping <a href="' . $base_url . '/admin/config/people/miniorange_oauth_client/licensing"> [PREMIUM, ENTERPRISE]</a></h3><hr><br>',
      );

      $form['miniorange_disable_attribute'] = array(
        '#type' => 'checkbox',
        '#title' => t('Do not update existing user&#39;s role.'),
	    '#disabled' => TRUE,
      );

      $form['miniorange_oauth_disable_role_update'] = array(
        '#type' => 'checkbox',
        '#title' => t('Check this option if you do not want to update user role if roles not mapped.'),
	    '#disabled' => TRUE,
      );

      $mrole= user_role_names($membersonly = TRUE);
      $drole = array_values($mrole);

      $form['miniorange_oauth_default_mapping'] = array(
          '#type' => 'select',
          '#id' => 'miniorange_oauth_client_app',
          '#title' => t('Select default group for the new users'),
          '#options' => $mrole,
          '#default_value' => $drole,
          '#attributes' => array('style' => 'width:73%;'),
          '#disabled' => TRUE,
      );

      foreach($mrole as $roles) {
          $rolelabel = str_replace(' ','',$roles);
          $form['miniorange_oauth_role_' . $rolelabel] = array(
              '#type' => 'textfield',
              '#title' => t($roles),
              '#default_value' => \Drupal::config('miniorange_oauth_client.settings')->get('miniorange_oauth_role_' . $rolelabel, ''),
              '#attributes' => array('style' => 'width:73%;background-color: hsla(0,0%,0%,0.08) !important;','placeholder' => 'Semi-colon(;) separated Group/Role value for ' . $roles),
              '#disabled' => TRUE,
          );
      }


      $form['markup_role_signin'] = array(
        '#markup' => '<br><h3>Custom Login/Logout (Optional)</h3><hr>'
      );

      $form['miniorange_oauth_client_login_url'] = array(
          '#type' => 'textfield',
          '#id' => 'text_field3',
          '#required' => FALSE,
          '#disabled' => TRUE,
          '#attributes' => array('style' => 'width:73%;background-color: hsla(0,0%,0%,0.08) !important;','placeholder' => 'Enter Login URL'),
      );

      $form['miniorange_oauth_client_logout_url'] = array(
          '#type' => 'textfield',
          '#id' => 'text_field4',
          '#required' => FALSE,
          '#disabled' => TRUE,
          '#attributes' => array('style' => 'width:73%;background-color: hsla(0,0%,0%,0.08) !important;','placeholder' => 'Enter Logout URL'),
      );

      $form['markup_role_break'] = array(
          '#markup' => '<br>',
      );

      $form['miniorange_oauth_client_attr_setup_button'] = array(
        '#type' => 'submit',
        '#id' => 'button_config_center',
        '#value' => t('Save Configuration'),
        '#disabled' => TRUE,
      );

      $form['mo_header_style_end'] = array('#markup' => '</div>');

      Utilities::show_attr_list_from_idp($form, $form_state);

      $form['miniorange_idp_guide_link_end'] = array(
          '#markup' => '</div>',
      );

      Utilities::AddSupportButton($form, $form_state);

      return $form;
  }

  public function submitForm(array &$form, \Drupal\Core\Form\FormStateInterface $form_state) {

  }

  function miniorange_oauth_client_attr_setup_submit($form, $form_state)
  {
      $email_attr = trim($form['miniorange_oauth_client_email_attr']['#value']);
      $name_attr = trim($form['miniorange_oauth_client_name_attr']['#value']);

      if (empty($email_attr)){
          \Drupal::messenger()->addMessage(t('Email attribute cannot be empty.'),'error');
          return;
      }

      \Drupal::configFactory()->getEditable('miniorange_oauth_client.settings')->set('miniorange_oauth_client_email_attr_val', $email_attr)->save();
      \Drupal::configFactory()->getEditable('miniorange_oauth_client.settings')->set('miniorange_oauth_client_name_attr_val', $name_attr)->save();
      $app_values = \Drupal::config('miniorange_oauth_client.settings')->get('miniorange_oauth_client_appval');

      $app_values['miniorange_oauth_client_email_attr'] = $email_attr;
      $app_values['miniorange_oauth_client_name_attr'] = $name_attr;
      \Drupal::configFactory()->getEditable('miniorange_oauth_client.settings')->set('miniorange_oauth_client_appval',$app_values)->save();
      \Drupal::messenger()->addMessage(t('Attribute Mapping saved successfully.'));
  }

    /**
     * Send support query.
     */
    public function saved_support(array &$form, \Drupal\Core\Form\FormStateInterface $form_state) {

        $email = trim($form['miniorange_oauth_client_email_address']['#value']);
        $phone = $form['miniorange_oauth_client_phone_number']['#value'];
        $query = trim($form['miniorange_oauth_client_support_query']['#value']);
        Utilities::send_support_query($email, $phone, $query);
    }

    function clear_attr_list(&$form,$form_state){
        \Drupal::configFactory()->getEditable('miniorange_oauth_client.settings')->clear('miniorange_oauth_client_attr_list_from_server')->save();
        Utilities::show_attr_list_from_idp($form, $form_state);
    }

    public function rfd(array &$form, \Drupal\Core\Form\FormStateInterface $form_state) {
        global $base_url;
        $response = new RedirectResponse($base_url."/admin/config/people/miniorange_oauth_client/request_for_demo");
        $response->send();
    }
}
