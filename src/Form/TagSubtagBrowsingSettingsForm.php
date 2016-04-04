<?php

/**
 * @file
 * Contains Drupal\tag_subtag_browsing\SettingsForm\TagSubtagBrowsingSettingsForm.
 */

namespace Drupal\tag_subtag_browsing\Form;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Form\ConfigFormBase;
 
/**
 * TagSubtagBrowsingSettingsForm form.
 * 
 * This configuration form allows the user to choose which taxonomy is used in 
 * Drupal\tag_subtag_browsing\Form\TagSubtagBrowsingForm.
 */ 
class TagSubtagBrowsingSettingsForm extends ConfigFormBase { 

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'tag_subtag_browsing_config_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $saved_taxonomy = $this->config('tag_subtag_browsing.settings')->get('browsing_tag');

    //Taxonomies are returned in alpha order, so no need to sort them.
    $vocabularies = \Drupal\taxonomy\Entity\Vocabulary::loadMultiple();

    foreach($vocabularies as $key => $vocabulary) {
      $taxonomies[$key] = $key;    
    }
    
    $form['browsing_tag'] = array(
      '#type' => 'select',
      '#title' => $this->t('Select taxonomy'),
      '#options' => $taxonomies,
      '#default_value' => $saved_taxonomy,
    );

    return parent::buildForm($form, $form_state);
  }

  public function getEditableConfigNames() {
    return array('browsing_tag');
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {

    //We have to use Drupal\Core\Config\ConfigFactoryInterface::getEditable() 
    //in order to be able modify the value saved in the service container.
    $container = \Drupal::getContainer();

    $container->get('config.factory')
        ->getEditable('tag_subtag_browsing.settings')
        ->set('browsing_tag', $form_state->getValue('browsing_tag'))
        ->save();

    parent::submitForm($form, $form_state);
  }

}
