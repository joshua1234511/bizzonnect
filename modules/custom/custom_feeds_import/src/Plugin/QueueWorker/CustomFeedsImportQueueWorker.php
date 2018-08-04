<?php  
/**
 * @file
 * Contains \Drupal\custom_feeds_import\Plugin\QueueWorker\CustomFeedsImportQueueWorker.
 */


namespace Drupal\custom_feeds_import\Plugin\QueueWorker;

use Drupal\Core\Queue\QueueWorkerBase;

use \Drupal\node\Entity\Node;
use \Drupal\file\Entity\File;

/**
 * Processes tasks for custom feeds import module.
 *
 * @QueueWorker(
 *   id = "custom_feeds_import",
 *   title = @Translation("Custom Feeds Import: Queue worker"),
 *   cron = {"time" = 90}
 * )
 */
class CustomFeedsImportQueueWorker extends QueueWorkerBase {

  /**
   * {@inheritdoc}
   */
  public function processItem($item) {
    // Create node object with attached file.
    $exists = db_query('SELECT 1 FROM {node_field_data} WHERE title = :title', array(':title' => $item->title))->fetchField();
    if(!$exists){
      $node = \Drupal\node\Entity\Node::create(['type' => 'article']);
      $node->set('title', $item->title);
      $node->set('uid', $item->uid);

      $field_category  = [
        ['target_id' => $item->tid]
      ];
      $node->set('field_category', $field_category);

      if(!empty($item->body)) {
        $bodyData = $item->body;
        if(!empty($item->author)){
          $bodyData .= '<p>Author: '.$item->author.'</p>';
        }
        if(!empty($item->source)){
          $bodyData .= '<p>Source: '.$item->source.'</p>';
        }
        if(!empty($item->link)){
          $bodyData .= '<p>Url: <a href="'.$item->link.'" target="_blank" >'.$item->link.'</a></p>';
        }
        $bodyData .= '<br /><br /><p>Powered by <a href="https://newsapi.org" target="_blank" >News API</a></p>';
        $body = [
          'value' => $bodyData,
          'format' => 'full_html',
        ];
        $node->set('body', $body);
      }

      if(!empty($item->url)) {
        $data = file_get_contents($item->url);
        $file_directory_name = 'apiImages';
        $path_parts = pathinfo($item->url);
        if(!empty($data)) {
          $file = file_save_data($data, 'public://'.$file_directory_name."/".$path_parts['basename'], FILE_EXISTS_RENAME);
          $field_image = [
            'target_id' => $file->id(),
            'alt' => $item->title,
            'title' => $item->title
          ];
          $node->set('field_image', $field_image);
        }
      }
      $node->enforceIsNew();
      $node->save();
    }
  }

}
