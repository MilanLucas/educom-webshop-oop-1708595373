<?php
require_once('basicDoc.php');
abstract class ProductDoc Extends BasicDoc{
  protected  function showProductContent($type, $imagesize){
    /*
    type string
    imagesize array('width', 'height')
    */
    $page = $this->data -> page; 
    $items = $this->data->products;
    if($type == 'menu'){
      $this->showProductMenu($page, $items, $imagesize);
    }  
  }

  protected function showAddToButton($page, $item=''){
  /* 
  page string
  item array
  */
  if ($page == 'cart'){
    $action = 'placeOrder';
    $text = 'Plaats bestelling';
    $id = 'id';
  } else {
    $action = 'addToCart';
    $text = 'Toevoegen aan bestelling';
    $id = $item->id;
  }
  echo '';
    if (isset($_SESSION['user'])){
      echo '
      <form action="index.php?page="cart" method="POST">
      <input type="hidden" name="page" value="cart">
      <input type="hidden" name="action" value="'.$action.'">
      <input type="hidden" name="id" value="'.$id.'">
      <button type="submit">'.$text.'</button>
      </form>';
    }
  }

  protected function showProductImage($item, $imagesize){
    /*
    item array 
    imagesize array('width', 'height')
    */
      echo '<img src="Images\\'.$item->image.'"  style="width:'.$imagesize['width'].'px;height:'.$imagesize['height'].'px;">';

  }

  protected function showProductImageLink($item, $imagesize){
    /*
    item array 
    imagesize array('width', 'height')
    */
    echo  '<a class=product_image  href="index.php?page=product-'.$item->name.'-'.$item->id.'">';
    $this->showProductImage($item, $imagesize);
    echo '</a>';
  }

  private function showProductName($item){
    // item array
    echo '<h3 class=product_name>
        <a class=product_text href="index.php?page=product-'.$item->name.'-'.$item->id.'">
        <span>'.$item->name.' </span>
        </a>
      </h3>';
  }


  private function showProductMenu($page, $items, $imagesize){
    /*
    page string
    item array 
    imagesize array('width', 'height')
    */
    echo '';
    if(isset($this->data->error['dbError'])){
      echo 'Database kan momenteel niet bereikt worden';
    }
    echo '<ul class=items>';
    foreach ($items as $item) {
      echo '
      <li class=product_webshop>
      <br>
      <article>'; 
      $this->showProductImageLink($item, $imagesize); 
      $this->showProductName($item);

      if ($page == 'webshop'){
      echo '
        <div class=product_price>
        <span class=price>&euro;'.$item->price.'  </span>
        </div>';
        $this-> showAddToButton($page, $item);
      }
      echo '</article>
      </li>';
    }
    echo '</ul>';
  }
}
