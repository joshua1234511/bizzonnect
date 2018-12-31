<?php
namespace Drupal\record_shorten;

/**
 * Sets up a form for choosing the Shorten URLs service.
 */
class record_shorten_views_handler_filter_string_service extends views_handler_filter_many_to_one {
  function get_value_options() {
    $this->value_options = array_combine(array_keys(\Drupal::moduleHandler()->invokeAll('shorten_service')), array_keys(\Drupal::moduleHandler()->invokeAll('shorten_service')));
  }
}
