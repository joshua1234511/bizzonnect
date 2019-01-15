<?php


/**
 * Provides the 'Custom User Block'
 *
 * @Block(
 *   id = "custom_user_block",
 *   admin_label = @Translation("Custom User Block"),
 * )
 */


namespace Drupal\bizzonnect\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Block\BlockPluginInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Entity;
use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\JsonResponse;
use Drupal\Core\Database;
use Drupal\node\Entity\Node;
use Drupal\Core\Url;
use Symfony\Component\HttpFoundation;
use Drupal\Component\Render\FormattableMarkup;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\OpenModalDialogCommand;
use Drupal\user\Entity\User;
use Drupal\Core\Session\AccountInterface;
use Symfony\Component\HttpFoundation\Request;

class CustomUserBlock extends BlockBase implements \Drupal\Core\Block\BlockPluginInterface
{

    /**
    * {@inheritdoc}
    */
    public function getCacheMaxAge() {
      return 0;
    }

    /**
     * {@inheritdoc}
     */
    public function build()
    {
        \Drupal::service('page_cache_kill_switch')->trigger();
        $o = '';
        $db = \Drupal::database();
        $query = $db->select('node_field_data', 'n');
        $query->fields('n',array('nid'));
        $query->condition('n.type', "blog_post");
        $query->condition('n.uid',\Drupal::currentUser()->id());
        $query->condition('n.status', '1');
        $blogCount = $query->countQuery()->execute()->fetchField();

        $query = $db->select('node_field_data', 'n');
        $query->fields('n',array('nid'));
        $query->condition('n.type', "content_page");
        $query->condition('n.uid',\Drupal::currentUser()->id());
        $query->condition('n.status', '1');
        $businessCount = $query->countQuery()->execute()->fetchField();

        $query = $db->select('node_field_data', 'n');
        $query->fields('n',array('nid'));
        $query->condition('n.type', "d_product");
        $query->condition('n.uid',\Drupal::currentUser()->id());
        $query->condition('n.status', '1');
        $productCount = $query->countQuery()->execute()->fetchField();

        $o = '<div class="submissions-count">
          <div class="col-sm-4">
            <h3>Business Listing</h3>
            <span>'.$businessCount.'/1</span>';
        if($businessCount < 1):
          $o .= '<a href="/node/add/content_page">Add Business Listing</a>';
        endif;
        $o .= '</div>
          <div class="col-sm-4">
            <h3>Product Listing</h3>
            <span>'.$productCount.'/3</span>';
        if($productCount < 5):
          $o .= '<a href="/node/add/d_product">Add Product</a>';
        endif;
        $o .= '</div>
          <div class="col-sm-4">
            <h3>Blog Listing</h3>
            <span>'.$blogCount.'/3</span>';
        if($blogCount < 3):
          $o .= '<a href="/node/add/blog_post">Add Blog Post</a>';
        endif;
        $o .= '</div>
        </div>';
        $form['#markup'] = $o;
        $form['#cache']['max-age'] = 0;
        $form['#allowed_tags'] = [
            'div', 'script', 'style', 'link', 'form',
            'h2', 'h1', 'h3', 'h4', 'h5',
            'table', 'thead', 'tr', 'td', 'tbody', 'tfoot',
            'img', 'a', 'span', 'option', 'select', 'input',
            'ul', 'li', 'br', 'p', 'link', 'hr', 'style', 'img',

        ];
        return $form;

    }

}

