<?php

namespace Xzito\Products;

class ProductsPage {
  private $banner;
  private $heading;
  private $about;
  private $cta;

  public function __construct() {
    $this->set_banner();
    $this->set_heading();
    $this->set_about();
    $this->set_cta();
  }

  public function banner($size = 'full') {
    return wp_get_attachment_image_url($this->banner, $size);
  }

  public function link() {
    return get_post_type_archive_link(ProductPostType::ID);
  }

  public function heading() {
    return $this->heading;
  }

  public function about() {
    return $this->about;
  }

  public function cta($size = 'thumbnail') {
    $cta = $this->cta;

    $cta['image'] = wp_get_attachment_image_url($cta['image'], $size);

    return $cta;
  }

  private function set_banner() {
    $this->banner = get_field('products_page', 'options')['banner'];
  }

  private function set_heading() {
    $this->heading = get_field('products_page', 'options')['heading'];
  }

  private function set_about() {
    $this->about = get_field('products_page', 'options')['about'];
  }

  private function set_cta() {
    $this->cta = get_field('products_page', 'options')['cta'];
  }
}
