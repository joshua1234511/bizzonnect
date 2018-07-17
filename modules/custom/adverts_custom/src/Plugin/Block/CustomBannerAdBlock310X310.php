<?php


/**
 * Provides the 'Ads Banner 310 X 310' Block
 *
 * @Block(
 *   id = "custom_banner_ad_block_field_ads_banner_310_x_310",
 *   admin_label = @Translation("Ads Banner 310 X 310"),
 * )
 */


namespace Drupal\adverts_custom\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Block\BlockPluginInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Entity;

class CustomBannerAdBlock310X310 extends BlockBase implements \Drupal\Core\Block\BlockPluginInterface
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
        $data = _display_advert_node();
        if(!empty($data)){
        $o = '<div class="desktopAd"><a href="'.$data['field_url'].'" rel="nofollow" class="ads-click" target="_blank" data-nid="'.$data['nid'].'" data-title="'.$data['title'].'" data-type="advert" data-device="landscape" ><img src="'.$data['mobile_image'].'" width="'.$data['field_mobile_images']['width'].'" height="'.$data['field_mobile_images']['height'].'" alt="'.$data['field_mobile_images']['alt'].'" title="'.$data['field_mobile_images']['title'].'" typeof="Image" class="img-responsive"></a></div>
          <div class="mobileAd"><a href="'.$data['field_url'].'" rel="nofollow" class="ads-click" target="_blank" data-nid="'.$data['nid'].'" data-title="'.$data['title'].'" data-type="advert" data-device="portrait"><img src="'.$data['mobile_image'].'" width="'.$data['field_mobile_images']['width'].'" height="'.$data['field_mobile_images']['height'].'" alt="'.$data['field_mobile_images']['alt'].'" title="'.$data['field_mobile_images']['title'].'" typeof="Image" class="img-responsive"></a></div>';
        }
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

