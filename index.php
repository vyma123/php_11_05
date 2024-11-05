<?php
require_once 'includes/db.inc.php';
require_once './includes/functions.php';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="stylesheet" href="styles/style.css" type="text/css">

    <link href="//maxcdn.bootstrapcdn.com/font-awesome/4.1.0/css/font-awesome.min.css" rel="stylesheet">
   <style>
    .box_gallery{
    display: flex ;
    align-items: center;
    justify-content: space-between;
}
   </style>
    <!-- link semantic ui -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/semantic-ui/2.5.0/semantic.min.css"  />
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/semantic-ui/2.4.1/semantic.min.js"></script>

    <title>PHP1</title>
</head>

<body>

<!-- popup add product -->
<?php include('model_add_product.php');?>
<?php include('model_add_property.php');?>

<!-- close popup add product -->

    <section class="container">
        <h1>PHP1</h1>

        <div class="product_header">
            <div class="product_header_top">
                <div>
                    <button id="add_product" class="ui primary button">
                        Add product
                    </button>
                    <button id="add_property" class="ui button">
                        Add property
                    </button>
                    <a href="#" class="ui button">
                        Sync online
                    </a>
                </div>
                <div class="ui icon input">
                    <input name="search" type="text" placeholder="Search product..."  value="">
                    <i class="inverted circular search link icon" ></i>
                </div>
            </div>
            <div class="product_header_bottom">
                <select class="ui dropdown" name="sort_by" id="sort_by">
                    <option value="date">Date</option>
                    <option value="product_name">Product name</option>
                    <option value="price">Price</option>
                </select>
                <select class="ui dropdown" name="order">
                    <option value="ASC">ASC</option>
                    <option value="DESC">DESC</option>
                </select>

                <select class="ui dropdown" name="category">
                   <option value="0">Category</option>
                   <option value="1">category 1</option>
                </select>

                <select class="ui dropdown" name="tag">
                    <option value="0">Select Tag</option>
                    <option value="1">tag 1</option>
                
                </select>
                <div class="ui input">
                    <input type="date" value="" id="date_from" name="date_from">
                </div>
                <div class="ui input">
                    <input type="date" value="" id="date_to" name="date_to">
                </div>
                <div class="ui input">
                    <input type="text" value="" id="price_from" name="price_from" placeholder="price from"
                    >
                </div>
                <div class="ui input">
                    <input type="text" value="" id="price_to" name="price_to" placeholder="price to">
                </div>
                <button type="submit" class="ui button">
                    Filter
                </button>
            </div>
        </div>

        <!-- table -->
         <div class="box_table">

    <table class="ui compact celled table">
  <thead>
    <tr>
      <th>Date</th>
      <th>Product name</th>
      <th>SKU</th>
      <th>Price</th>
      <th>Feature Image</th>
      <th class="gallery_name">Gallery</th>
      <th >Categories</th>
      <th class="tag_name">Tags</th>
      <th>Action</th>
    </tr>
  </thead>
  <tbody>
     <tr>
      <td>td1</td>
      <td class="product_name">td2</td>
      <td class="sku">td3</td>
      <td>td4</td>
      <td>
          <img height="30" src="">
      </td>
      <td class="gallery_images">gallery 1</td>
      <td>category</td>
      <td>tag</td>
      <td>
        <a class="edit_button" href="#">
        <i class="edit icon"></i>
        </a>
      
        <a  class="delete_button" href="">
        <i class="trash icon"></i>
        </a>
      </td>
    </tr>
        <tr>
            <td colspan="9" style="text-align: center;">Product not found</td>
        </tr>
  </tbody>
</table>
</div>
<!-- 
<div class="pagination_box">
<div class="ui pagination menu">                
</div>
</div> -->

</section>
<script src="./jquery/my_jquery_functions.js">
    
</script>


</body>
</html>