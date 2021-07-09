<ul class="sp-hubspot-grid hs-three-grid">
  <?php
    foreach( $response->results as $slug ):
      $url = $slug->url;
      $featured_image = $slug->featuredImage;
      $title = $slug->htmlTitle
    ?>
    <li class="sp-hs-post">
      <a class="hubspot-post-wrapper" href="<?php if( $url && !empty( $url ) ){ _e( $url ); } else{ echo "#"; }?>">
        <div class="thumbnail-bg" <?php if( $featured_image && !empty( $featured_image ) ){ echo 'style="background-image:url('.$featured_image.');"'; }?>></div>
        <div class="title"><?php if( $title && !empty( $title ) ){ _e( $title ); }?></div>
      </a>
    </li>
  <?php endforeach;?>
</ul>
