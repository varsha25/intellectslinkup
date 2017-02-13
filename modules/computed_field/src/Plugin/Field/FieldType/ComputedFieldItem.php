<?php

/**
 * @file
 * Contains Drupal\computed_field\Plugin\Field\FieldType\ComputedFieldType.
 */

namespace Drupal\computed_field\Plugin\Field\FieldType;

use Drupal\Component\Utility\Random;
use Drupal\Core\Field\FieldDefinitionInterface;
use Drupal\Core\Field\FieldItemBase;
use Drupal\Core\Field\FieldStorageDefinitionInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\StringTranslation\TranslationWrapper;
use Drupal\Core\TypedData\DataDefinition;

/**
 * Plugin implementation of the 'computed' field type.
 *
 * @FieldType(
 *   id = "computed",
 *   label = @Translation("Computed"),
 *   description = @Translation("Defines a field type that allows values to be computed via PHP code."),
 *   default_formatter = "computed"
 * )
 */
class ComputedFieldItem extends FieldItemBase
{

  /**
   * {@inheritdoc}
   */
  public static function defaultFieldSettings()
  {
    return array(
      'computed_code' => '$value = \'\';',
      'display_code' => '$display_output = $value;',
    ) + parent::defaultFieldSettings();
  }

  /**
   * {@inheritdoc}
   */
  public static function defaultStorageSettings()
  {
    return array(
      'data_type' => 'varchar',
      'data_length' => 32,
      'data_size' => 'normal',
      'data_precision' => 10,
      'data_scale' => 2,
    ) + parent::defaultStorageSettings();
  }

  /**
   * {@inheritdoc}
   */
  public static function propertyDefinitions(FieldStorageDefinitionInterface $field_definition)
  {
    $type = $field_definition->getSetting('data_type');

    if ($type == 'varchar' || $type == 'text') {
      $properties['value'] = DataDefinition::create('string')
        ->setLabel(t('Text value'))
        ->setSetting('case_sensitive', $field_definition->getSetting('case_sensitive'))
        ->setRequired(TRUE);
    }

    if ($type == 'int') {
      $properties['value'] = DataDefinition::create('integer')
        ->setLabel(t('Integer value'))
        ->setRequired(TRUE);
    }

    if ($type == 'float') {
      $properties['value'] = DataDefinition::create('float')
        ->setLabel(t('Float'))
        ->setRequired(TRUE);
    }

    if ($type == 'decimal') {
      $properties['value'] = DataDefinition::create('string')
        ->setLabel(t('Decimal value'))
        ->setRequired(TRUE);
    }

    return $properties;
  }

  /**
   * {@inheritdoc}
   */
  public static function schema(FieldStorageDefinitionInterface $field_definition)
  {
    $type = $field_definition->getSetting('data_type');
    $schema = array(
      'columns' => array(
        'value' => array(
          'type' => $type,
        ),
      ),
    );

    if ($type == 'varchar') {
      $schema['columns']['value']['length'] = (int)$field_definition->getSetting('data_length');
    }

    if ($type == 'text' || $type == 'int' || $type == 'float') {
      $schema['columns']['value']['size'] = $field_definition->getSetting('data_size');
    }

    if ($type == 'decimal') {
      $schema['columns']['value']['precision'] = (int)$field_definition->getSetting('data_precision');
      $schema['columns']['value']['scale'] = (int)$field_definition->getSetting('data_scale');
    }

    return $schema;
  }

  /**
   * {@inheritdoc}
   */
  public function fieldSettingsForm(array $form, FormStateInterface $form_state)
  {
    $element = array();

    $element['computed_code'] = array(
      '#type' => 'textarea',
      '#title' => $this->t('Computed Code (PHP)'),
      '#default_value' => $this->getSetting('computed_code'),
      '#required' => FALSE,
      '#description' => t('<p>The variables available to your code include: <code>&$value</code>, <code>$fields</code>, <code>$entity</code>, <code>$entity_manager</code>. To set the value of the field, set <code>$value</code>. For multi-value computed fields <code>$value</code> should be an array. Here\'s a simple example which sets the computed field\'s value to the value of the sum of the number fields (<code>field_a</code> and <code>field_b</code>) in a node entity:</p><p><code>$value = $entity->field_a->value + $entity->field_b->value;</code></p><p>An alternative example:</p><p><code>$value = $fields[\'field_a\'][0][\'value\'] + $fields[\'field_b\'][0][\'value\'];</code></p>')
    );

    $element['display_code'] = array(
      '#type' => 'textarea',
      '#title' => $this->t('Display Code (PHP)'),
      '#default_value' => $this->getSetting('display_code'),
      '#required' => FALSE,
      '#description' => t('This code should assign a string to the $display_output variable, which will be printed when the field is displayed. The raw computed value of the field is in <code>$value</code>. Also following variables are available: <code>$fields</code>, <code>$entity</code>, <code>$entity_manager</code>. Note: this code has no effect if you use the "Raw computed value" display formatter.')
    );

    return $element;
  }

