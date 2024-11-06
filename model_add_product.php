

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
  <!-- form -->
  <form class="ui form form_add_products" class='editProduct' id="saveProduct" enctype="multipart/form-data">
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
    <div class="field featured_image_box">
      <label>Featured Image</label>
      <div class="box_gallery">
        <div id="resultContainer">
          <img src="" alt="featured Image" id="uploadedImage" style="height: 80px; max-width: 100%;" />
        </div>
        <div class="ui small image">
          <input accept="image/*" type="file" name="featured_image" id="featured_image" accept="image/*">
        </div>
      </div>
    </div>
    <div class="field featured_image_box">
      <label>Gallery</label>
      <div class="box_gallery">
        <div id="galleryPreviewContainer">
          <img src="" alt="Gallery Image" id="galleryImage" style=" height: 80px; max-width: 100%;" />
        </div>
        <div class="ui small image">
          <input accept="image/*" type="file" name="gallery[]" id="gallery" accept="image/*" multiple>
        </div>
      </div>
    </div>
    <div id="load_property">
      <div class="field featured_image_box">
        <label>Category</label>
        <select id="categories_select" name="categories[]" multiple class="select_property">
            <option value="0" >
              category 1
            </option>
      </select>
      </div>
      <div class="field featured_image_box">
        <label>Tag</label>
        <select id="tags_select" name="tags[]" multiple class="select_property">
        <option value="0" >
              tag 1
          </option>
    </select>
      </div>
    </div>

    <div class="box_button_add">
      <button id="close_product" class="ui button" type="button">Close</button>
      <button class="ui button" type="submit">Submit</button>
    </div>
  </form>
</div>


