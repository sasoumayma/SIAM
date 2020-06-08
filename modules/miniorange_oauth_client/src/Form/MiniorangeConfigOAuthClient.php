<?php

/**
 * @file
 * Contains \Drupal\miniorange_oauth_client\Form\MiniorangeConfigOAuthClient.
 */

namespace Drupal\miniorange_oauth_client\Form;
use Drupal\Core\Form\FormBase;
use Drupal\miniorange_oauth_client\Controller\miniorange_oauth_clientController;
use Drupal\miniorange_oauth_client\Utilities;
use Drupal\miniorange_oauth_client\mo_saml_visualTour;
use Symfony\Component\HttpFoundation\RedirectResponse;

class MiniorangeConfigOAuthClient extends FormBase
{
    public function getFormId() {
       return 'miniorange_oauth_client_settings';
    }

    public function buildForm(array $form, \Drupal\Core\Form\FormStateInterface $form_state)
    {
        global $base_url;
        \Drupal::configFactory()->getEditable('miniorange_oauth_client.settings')->set('miniorange_oauth_client_disabled', FALSE)->save();
        $moTour = mo_saml_visualTour::genArray();
        $form['tourArray'] = array(
            '#type' => 'hidden',
            '#value' => $moTour,
        );

        $baseUrl = \Drupal::config('miniorange_oauth_client.settings')->get('miniorange_oauth_client_base_url');
        $baseUrlValue = empty($baseUrl) ? Utilities::getOAuthBaseURL($base_url)  : $baseUrl ;

        $login_path = '<a href='.$baseUrlValue.'/moLogin>Enter what you want to display on the link</a>';
        $module_path = \Drupal::service('extension.list.module')->getPath('miniorange_oauth_client');

        $miniorange_auth_client_callback_uri = $baseUrlValue."/mo_login";
        \Drupal::configFactory()->getEditable('miniorange_oauth_client.settings')->set('miniorange_auth_client_callback_uri',$miniorange_auth_client_callback_uri)->save();

        $attachments['#attached']['library'][] = 'miniorange_oauth_client/miniorange_oauth_client.admin';
        $form['markup_library'] = array(
            '#attached' => array(
               'library' => array(
                    "miniorange_oauth_client/miniorange_oauth_client.oauth_config",
                    "miniorange_oauth_client/miniorange_oauth_client.admin",
                    "miniorange_oauth_client/miniorange_oauth_client.testconfig",
                    "miniorange_oauth_client/miniorange_oauth_client.returnAttribute",
                    "miniorange_oauth_client/miniorange_oauth_client.style_settings",
                    "miniorange_oauth_client/miniorange_oauth_client.Vtour",
               )
            ),
        );

        $app_name = \Drupal::config('miniorange_oauth_client.settings')->get('miniorange_auth_client_app_name');
        $client_id = \Drupal::config('miniorange_oauth_client.settings')->get('miniorange_auth_client_client_id');
        if(!empty($app_name) && !empty($client_id)){
            $disabled = TRUE;
        }

        $disableButton = NULL;
        if( empty($app_name)  || empty($client_id) ){
            $disableButton = 'disabled';
        }

        $app_name_selected = \Drupal::config('miniorange_oauth_client.settings')->get('miniorange_auth_client_app_name');
        $client_id = \Drupal::config('miniorange_oauth_client.settings')->get('miniorange_auth_client_client_id');
        if(!empty($app_name_selected) && !empty($client_id)){
            $disabled = TRUE;
            $attributes_arr =  array('style' => 'width:73%;background-color: hsla(0,0%,0%,0.08) !important;');
        }
        else{
            $disabled = FALSE;
            $attributes_arr =  array('style' => 'width:73%;');
        }

        $form['mo_oauth_top_div'] = array('#markup' => '<div class="mo_oauth_table_layout_1">');

        $form['mo_oauth_inside_div'] = array(
          '#markup' => '<div class="mo_oauth_table_layout mo_oauth_container">',
        );

        $form['markup_top_vt_start'] = array(
            '#markup' => '<div id="tabhead"><h3>CONFIGURE OAUTH APPLICATION &nbsp;&nbsp; 
            <a id="showMetaButton" class="mo_oauth_btn mo_oauth_btn-primary mo_btn-sm" onclick="testConfig()" '.$disableButton.'>Backup/Import</a>&nbsp;</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            <a id="Restart_moTour" class="mo_oauth_btn mo_oauth_btn-primary-color mo_oauth_btn-large" onclick="Restart_moTour()">Take a Tour</a></h3><hr><br></div>',
        );

        $prefixname = '<div class="mo_oauth_row"><div class="mo_oauth_name">';
        $suffixname = '</div>';

        $prefixvalue = '<div class="mo_oauth_value">';
        $suffixvalue = '</div></div>';
        $module_path = \Drupal::service('extension.list.module')->getPath('miniorange_oauth_client');
    
        $form['markup_top_1'] = array (
            '#markup' => '
                <div border="1" id="backup_import_form" class="mo_oauth_backup_download">
				<h3>Backup/ Import Configurations</h3><hr><span class="mo_oauth_backup_cancel">
				<a id="hideMetaButton" class="mo_oauth_btn mo_oauth_btn-sm mo_oauth_btn-danger" onclick = "testConfig()">Cancel</a></span>',
        );

        $form['markup_1'] = array(
            '#markup' => '<br><br><div class="mo_oauth_highlight_background_note"><p><b>NOTE: </b>This tab will help you to transfer your module configurations when you change your Drupal instance. 
                          <br>Example: When you switch from test environment to production.<br>Follow these 3 simple steps to do that:<br>
                          <br><strong>1.</strong> Download module configuration file by clicking on the Download Configuration button given below.
                          <br><strong>2.</strong> Install the module on new Drupal instance.<br><strong>3.</strong> Upload the configuration file in Import module Configurations section.<br>
                          <br><b>And just like that, all your module configurations will be transferred!</b></p></div><br><div id="Exort_Configuration"><h3>Backup/ Export Configuration &nbsp;&nbsp;</h3><hr/><p>
                          Click on the button below to download module configuration.</p>',
        );
  
        $form['miniorange_saml_imo_option_exists_export'] = array(
            '#type' => 'submit',
            '#value' => t('Download Module Configuration'),
            '#submit' => array('::miniorange_import_export'),
            '#suffix'=> '<br/><br/></div>',
        );
  
        $form['markup_prem_plan'] = array(
            '#markup' => '<div id="Import_Configuration"><br/><h3>Import Configuration</h3><hr><br>
                          <div class="mo_oauth_highlight_background_note_1"><b>Note: </b>Available in 
                          <a href="' . $base_url . '/admin/config/people/miniorange_oauth_client/licensing">Standard, Premium and Enterprise</a> versions of the module</div>',
        );
  
        $form['markup_import_note'] = array(
            '#markup' => '<p>This tab will help you to<span style="font-weight: bold"> Import your module configurations</span> when you change your Drupal instance.</p>
                 <p>choose <b>"json"</b> Extened module configuration file and upload by clicking on the button given below. </p>',
        );
  
        $form['import_Config_file'] = array(
            '#type' => 'file',
            '#disabled' => TRUE,
        );
  
        $form['miniorange_saml_idp_import'] = array(
            '#type' => 'submit',
            '#value' => t('Upload'),
            '#disabled' => TRUE,
            '#suffix' => '<br><br></div></div><div id="clientdata">'
        );

        $form['miniorange_oauth_client_app_options'] = array(
            '#type' => 'value',
            '#id' => 'miniorange_oauth_client_app_options',
            '#value' => array(
                'Azure AD' => t('Azure AD'),
                'Keycloak' => t('Keycloak'),
                'Salesforce' => t('Salesforce'),
                'Google' => t('Google'),
                'GitHub' => t('GitHub'),
                'Facebook' => t('Facebook'),
                'Box' => t('Box'),
                'Slack' => t('Slack'),
                'Discord' => t('Discord'),
                'Line' => t('Line'),
                'Wild Apricot' => t('Wild Apricot'),
                'Zendesk' => t('Zendesk'),
                'LinkedIn' => t('LinkedIn'),
                'Strava' => t('Strava'),
                'FitBit' => t('FitBit'),
                'Custom' => t('Custom OAuth 2.0 Provider'),
                'Azure AD B2C' => t('Azure AD B2C (Premium and Enterprise)'),
                'AWS Cognito' => t('AWS Cognito (Premium and Enterprise)'),
                'Onelogin' => t('Onelogin (Premium and Enterprise)'),
                'miniOrange' => t('miniOrange (Premium and Enterprise)'),
                'Custom_openid' => t('Custom OpenID Provider (We support OpenID protocol in Premium and Enterprise version)')),
        );

        $form['miniorange_oauth_client_app_title'] = array(
            '#markup' => '<b>Select Application:</b> <div class="mo_oauth_tooltip"><img src="'.$base_url.'/'. $module_path . '/includes/images/info.png" alt="info icon" height="15px" width="15px"></div><div class="mo_oauth_tooltiptext">Select an OAuth Server</div>',
            '#prefix' => $prefixname,
            '#suffix' => $suffixname,
        );

        $form['miniorange_oauth_client_app'] = array(
            '#id' => 'miniorange_oauth_client_app',
            '#type' => 'select',
            '#disabled' => $disabled,
            '#options' => $form['miniorange_oauth_client_app_options']['#value'],
            '#required' => TRUE,
            '#attributes' => $attributes_arr,
            '#default_value' => \Drupal::config('miniorange_oauth_client.settings')->get('miniorange_oauth_client_app'),
            '#prefix' => $prefixvalue,
            '#suffix' => $suffixvalue,
        );

        $form['mo_vt_id_start'] = array(
            '#markup' => '<div id = "mo_vt_callback_url">',
        );

        $form['miniorange_oauth_callback_title'] = array(
            '#markup' => '<b>Callback/Redirect URL:</b> <div class="mo_oauth_tooltip"><img src="'.$base_url.'/'. $module_path . '/includes/images/info.png" alt="info icon" height="15px" width="15px"></div><div class="mo_oauth_tooltiptext"><b>Note:</b> If you want to change the <b>Redirect URL</b>, you can provide the site root URL/ base URL in <b>Sign In Settings</b> tab.</div>',
            '#prefix' => $prefixname,
            '#suffix' => $suffixname,
        );

        $form['miniorange_oauth_callback'] = array(
            '#type' => 'textfield',
            '#id'  => 'callbackurl',
            '#default_value' => $baseUrlValue."/mo_login",
            '#disabled' => true,
            '#prefix' => $prefixvalue,
            '#suffix' => $suffixvalue,
            '#attributes' => array('style' => 'width:73%;background-color: hsla(0,0%,0%,0.08) !important;'),
        );

        $form['mo_vt_id_end'] = array(
            '#markup' => '</div>',
        );

        $form['mo_vt_id_data'] = array(
            '#markup' => '<div id = "mo_vt_add_data">',
        );

        $form['miniorange_oauth_app_name_title'] = array(
            '#markup' => '<b>Custom App Name:</b>',
            '#prefix' => $prefixname,
            '#suffix' => $suffixname,
        );

        $form['miniorange_oauth_app_name'] = array(
            '#type' => 'textfield',
            '#default_value' => \Drupal::config('miniorange_oauth_client.settings')->get('miniorange_auth_client_app_name'),
            '#id'  => 'miniorange_oauth_client_app_name',
            '#disabled' => $disabled,
            '#required' => TRUE,
            '#attributes' => $attributes_arr,
            '#prefix' => $prefixvalue,
            '#suffix' => $suffixvalue,
        );

        $form['miniorange_oauth_client_display_name_title'] = array(
            '#markup' => '<b>Login link on the login page:</b> <div class="mo_oauth_tooltip"><img src="'.$base_url.'/'. $module_path . '/includes/images/info.png" alt="info icon" height="15px" width="15px"></div><div class="mo_oauth_tooltiptext"><b>Note:</b> The login link will appear on the user login page in this manner</div>',
            '#prefix' => $prefixname,
            '#suffix' => $suffixname,
        );

        $form['miniorange_oauth_client_display_name'] = array(
            '#type' => 'textfield',
            '#id'  => 'miniorange_oauth_client_display_name',
            '#default_value' => \Drupal::config('miniorange_oauth_client.settings')->get('miniorange_auth_client_display_name'),
            '#attributes' => array('style' => 'width:73%','placeholder' => 'Login using ##app_name##'),
            '#prefix' => $prefixvalue,
            '#suffix' => $suffixvalue,
        );

        $form['miniorange_oauth_client_id_title'] = array(
            '#markup' => '<b>Client ID: </b> <div class="mo_oauth_tooltip"><img src="'.$base_url.'/'. $module_path . '/includes/images/info.png" alt="info icon" height="15px" width="15px"></div><div class="mo_oauth_tooltiptext">You will get this value from your OAuth Server</div>',
            '#prefix' => $prefixname,
            '#suffix' => $suffixname,
        );

        $form['miniorange_oauth_client_id'] = array(
            '#type' => 'textfield',
            '#id'  => 'miniorange_oauth_client_client_id',
            '#default_value' => \Drupal::config('miniorange_oauth_client.settings')->get('miniorange_auth_client_client_id'),
            '#attributes' => array('style' => 'width:73%'),
            '#required' => TRUE,
            '#prefix' => $prefixvalue,
            '#suffix' => $suffixvalue,
        );

        $form['miniorange_oauth_client_secret_title'] = array(
            '#markup' => '<b>Client Secret: </b> <div class="mo_oauth_tooltip"><img src="'.$base_url.'/'. $module_path . '/includes/images/info.png" alt="info icon" height="15px" width="15px"></div><div class="mo_oauth_tooltiptext">You will get this value from your OAuth Server</div>',
            '#prefix' => $prefixname,
            '#suffix' => $suffixname,
        );

        $form['miniorange_oauth_client_secret'] = array(
            '#type' => 'textfield',
            '#default_value' => \Drupal::config('miniorange_oauth_client.settings')->get('miniorange_auth_client_client_secret'),
            '#id'  => 'miniorange_oauth_client_client_secret',
            '#attributes' => array('style' => 'width:73%'),
            '#required' => TRUE,
            '#prefix' => $prefixvalue,
            '#suffix' => $suffixvalue,
        );

        $form['mo_vt_id_data1'] = array(
            '#markup' => '</div>',
        );

        $form['mo_vt_id_data2'] = array(
            '#markup' => '<div id = "mo_vt_add_data2">',
        );

        $form['miniorange_oauth_client_scope_title'] = array(
            '#markup' => '<b>Scope: </b> <div class="mo_oauth_tooltip"><img src="'.$base_url.'/'. $module_path . '/includes/images/info.png" alt="info icon" height="15px" width="15px"></div><div class="mo_oauth_tooltiptext">Scope decides the range of data that you will be getting from your OAuth Provider</div>',
            '#prefix' => $prefixname,
            '#suffix' => $suffixname,
        );

        $form['miniorange_oauth_client_scope'] = array(
            '#type' => 'textfield',
            '#id'  => 'miniorange_oauth_client_scope',
            '#default_value' => \Drupal::config('miniorange_oauth_client.settings')->get('miniorange_auth_client_scope'),
            '#attributes' => array('style' => 'width:73%'),
            '#required' => TRUE,
            '#prefix' => $prefixvalue,
            '#suffix' => $suffixvalue,
        );

        $form['mo_vt_id_data4'] = array(
            '#markup' => '</div>',
        );

        $form['mo_vt_id_data5'] = array(
            '#markup' => '<div id = "mo_vt_add_data4">',
        );

        $form['miniorange_oauth_client_authorize_endpoint_title'] = array(
            '#markup' => '<b>Authorize Endpoint: </b>',
            '#prefix' => $prefixname,
            '#suffix' => $suffixname,
        );

        $form['miniorange_oauth_client_authorize_endpoint'] = array(
            '#type' => 'textfield',
            '#default_value' => \Drupal::config('miniorange_oauth_client.settings')->get('miniorange_auth_client_authorize_endpoint'),
            '#id'  => 'miniorange_oauth_client_auth_ep',
            '#attributes' => array('style' => 'width:73%'),
            '#required' => TRUE,
            '#prefix' => $prefixvalue,
            '#suffix' => $suffixvalue,
        );

        $form['miniorange_oauth_client_access_token_endpoint_title'] = array(
            '#markup' => '<b>Access Token Endpoint:</b> ',
            '#prefix' => $prefixname,
            '#suffix' => $suffixname,
        );

        $form['miniorange_oauth_client_access_token_endpoint'] = array(
            '#type' => 'textfield',
            '#default_value' => \Drupal::config('miniorange_oauth_client.settings')->get('miniorange_auth_client_access_token_ep'),
            '#id'  => 'miniorange_oauth_client_access_token_ep',
            '#attributes' => array('style' => 'width:73%'),
            '#required' => TRUE,
            '#prefix' => $prefixvalue,
            '#suffix' => $suffixvalue,
        );

        $form['miniorange_oauth_client_userinfo_endpoint_title'] = array(
            '#markup' => '<b>Get User Info Endpoint: </b>',
            '#prefix' => $prefixname,
            '#suffix' => $suffixname,
        );

        $form['miniorange_oauth_client_userinfo_endpoint'] = array(
            '#type' => 'textfield',
            '#default_value' => \Drupal::config('miniorange_oauth_client.settings')->get('miniorange_auth_client_user_info_ep'),
            '#id'  => 'miniorange_oauth_client_user_info_ep',
            '#attributes' => array('style' => 'width:73%'),
            '#required' => TRUE,
            '#prefix' => $prefixvalue,
            '#suffix' => $suffixvalue,
        );

        $form['client_credentials'] = array(
            '#markup' => '<b>Send Client ID and secret in:</b> <div class="mo_oauth_tooltip"><img src="'.$base_url.'/'. $module_path . '/includes/images/info.png" alt="info icon" height="15px" width="15px"></div><div class="mo_oauth_tooltiptext"><b>Note:</b> This option depends upon the OAuth provider. In case you are unaware about what to save, keeping this default is the best practice.</div>',
            '#prefix' => $prefixname,
            '#suffix' => $suffixname,
        );

        $form['background_1'] = array(
            '#markup' => "<div class='mo_oauth_highlight_background_note_2'>",
            '#prefix' => $prefixvalue,
        );

        $form['miniorange_oauth_send_with_header_oauth'] = array(
            '#type' => 'checkbox',
            '#default_value' => \Drupal::config('miniorange_oauth_client.settings')->get('miniorange_oauth_send_with_header_oauth'),
            '#title' => t('<b>Header</b>'),
        );

        $form['miniorange_oauth_send_with_body_oauth'] = array(
            '#type' => 'checkbox',
            '#default_value' => \Drupal::config('miniorange_oauth_client.settings')->get('miniorange_oauth_send_with_body_oauth'),
            '#title' => t('<b>Body</b>'),

        );

        $form['background_1_end'] = array(
            '#markup' => '</div>',
            '#suffix' => $suffixvalue,
        );

        $form['mo_btn_breaks'] = array(
            '#markup' => "</div><br>",
        );

        $form['mo_vt_id_data3'] = array(
            '#markup' => '<div id = "mo_vt_add_data3">',
        );

        $form['miniorange_oauth_enable_login_with_oauth_title'] = array(
            '#markup' => '<b>Enable Login with OAuth:</b>',
            '#prefix' => $prefixname,
            '#suffix' => $suffixname,
        );

        $form['miniorange_oauth_enable_login_with_oauth'] = array(
            '#type' => 'checkbox',
            '#default_value' => \Drupal::config('miniorange_oauth_client.settings')->get('miniorange_oauth_enable_login_with_oauth'),
            '#prefix' => $prefixvalue,
            '#suffix' => $suffixvalue,
        );

        $form['mo_btn_breaks2'] = array(
            '#markup' => "</div><br>",
        );

        $form['miniorange_oauth_client_config_submit'] = array(
            '#type' => 'submit',
            '#value' => t('Save Configuration'),
            '#id' => 'button_config',
        );

        $baseUrlValue = Utilities::getOAuthBaseURL($base_url);

        $form['miniorange_oauth_client_test_config_button'] = array(
            '#value' => t('Test'),
            '#markup' => '<span id="base_Url" name="base_Url" data="'. $baseUrlValue.'"></span>
                                <a id="testConfigButton" class="mo_oauth_btn mo_oauth_btn-primary-color mo_oauth_btn-large mo_oauth_btn_fix">Test Configuration</a>',
        );

        $form['mo_reset'] = array(
            '#markup' => "<a class='mo_oauth_btn mo_oauth_btn-primary mo_oauth_btn-large' id ='vt_reset_config' href='".$baseUrlValue."/resetConfig'>Reset Configuration</a>",
        );

        $form['miniorange_oauth_login_link'] = array(
            '#id'  => 'miniorange_oauth_login_link',
            '#markup' => "<br><br><br><br><div class='mo_oauth_instruction_style'>
                <br><strong><div class='mo_custom_font_size_1'>Instructions to add login link to different pages in your Drupal site: </div></strong><br>
                <div class='mo_custom_font_size_2'>After completing your configurations, by default you will see a login link on your drupal site's login page.
                However, if you want to add login link somewhere else, please follow the below given steps:</div>
                <div class='mo_custom_font_size_3'>
                <li>Go to <b>Structure</b> -> <b>Blocks</b></li>
                <li> Click on <b>Add block</b></li>
                <li>Enter <b>Block Title</b> and the <b>Block description</b></li>
                <li>Under the <b>Block body</b> add the following URL to add a login link:
                    <ol> <h3><b>&lt;a href= '".$baseUrlValue."/moLogin'> Click here to Login&lt;/a&gt;</b></h3></ol>
                </li>
                <li>From the text filtered dropdown select either <b>Filtered HTML</b> or <b>Full HTML</b></li>
                <li>From the division under <b>REGION SETTINGS</b> select where do you want to show the login link</li>
                <li>Click on the <b>SAVE block</b> button to save your settings</li><br>
                </div>
                </div>",
            '#attributes' => array(),
        );

        $form['mo_header_style_end'] = array('#markup' => '</div></div>');


        Utilities::spConfigGuide($form, $form_state);
        $form['mo_markup_div_2_imp']=array('#markup'=>'</div>');
        $form['mo_markup_div__imp']=array('#markup'=>'<div id="mo_advertise">');
        Utilities::advertiseServer($form, $form_state);
        $form['mo_markup_div_imp']=array('#markup'=>'</div>');
        Utilities::AddSupportButton($form, $form_state);

        return $form;
    }

