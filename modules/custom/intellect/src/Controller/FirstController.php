<?php    
namespace Drupal\intellect\Controller;
/**
 * Provides route responses for the Example module.
 */
class FirstController {
  /**
   * Returns a simple page.
   *
   * @return array
   *   A simple renderable array.
   */
  public function linkingUp() {
    $element = array(
      '#markup' => 'Hello world!',
    );
    return $element;
  }
}
    