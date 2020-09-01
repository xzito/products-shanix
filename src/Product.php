<?php

namespace Xzito\Products;

use Xzito\Solutions\Solution;

class Product {
  private $id;
  private $name;
  private $short_description;
  private $card_image;
  private $small_icon;
  private $large_icon;
  private $banner;
  private $features;
  private $main_copy;
  private $related_solutions;
  private $cta;

  public static function find_by_name($name) {
    $query = new \WP_Query([
      'numberposts' => 1,
      'post_type' => ProductPostType::ID,
      'name' => $name,
      'fields' => 'ids',
    ]);

    $id = $query->posts[0];

    return new Product($id);
  }

  private static function find_published($post_type, $post_ids) {
    $ids = [];

    foreach ($post_ids as $id) {
      $ids[] = intval($id);
    }

    $query = new \WP_Query([
      'nopaging' => true,
      'post_type' => $post_type,
      'post_status' => 'publish',
      'fields' => 'ids',
      'post__in' => $ids,
    ]);

    return $query->posts ?? [];
  }

  public static function find_related_attachments($name) {
    $query = new \WP_Query([
      'nopaging' => true,
      'post_type' => 'attachment',
      'post_status' => 'inherit',
      'fields' => 'ids',
      'tax_query' => [
        [
          'taxonomy' => Products::TAXONOMY_ID,
          'field' => 'name',
          'terms' => $name,
        ],
      ],
    ]);

    return $query->posts ?? [];
  }

  public function __construct($product_id = '') {
    $this->id = $product_id;
    $this->set_name();
    $this->set_short_description();
    $this->set_card_image();
    $this->set_banner();
    $this->set_small_icon();
    $this->set_large_icon();
    $this->set_main_copy();
    $this->set_features();
    $this->set_related_solutions();
    $this->set_cta();
  }

  public function id() {
    return $this->id;
  }

  public function name() {
    return $this->name;
  }

  public function slug() {
    return get_post_field('post_name', $this->id);
  }

  public function link() {
    return get_post_permalink($this->id);
  }

  public function term() {
    return get_term_by('name', $this->name, Products::TAXONOMY_ID);
  }

  public function short_description() {
    return $this->short_description;
  }

  public function card_image($size = 'thumbnail') {
    return wp_get_attachment_image_url($this->card_image, $size);
  }

  public function small_icon() {
    return wp_get_attachment_image_url($this->small_icon, 'full');
  }

  public function large_icon() {
    return wp_get_attachment_image_url($this->large_icon, 'full');
  }

  public function banner($size = 'full') {
    return wp_get_attachment_image_url($this->banner, $size);
  }

  public function main_copy() {
    $copy = $this->main_copy;

    $id = $copy['supporting_image']['image'];
    $url = wp_get_attachment_image_url($id, 'large');

    $copy['supporting_image']['url'] = $url;

    return $copy;
  }

  public function features() {
    return $this->features;
  }

  public function cta($size = 'large') {
    $cta = $this->cta;

    $cta['image'] = wp_get_attachment_image_url($cta['image'], $size);

    return $cta;
  }

  public function related_solutions() {
    return $this->related_solutions;
  }

  private function set_name() {
    $default = 'Unnamed Product';

    $this->name = (get_field('product_info', $this->id)['name'] ?: $default);
  }

  private function set_short_description() {
    $this->short_description = get_field('product_info', $this->id)['short_description'];
  }

  private function set_card_image() {
    $this->card_image = get_field('product_images', $this->id)['card'];
  }

  private function set_banner() {
    $this->banner = get_field('product_images', $this->id)['banner'];
  }

  private function set_small_icon() {
    $this->small_icon = get_field('product_images', $this->id)['small_icon'];
  }

  private function set_large_icon() {
    $this->large_icon = get_field('product_images', $this->id)['large_icon'];
  }

  private function set_main_copy() {
    $this->main_copy = get_field('product_main_copy', $this->id);
  }

  private function set_features() {
    $features = get_field('product_features', $this->id);

    foreach ($features as &$feature) {
      $feature['image_tag'] = wp_get_attachment_image( $feature['side_image'], '1200x0' );
    }

    $this->features = $features;
  }

  private function set_related_solutions() {
    $solutions_ids = get_field('portfolios_solutions', $this->id) ?? [];

    $related_solutions = array_map(function($solution_id) {
      return new Solution($solution_id);
    }, $solutions_ids);

    $this->related_solutions = $related_solutions;

  }

  private function set_cta() {
    $cta_settings   = get_field('product_cta', $this->id);
    $overlay        = $cta_settings['overlay_color'];
    $overlay_colors = [
      'light' => '#F5F5F5',
      'blue'  => '#293583'
    ];

    $cta_data = [
      'show'           => $cta_settings['show'],
      'heading'        => $cta_settings['heading'],
      'overlay_color'  => $overlay,
      'overlay_hex'    => $overlay_colors[$overlay],
      'text'           => $cta_settings['text'],
      'button_text'    => $cta_settings['button_text'],
      'link'           => $cta_settings['link'],
      'side_image_tag' => wp_get_attachment_image($cta_settings['image'], '1200x0'),
      'bg_image_url'   => wp_get_attachment_image_url($cta_settings['bg_image'], 'fullwidth'),
    ];

    $this->cta = $cta_data;
  }
}
