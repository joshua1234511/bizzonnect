<?php
 
namespace Drupal\adverts_custom\Controllers;
 
use Drupal\Core\Controller\ControllerBase;
use Drupal\node\Entity\Node;
use Drupal\Core\Url;
use Drupal\image\Entity\ImageStyle;
use Drupal\Core\File\File;
use Drupal\comment\Entity\Comment;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\JsonResponse;
use Drupal\Component\Render;
use Drupal\user\Entity\User;
use Drupal\Component\Render\PlainTextOutput;
use Symfony\Component\HttpFoundation;
use Drupal\Core\Cache\CacheBackendInterface;
use Drupal\Component\Render\FormattableMarkup;
use Drupal\taxonomy\Entity\Term;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\OpenModalDialogCommand;

class AdvertsCustomController extends ControllerBase
{
	public function adClick()
    {
      $query = db_select('ad_clicks', 'pnc');
      $query->fields('pnc');
      $query->condition('nid', $_POST['nid'] ,'=');
      $result = $query->execute();
      $result->allowRowCount = TRUE;
 
      if ($result->rowCount() > 0){
          $query = db_update('ad_clicks');
          $query->expression('clicks', 'clicks + 1')
                ->condition('nid', $_POST['nid'], '=')
                ->execute();
      }
      else{
          $Data = db_insert('ad_clicks')
          ->fields(
                    array(
                      'nid' => $_POST['nid'],
                      'clicks' => 1,
                      )
                  )
          ->execute();
      }
      // detailed save
      $session_manager = \Drupal::service('session_manager');
      $session_id = $session_manager->getId();
      $current_path = \Drupal::service('path.current')->getPath();
      $url = (!empty($_POST['url'])?$_POST['url'] : $current_path);
      $Data = db_insert('ad_click_stats')
          ->fields(
                    array(
                      'nid' => $_POST['nid'],
                      'created' => time(),
                      'page_url' => $url,
                      'session_id' => $session_id,
                      'ip' => getUserIP(),
                      'uid' => \Drupal::currentUser()->id(),
                      'ad_url' => $_POST['ad_url'],
                      'title' => $_POST['title'],
                      'type' => $_POST['type'],
                      'device' => $_POST['device'],
                      )
                  )
          ->execute();
      exit();
    }
 
    public function adClickAll()
    {
      \Drupal::service('page_cache_kill_switch')->trigger();
        $header1 = array(
              array('data' => $this->t('Ad Title'), 'field' => 'nid'),
              array('data' => $this->t('Ad Link'), 'field' => 'ad_url'),
              array('data' => $this->t('Clicks')),
              array('data' => $this->t('Details')),
            );
        $db = \Drupal::database();
        $query1 = $db->select('ad_click_stats', 'pnc');
        $query1->fields('pnc', array('nid'));
        $query1->groupBy('pnc.nid');
        $query1->condition('pnc.type','advert');
        $table_sort1 = $query1->extend('Drupal\Core\Database\Query\TableSortExtender')->orderByHeader($header1);
        $pager1 = $table_sort1->extend('Drupal\Core\Database\Query\PagerSelectExtender')->limit(20);
        $results1 = $pager1->execute()->fetchAll();
        $rows1 = array();
        foreach($results1 as $row) {
            $link = db_query("SELECT clicks from {ad_clicks} WHERE nid = :nid LIMIT 1", array(":nid" => $row->nid))->fetchField();
            $title = db_query("SELECT title from {ad_click_stats} WHERE nid = :nid LIMIT 1", array(":nid" => $row->nid))->fetchField();
            $ad_url = db_query("SELECT ad_url from {ad_click_stats} WHERE nid = :nid LIMIT 1", array(":nid" => $row->nid))->fetchField();
            $detail   = \Drupal\Core\Url::fromUserInput('/admin/adclick/'.$row->nid);
        $rows1[] = array('data' => array(
            'title' => $title,
            'link' => $ad_url,
            'clicks' => $link,
            \Drupal::l('Details', $detail),
         ));
        }
        $build['config_table_main'] = array(
        '#type' => 'markup',
        '#markup' => '<div><h2>Main Banner Ads</h2></div>',
        );
        // Generate the table.
        $build['clicks_table_main'] = array(
          '#theme' => 'table',
          '#header' => $header1,
          '#rows' => $rows1,
        );
        // Finally add the pager.
        $build['pager1'] = array(
          '#type' => 'pager',
          '#element' => $query1->element,
        );
        
        return $build;
    }
 
    function adClickSingle($nid){
    \Drupal::service('page_cache_kill_switch')->trigger();
      global $base_url;
      $header = array(
        array('data' => $this->t('Date'), 'field' => 'created', 'sort' => 'desc'),
        array('data' => $this->t('AD Url'), 'field' => 'ad_url'),
        array('data' => $this->t('Page Url'), 'field' => 'page_url'),
        array('data' => $this->t('Title'), 'field' => 'title'),
        array('data' => $this->t('Device'), 'field' => 'device'),
      );
 
      $db = \Drupal::database();
      $query = $db->select('ad_click_stats','c');
      $query->fields('c');
      $query->condition('c.nid',$nid);
      $table_sort = $query->extend('Drupal\Core\Database\Query\TableSortExtender')
                          ->orderByHeader($header);
      $pager = $table_sort->extend('Drupal\Core\Database\Query\PagerSelectExtender')
                          ->limit(20);
      $result = $pager->execute();
 
      $rows = array();
      foreach($result as $row) {
        $rows[] = array('data' => array(
          'created' => date('d M Y, H:i:s', $row->created),
          'ad_url' => $row->ad_url,
          'page_url' => $row->page_url,
          'title' => $row->title,
          'device' => $row->device,
 
        ));
      }
 
      // Generate the table.
      $build['config_table'] = array(
        '#theme' => 'table',
        '#header' => $header,
        '#rows' => $rows,
      );
 
      // Finally add the pager.
      $build['pager'] = array(
        '#type' => 'pager'
      );
 
      return $build;
    }

}