    public function submitForm(array &$form, \Drupal\Core\Form\FormStateInterface $form_state) {
        global $base_url;
        $baseUrlValue = Utilities::getOAuthBaseURL($base_url);

        if(isset($form['miniorange_oauth_client_app']))
            $client_app =  $form['miniorange_oauth_client_app']['#value'];
        if(isset($form['miniorange_oauth_app_name']['#value']))
            $app_name = $form['miniorange_oauth_app_name']['#value'];
        $app_name = str_replace(' ', '', $app_name);
        if(isset($form['miniorange_oauth_client_display_name']['#value']))
            $display_name = $form['miniorange_oauth_client_display_name'] ['#value'];
        if(isset($form['miniorange_oauth_client_id']))
            $client_id = $form['miniorange_oauth_client_id']['#value'];
        if(isset($form['miniorange_oauth_client_secret']['#value']))
            $client_secret = $form['miniorange_oauth_client_secret'] ['#value'];
        if(isset($form['miniorange_oauth_client_scope']['#value']))
            $scope = $form['miniorange_oauth_client_scope']['#value'];
        if(isset($form['miniorange_oauth_client_authorize_endpoint']['#value']))
            $authorize_endpoint = trim($form['miniorange_oauth_client_authorize_endpoint'] ['#value']);
        if(isset($form['miniorange_oauth_client_access_token_endpoint']['#value']))
            $access_token_ep = trim($form['miniorange_oauth_client_access_token_endpoint']['#value']);
        if(isset($form['miniorange_oauth_client_userinfo_endpoint']['#value']))
            $user_info_ep = trim($form['miniorange_oauth_client_userinfo_endpoint']['#value']);
        $email_attr = \Drupal::config('miniorange_oauth_client.settings')->get('miniorange_oauth_client_email_attr_val');
        if(($client_app=='Select') || empty($client_app) || empty($app_name) || empty($client_id) || empty($client_secret) || empty($authorize_endpoint) || empty($access_token_ep)
            || empty($user_info_ep)) {
            if(empty($client_app)|| $client_app == 'Select'){
                \Drupal::messenger()->addMessage(t('The <b>Select Application</b> dropdown is required. Please Select your application.'), 'error');
                return;
            }
            \Drupal::messenger()->addMessage(t('The <b>Custom App name</b>, <b>Client ID</b>, <b>Client Secret</b>, <b>Authorize Endpoint</b>, <b>Access Token Endpoint</b>
                , <b>Get User Info Endpoint</b> fields are required.'), 'error');
            return;
        }

        if(empty($client_app))
        {
            $client_app =\Drupal::config('miniorange_oauth_client.settings')->get('miniorange_oauth_client_app');
        }
        if(empty($app_name))
        {
            $client_app = \Drupal::config('miniorange_oauth_client.settings')->get('miniorange_auth_client_app_name');
        }
        if (empty($display_name))
        {
            $display_name = '';
        }
        if(empty($client_id))
        {
            $client_id =\Drupal::config('miniorange_oauth_client.settings')->get('miniorange_auth_client_client_id');
        }
        if(empty($client_secret))
        {
            $client_secret = \Drupal::config('miniorange_oauth_client.settings')->get('miniorange_auth_client_client_secret');
        }
        if(empty($scope))
        {
            $scope = \Drupal::config('miniorange_oauth_client.settings')->get('miniorange_auth_client_scope');
        }
        if(empty($authorize_endpoint))
        {
            $authorize_endpoint = \Drupal::config('miniorange_oauth_client.settings')->get('miniorange_auth_client_authorize_endpoint');
        }
        if(empty($access_token_ep))
        {
            $access_token_ep =\Drupal::config('miniorange_oauth_client.settings')->get('miniorange_auth_client_access_token_ep');
        }
        if(empty($user_info_ep))
        {
            $user_info_ep = \Drupal::config('miniorange_oauth_client.settings')->get('miniorange_oauth_client_userinfo_endpoint');
        }

        $callback_uri = $baseUrlValue."/mo_login";

        $app_values = \Drupal::config('miniorange_oauth_client.settings')->get('miniorange_oauth_client_appval');
        if(!is_array($app_values))
            $app_values = array();
        $app_values['client_id'] = $client_id;
        $app_values['client_secret'] = $client_secret;
        $app_values['app_name'] = $app_name;
        $app_values['display_name'] = $display_name;
        $app_values['scope'] = $scope;
        $app_values['authorize_endpoint'] = $authorize_endpoint;
        $app_values['access_token_ep'] = $access_token_ep;
        $app_values['user_info_ep'] = $user_info_ep;
        $app_values['callback_uri'] = $callback_uri;
        $app_values['client_app'] = $client_app;
        $app_values['miniorange_oauth_client_email_attr'] = $email_attr;
        $enable_login_with_oauth = $form['miniorange_oauth_enable_login_with_oauth']['#value'];
        $enable_login = $enable_login_with_oauth == 1 ? TRUE : FALSE;
        $enable_with_header = $form['miniorange_oauth_send_with_header_oauth']['#value'];
        $enable_with_body = $form['miniorange_oauth_send_with_body_oauth']['#value'];
        $enable_header = $enable_with_header == 1 ? TRUE : FALSE ;
        $enable_body = $enable_with_body == 1 ? TRUE : FALSE ;

        \Drupal::configFactory()->getEditable('miniorange_oauth_client.settings')->set('miniorange_oauth_enable_login_with_oauth',$enable_login)->save();
        \Drupal::configFactory()->getEditable('miniorange_oauth_client.settings')->set('miniorange_oauth_client_app',$client_app)->save();
        \Drupal::configFactory()->getEditable('miniorange_oauth_client.settings')->set('miniorange_oauth_client_appval',$app_values)->save();
        \Drupal::configFactory()->getEditable('miniorange_oauth_client.settings')->set('miniorange_auth_client_app_name',$app_name)->save();
        \Drupal::configFactory()->getEditable('miniorange_oauth_client.settings')->set('miniorange_auth_client_display_name',$display_name)->save();
        \Drupal::configFactory()->getEditable('miniorange_oauth_client.settings')->set('miniorange_auth_client_client_id',$client_id)->save();
        \Drupal::configFactory()->getEditable('miniorange_oauth_client.settings')->set('miniorange_auth_client_client_secret',$client_secret)->save();
        \Drupal::configFactory()->getEditable('miniorange_oauth_client.settings')->set('miniorange_auth_client_scope',$scope)->save();
        \Drupal::configFactory()->getEditable('miniorange_oauth_client.settings')->set('miniorange_auth_client_authorize_endpoint',$authorize_endpoint)->save();         \Drupal::configFactory()->getEditable('miniorange_oauth_client.settings')->set('miniorange_auth_client_access_token_ep',$access_token_ep)->save();
        \Drupal::configFactory()->getEditable('miniorange_oauth_client.settings')->set('miniorange_auth_client_user_info_ep',$user_info_ep)->save();
        \Drupal::configFactory()->getEditable('miniorange_oauth_client.settings')->set('miniorange_auth_client_stat',"Review Config")->save();
        \Drupal::configFactory()->getEditable('miniorange_oauth_client.settings')->set('miniorange_auth_client_callback_uri',$callback_uri)->save();
        \Drupal::configFactory()->getEditable('miniorange_oauth_client.settings')->set('miniorange_oauth_send_with_header_oauth',$enable_header)->save();
      	\Drupal::configFactory()->getEditable('miniorange_oauth_client.settings')->set('miniorange_oauth_send_with_body_oauth',$enable_body)->save();

      $check_email = \Drupal::config('system.site')->get('mail');
      $c_time = date('m/d/Y H:i:s', time());

      $result = \Drupal::database()->select('miniornage_oauth_client_customer', 'n')
        ->fields('n',['cd_plugin'])->execute()->fetchAll();

      if (!isset($result[0]->cd_plugin) || empty($result[0]->cd_plugin)) {

        $db = \Drupal::database();
        $db->insert('miniornage_oauth_client_customer')
          ->fields([
            'id' => 1,
            'cd_plugin' => $c_time,
            'dno_ssos' => 0,
            'tno_ssos' => 0,
            'previous_update' => '',
          ])
          ->execute();
      }
      else{
        $c_time = $result[0]->cd_plugin;
      }

      $dno_ssos = 0;
      $tno_ssos = 0;
      $previous_update = '';
      $present_update = '';

      miniorange_oauth_clientController::plugin_rr($check_email,$app_name,$baseUrlValue, $c_time, $dno_ssos, $tno_ssos, $previous_update, $present_update);

      \Drupal::messenger()->addMessage(t('Configurations saved successfully.'));
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

    public function rfd(array &$form, \Drupal\Core\Form\FormStateInterface $form_state) {

        global $base_url;
        $response = new RedirectResponse($base_url."/admin/config/people/miniorange_oauth_client/request_for_demo");
        $response->send();
    }

    function miniorange_import_export() 
	{
        $tab_class_name = array(
            'OAuth Client Configuration' => 'mo_options_enum_client_configuration',
            'Attribute Mapping' => 'mo_options_enum_attribute_mapping',
            'Sign In Settings' => 'mo_options_enum_signin_settings'
        );

		$configuration_array = array();
		foreach($tab_class_name as $key => $value) {
			$configuration_array[$key] = self::mo_get_configuration_array($value);
		}

		$configuration_array["Version_dependencies"] = self::mo_get_version_informations();
		header("Content-Disposition: attachment; filename = miniorange_oauth_client_config.json");
		echo(json_encode($configuration_array, JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES));
		exit;
	}

    function mo_get_configuration_array($class_name)
    {
        $class_object = Utilities::getVariableArray($class_name);
        $mo_array = array();
        foreach($class_object as $key => $value) {
            $mo_option_exists = \Drupal::config('miniorange_oauth_client.settings')->get($value);
            if($mo_option_exists) {
                $mo_array[$key] = $mo_option_exists;
            }
        }
        return $mo_array;
    }

    function mo_get_version_informations() {
        $array_version = array();
        $array_version["PHP_version"] = phpversion();
        $array_version["Drupal_version"] = \DRUPAL::VERSION;
        $array_version["OPEN_SSL"] = self::mo_oauth_is_openssl_installed();
        $array_version["CURL"] = self::mo_oauth_is_curl_installed();
        $array_version["ICONV"] = self::mo_oauth_is_iconv_installed();
        $array_version["DOM"] = self::mo_oauth_is_dom_installed();
        return $array_version;
    }

	function mo_oauth_is_openssl_installed() {
		if ( in_array( 'openssl', get_loaded_extensions() ) ) {
			return 1;
		} else {
			return 0;
		}
	}
    function mo_oauth_is_curl_installed() {
        if ( in_array( 'curl', get_loaded_extensions() ) ) {
            return 1;
        } else {
            return 0;
        }
    }
    function mo_oauth_is_iconv_installed() {
        if ( in_array( 'iconv', get_loaded_extensions() ) ) {
            return 1;
        } else {
            return 0;
        }
    }
    function mo_oauth_is_dom_installed() {
        if ( in_array( 'dom', get_loaded_extensions() ) ) {
            return 1;
        } else {
            return 0;
        }
    }
}