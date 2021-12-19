<?php
/**
 * @file
 * Contains Drupal\tag_subtag_browsing\Form\TagSubtagBrowsingForm.
 */

namespace Drupal\tag_subtag_browsing\Form;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * TagSubtagBrowsingForm form.
 *
 * This form will appear in
 * Drupal\tag_subtag_browsing\Plugin\Block\TagSubtagBrowsingBlock
 */
class TagSubtagBrowsingForm extends FormBase {
  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'tag_subtag_browsing_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {

    list($parent_tags, $child_tags) = $this->getTags();

    $form['tag_subtag_browsing_container'] = array(
      '#type' => 'container',
    );

    //Options for top-level categories
    $parent_tags  = array('0' => 'Select Category') + $parent_tags;

    $form['tag_subtag_browsing_container']['tag_select'] = array(
      '#type' => 'select',
      '#options' => $parent_tags,
    );

    //Sets Ajax callback for top-level categories only if $child_tags are
    //available to populate the child-level drop-down box
    if(!empty($child_tags)) {
      $form['tag_subtag_browsing_container']['tag_select']['#ajax'] = array(
        'callback' => array($this, 'showSubTagsAjax'),
        'wrapper' => 'child_container',
        'method' => 'replace',
        'effect' => 'fade',
        'event' => 'change',
      );
    }

    if(!empty($child_tags)) {
      //On page load, the subcategory selection array needs to contain all the
      //child tags, so when the user selects a top-level category, the child
      //categories that then populate the subcategory selection box will not be
      //considered illegal.
      $child_tags  = array('0' => 'Select Subcategory') + $child_tags;

      $form['tag_subtag_browsing_container']['subtag_select'] = array(
        '#type' => 'select',
        '#options' => $child_tags,
        '#prefix' => '<div id = "child_container">',
        '#suffix' => '</div>',
      );
    }

    $form['actions']['submit'] = array(
      '#type' => 'submit',
      '#value' => $this->t('View Category'),
      '#button_type' => 'primary',
    );

    return $form;
  }

  /**
   * Ajax callback for TagSubtagBrowsingForm::buildForm.
   *
   * Creates new set of options for subtag_select box from top-level tag that
   * user selected.
   *
   */
  public function showSubTagsAjax(array &$form, FormStateInterface $form_state) {

    //Gets user-selected value for top-level category from the $form_state
    //object.
    $parent_tag = $form_state->getValue('tag_select');

    list($parent_tags, $child_tags) = $this->getTags();

    if($parent_tag == 0) {
      //If the user choose "Select Category," then show all subcategories.
      $subcat_options = $child_tags;
    }
    elseif(is_array($child_tags)) {
      //Otherwise, if there are child tags for this category, show those child
      //tags.
      $subcat_options = $child_tags[$parent_tags[$parent_tag]];
    }
    else {
      //If this top-level category does not contain child tags, hide the child
      //tag selection box.
      $subcat_options = NULL;
    }

    if(isset($subcat_options)) {
      $subcat_options = array('0' => 'Select Subcategory') + $subcat_options;

      $form['tag_subtag_browsing_container']['subtag_select'] = array(
        '#id' => 'edit-subtag-select',
        '#name' => 'subtag_select',
        '#type' => 'select',
        '#options' => $subcat_options,
        '#prefix' => '<div id = "child_container">',
        '#suffix' => '</div>',
      );
    }
    else {
      $form['tag_subtag_browsing_container']['subtag_select'] = array(
        '#markup' => 'This category does not contain subcategories.',
        '#prefix' => '<div id = "child_container">',
        '#suffix' => '</div>',
      );
    }

    return $form['tag_subtag_browsing_container']['subtag_select'];
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $parent_tag = $form_state->getValue('tag_select');

    $child_tag = $form_state->getValue('subtag_select');

    //FormState::setRedirect() redirects to the specified route.  The route
    //"entity.taxonomy_term.canonical" will send the user to the specified
    //taxonomy page.
    if($child_tag != 0) {
      $form_state->setRedirect('entity.taxonomy_term.canonical',
        array('taxonomy_term' => $child_tag)
      );
    }
    else {
      $form_state->setRedirect('entity.taxonomy_term.canonical',
        array('taxonomy_term' => $parent_tag)
      );
    }

  }

  /**
   * Helper function to provide arrays for parent-level and child-level
   * categories for the taxonomy selected in the configuration form.
   */
  protected function getTags() {
    $parent_tags = array();
    $child_tags = array();

    //Gets taxonomy for select box (this is set in the
    //config/install/tag_subtag_browsing.settings.yml file or the
    //configuration form).
    $config = \Drupal::config('tag_subtag_browsing.settings');

    $saved_taxonomy = $config->get('browsing_tag');

    //Use the Symfony service container to retrieve the terms for the specified
    //taxonomy.
    $container = \Drupal::getContainer();
    $terms = $container->get('entity_type.manager')->getStorage('taxonomy_term')->loadTree($saved_taxonomy);

    if (!empty($terms)) {
      //We use two foreach loops here to get parent and child terms separately,
      //so that we can use the name of the parent tag as the  optgroup label,
      //instead of the parent tag's numerical key.
      foreach($terms as $term) {
        if($term->parents[0] == 0) {
          $parent_tags[$term->tid] = $term->name;
        }
      }

      foreach($terms as $term) {
        if($term->parents[0] != 0) {
          $child_tags[$parent_tags[$term->parents[0]]][$term->tid] = $term->name;;
        }
      }

      return array(0 => $parent_tags, 1 => $child_tags);
    }
  }
}