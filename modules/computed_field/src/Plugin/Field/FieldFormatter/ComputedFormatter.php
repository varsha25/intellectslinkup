<?php

/**
 * @file
 * Contains Drupal\computed_field\Plugin\Field\FieldFormatter\ComputedFieldFormatter.
 */

namespace Drupal\computed_field\Plugin\Field\FieldFormatter;

use Drupal\Component\Utility\SafeMarkup;
use Drupal\Core\Field\FieldItemInterface;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\FormatterBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Plugin implementation of the 'computed' formatter.
 *
 * @FieldFormatter(
 *   id = "computed",
 *   label = @Translation("Computed"),
 *   field_types = {
 *     "computed"
 *   }
 * )
 */
class ComputedFormatter extends FormatterBase {
  /**
   * {@inheritdoc}
   */
  public static function defaultSettings() {
    return array(
      'sanitized' => TRUE
      // Implement default settings.
    ) + parent::defaultSettings();
  }

  /**
   * {@inheritdoc}
   */
  public function settingsForm(array $form, FormStateInterface $form_state) {
    return array(
      'sanitized' => array(
        '#type' => 'checkbox',
        '#title' => t('Sanitized'),
        '#default_value' => $this->getSetting('sanitized'),
      )
      // Implement settings form.
    ) + parent::settingsForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function settingsSummary() {
    $summary = [];

    $summary[] = $this->getSetting('sanitized') ? t('Sanitized') : t('Unsanitized');
    // Implement settings summary.

    return $summary;
  }

  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode) {
    $elements = [];

    foreach ($items as $delta => $item) {
      $elements[$delta] = ['#markup' => $this->viewValue($item)];
    }

    return $elements;
  }

  /**
   * Generate the output appropriate for one field item.
   *
   * @param \Drupal\Core\Field\FieldItemInterface $item
   *   One field item.
   *
   * @return string
   *   The textual output generated.
   */
  protected function viewValue(FieldItemInterface $item) {
    $display_output = $item->executeDisplayCode();

    if ($this->getSetting('sanitized')) {
      return nl2br(SafeMarkup::checkPlain($display_output));
    } else {
      return nl2br($display_output);
    }
  }


}
