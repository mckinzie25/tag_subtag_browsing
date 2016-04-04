<?php
/**
 * @file
 * Contains Drupal\tag_subtag_browsing\Plugin\Block\TagSubtagBrowsingBlock.
 *
 * Provides a Block for Browsing Nodes by Tag and Subtag
 *
 * @Block(
 *   id = "tag_subtag_browsing",
 *   admin_label = @Translation("Better Browsing With Tags and Subtags"),
 * )
 */

namespace Drupal\tag_subtag_browsing\Plugin\Block;
use Drupal\Core\Block\BlockBase;

class TagSubtagBrowsingBlock extends BlockBase {
  /**
   * {@inheritdoc}
   */
  public function build() {

    $browsing_form = \Drupal::formBuilder()->getForm('Drupal\tag_subtag_browsing\Form\TagSubtagBrowsingForm');
    
    return $browsing_form;

  }

}