<?php  
/**  
 * @file  
 * Contains Drupal\ticket\Form\MessagesForm.  
 */  
namespace Drupal\ticket\Form;  
use Drupal\Core\Form\ConfigFormBase;  
use Drupal\Core\Form\FormStateInterface;  

class SimpleForm extends ConfigFormBase {  
  /**  
   * {@inheritdoc}  
   */  
  public function getFormId() {  
    return 'ticket_form';  
  }  
   /**  
   * {@inheritdoc}  
   */  
  protected function getEditableConfigNames() {  
    return [  
      'ticket.adminsettings',  
    ];  
  } 
  /**  
   * {@inheritdoc}  
   */  
  public function buildForm(array $form, FormStateInterface $form_state) {
    $node = \Drupal::routeMatch()->getParameter('node');
    //charger les paramètres d'administration  
    $config = $this->config('ticket.adminsettings');  

    //zone des éléments du formulaire 
    $form['titre'] = array(
      '#title' => t('Titre'),
      '#type' => 'textfield',
      '#size' => 70,
      '#default_value' => $config->get('titre'),
      
    );
    $form['description'] = array(
      '#title' => t('Descritpion'),
      '#type' => 'textfield',
      '#size' => 70,
      
    );
  //create a list of radio boxes that will toggle the  textbox
  //below if 'other' is selected
    $form['edition'] = [
      '#type' => 'select',
      '#title' => $this->t('Edition'),
      '#options' => [
        '1' => $this->t('15'),
        '2' => $this->t('16'),
        '3' => $this->t('17'),
        '4' => $this->t('18'),
        '5' => $this->t('19'),
    ],
     ];
     $form['periode'] = array(
      '#title' => t('Période'),
      '#type' => 'textfield',
      '#size' => 70,
      
    );
    $form['datetime'] = [
      '#title' => t('Date de disponibilité du ticket'),
      '#type' => 'date',
      '#size' => 70,
      '#date_date_format' => 'd/m/Y',
      
    ];
    $form['adresse'] = array(
      '#title' => t('Adresse'),
      '#type' => 'textfield',
      '#size' => 70,
      
    );
   
    $form['telephone'] = array(
      '#title' => t('Téléphone'),
      '#type' => 'textfield',
      '#size' => 70,
     
    );
    // $form['logo']= [
    //   '#title' => t('Logo'),
    //   '#type' => 'managed_file',
     
    //   '#upload_location' => 'public://ticket/logo'
    // ];
    $tid = 'tickettype';  //machine_name
    $terms=\Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadTree($tid);
    //$terms=\Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadTree($vid,0,NULL,TRUE); 
    foreach ($terms as $term) {
    $term_names[$term->tid] = $term->name;
    }
    //var_dump($terms);
    
    $form['ticket_type'] = array(
      '#type' => 'select',
      '#title' => $this->t('Ticket type'),
      '#options' => $term_names
    );

    return parent::buildForm($form, $form_state);  
  }   

  /**  
   * {@inheritdoc}  
   */  
  //enregistrement des données du formulaire
  public function submitForm(array &$form, FormStateInterface $form_state) {  
    parent::submitForm($form, $form_state);  
    // $logo = $form_state->getValue('logo');

    // $file = \Drupal\file\Entity\File::load($logo[0]);
    // $file->setPermanent();
    // $file->save();
    // $uri = $file->getFileUri();
    $edition = $form_state->getValue('edition');
    
   
    $this->config('ticket.adminsettings')  
      ->set('titre', $form_state->getValue('titre')) 
      ->set('description', $form_state->getValue('description')) 
      ->set('edition', $form_state->getValue('edition')) 
      ->set('datetime', $form_state->getValue('datetime')) 
      ->set('periode', $form_state->getValue('periode')) 
      ->set('adresse', $form_state->getValue('adresse')) 
      ->set('telephone', $form_state->getValue('telephone')) 
      ->set('nid', $form_state->getValue('nid')) 
      ->set('created' , time())
      // ->set('logo' , $uri)
      ->save(); 
     
      
      $this->messenger()->addStatus(t('Informations enregistrées'));
  } 
  
  public function validateForm(array &$form, FormStateInterface $form_state) {
    parent::validateForm($form, $form_state); 
  }
  

}  