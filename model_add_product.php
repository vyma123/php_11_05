
<div class="ui modal product_box">
  <div id="errMessage_add" class="ui negative message d-none">
    <div class="header">
    Product Name, SKU, Price, and Featured Image are required fields.  </div>
    </div>

    <div id="err_valid_Message_product" class="ui negative message d-none">
  <div class="header">
  don't allow special character.  </div>
  </div>
  <div id="okMessage_product" class="ui success message d-none">
    <div class="header">
    Added successfully.
    </div>
  </div>
  
  <div id="err_valid_Message_price" class="ui negative message d-none">
  <div class="header">
  Price just allow numbers.  </div>
  </div>
<form class="ui form form_add_products" id="saveProduct" enctype="multipart/form-data">
  <div class="field">
    <label>Product Name</label>
    <input type="text" name="product_name" id="product_name" placeholder="Product Name">
  </div>
  <div class="field">
    <label>SKU</label>
    <input type="text" name="sku" id="sku" placeholder="SKU">
  </div>
  <div class="field">
    <label>Price</label>
    <input type="text" name="price" id="price" placeholder="Price">
  </div>
  <!-- <div class="field featured_image_box">
    <label>Featured Image</label>
    <div id="resultContainer" class="ui small image">
    <img height="80" src="" id="uploadedImage">
    <input accept="image/*" class="featured_image" type="file" name="featured_image" id="featured_image">
    </div>
  </div>
 -->

  <div class="field featured_image_box">
    <label>Featured Image</label>
    <div class="box_gallery">
      <div id="resultContainer" >
        <img src="" alt="featured Image" id="uploadedImage" style="display: none; height: 80px; max-width: 100%;" />
      </div>
      <div class="ui small image">
        <input accept="image/*" type="file" name="featured_image" id="featured_image" accept="image/*" >
      </div>
    </div>
  </div>
  

  <div class="field featured_image_box">
    <label>Gallery</label>
    <div class="box_gallery">
      <div id="galleryPreviewContainer" >
        <img src="" alt="Gallery Image" id="galleryImage" style="display: none; height: 80px; max-width: 100%;" />
      </div>
      <div class="ui small image">
        <input accept="image/*" type="file" name="gallery[]" id="gallery" accept="image/*" multiple>
      </div>
    </div>
  </div>

  <div class="field featured_image_box">
  <label>Category</label>
  <select name="tags[]" multiple class="select_property">
            <?php
            
            $type_ = 'category';
            $categories = select_property($pdo, $type_);
             if($categories) {
                foreach ($categories as $category){
                    // $selected_category = in_array($category['id'], $selected_categories) && empty($productId) ? 'selected' : '';
            ?>
            <option value="<?= htmlspecialchars($category['id']) ?>" >
                <?= htmlspecialchars($category['name_']) ?>
            </option>
            <?php }} ?>
        </select>
  </div>
  <div class="field featured_image_box">
  <label>Tag</label>
  <select name="tags[]" multiple class="select_property">
            <?php
            
            $type_ = 'tag';
            $tags = select_property($pdo, $type_);
             if($tags) {
                foreach ($tags as $tag){
                    // $selected_category = in_array($category['id'], $selected_categories) && empty($productId) ? 'selected' : '';
            ?>
            <option value="<?= htmlspecialchars($tag['id']) ?>" >
                <?= htmlspecialchars($tag['name_']) ?>
            </option>
            <?php }} ?>
    </select>
  </div>
  <div class="box_button_add">
      <button id="close_product" class="ui button" type="submit">Close</button>
      <button class="ui button" type="submit">Submit</button>
    </div>
</form>
</div>