  /**
   * {@inheritdoc}
   */
  public function storageSettingsForm(array &$form, FormStateInterface $form_state, $has_data)
  {
    $elements = [];
    $elements['data_type'] = array(
      '#type' => 'radios',
      '#title' => t('Data Type'),
      '#description' => t('The SQL datatype to store this field in.'),
      '#default_value' => $this->getSetting('data_type'),
      '#options' => array('varchar' => 'varchar', 'text' => 'text', 'int' => 'int', 'float' => 'float', 'numeric' => 'decimal'),
      '#required' => FALSE,
    );
    $elements['data_length'] = array(
      '#type' => 'textfield',
      '#title' => t('Data Length (varchar)'),
      '#description' => t('<strong>Only</strong> valid for <strong>varchar</strong> field. The length of the field stored in the database.'),
      '#default_value' => $this->getSetting('data_length'),
      '#required' => FALSE,
    );
    $elements['data_size'] = array(
      '#type' => 'select',
      '#title' => t('Data Size (text/int/float)'),
      '#description' => t('<strong>Only</strong> valid for <strong>text</strong>, <strong>int</strong> or <strong>float</strong> fields. The size of the field stored in the database.'),
      '#default_value' => $this->getSetting('data_size'),
      '#options' => array('tiny' => 'tiny', 'small' => 'small', 'medium' => 'medium', 'normal' => 'normal', 'big' => 'big'),
      '#required' => FALSE,
    );
    $elements['data_precision'] = array(
      '#type' => 'select',
      '#title' => t('Decimal Precision (decimal)'),
      '#description' => t('<strong>Only</strong> valid for <strong>decimal</strong> fields. The total number of digits to store in the database, including those to the right of the decimal.'),
      '#options' => array_combine(range(10, 32), range(10, 32)),
      '#default_value' => $this->getSetting('data_precision'),
      '#required' => FALSE,
    );
    $elements['data_scale'] = array(
      '#type' => 'select',
      '#title' => t('Decimal Scale (decimal)'),
      '#description' => t('<strong>Only</strong> valid for <strong>decimal</strong> fields. The number of digits to the right of the decimal. '),
      '#options' => array_combine(range(0, 10), range(0, 10)),
      '#default_value' => $this->getSetting('data_scale'),
      '#required' => FALSE,
    );

    return $elements;
  }

  /**
   * {@inheritdoc}
   */
  public function isEmpty()
  {
    $value = $this->get('value')->getValue();
    $data_type = $this->getSetting('data_type');
    $empty = TRUE;

    // This will depend on the class of data type.
    switch ($data_type) {

      case 'int':
      case 'float':
      case 'numeric':
        // For numbers, the field is empty if the value isn't numeric.
        $empty = $value === NULL || !is_numeric($value);
        break;

      case 'varchar':
      case 'text':
      case 'longtext':
        // For strings, the field is empty if it doesn't match the empty string.
        $empty = $value === NULL || $value === '';
        break;
    }
    return $empty;
  }

  /**
   * {@inheritdoc}
   */
  public function preSave()
  {
    $value = $this->executeComputedCode();
    $this->setValue($value);
  }

  protected function executeComputedCode() {
    $code = $this->getSetting('computed_code');
    $entity_manager = \Drupal::EntityManager();
    $entity = $this->getEntity();
    $fields = $this->getEntity()->toArray();
    $value = NULL;

    eval($code);
    return $value;
  }

  public function executeDisplayCode() {
    $code = $this->getSetting('display_code');
    $entity_manager = \Drupal::EntityManager();
    $entity = $this->getEntity();
    $fields = $this->getEntity()->toArray();
    $value = $this->get('value')->getValue();
    $display_output = NULL;

    eval($code);
    return $display_output;
  }}
