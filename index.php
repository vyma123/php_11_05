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
    height: 80px !important;
    }

    body::-webkit-scrollbar {
    display: none;

    }
    #galleryPreviewContainer {
    overflow-x: auto; /* Enables horizontal scroll */
    white-space: nowrap; /* Prevents images from wrapping to the next line */
}

#galleryPreviewContainer img {
    height: 80px;
    width: 100%; /* Ensures image scaling within the div */
    display: inline-block; /* Aligns images side by side */
}

    .form_add_products{
        padding: 1rem;
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

    <table id="tableID" class="ui compact celled table">
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
  <?php 
        $per_page_record = 5;
        if (isset($_GET["page"])) {    
            $page  = $_GET["page"];    
        } else {    
            $page=1;    
          }    
          $start_from = ($page-1) * $per_page_record;     
          $query = "SELECT * FROM products LIMIT $start_from, $per_page_record";     
          $stmt = $pdo->prepare($query);
          $stmt->execute();
          $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

         if (count($results) > 0) {

            foreach ($results as $row){
              $product_id = $row['id']; ?>
    

     <tr>
     <td><?php echo htmlspecialchars($row['date'])?></td>
      <td class="product_name"><?php echo htmlspecialchars($row['product_name'])?></td>
      <td class="sku"><?php echo htmlspecialchars($row['sku'])?></td>
      <td><?php echo htmlspecialchars($row['price'])?></td>
      <td>
          <img height="30" src="./uploads/<?php echo $row['featured_image']; ?>">
      </td>
      <td class="gallery_images">
              <?php 
            $query = "SELECT p.name_ FROM product_property pp
                    JOIN property p ON pp.property_id = p.id
                    WHERE pp.product_id = :product_id AND p.type_ = 'gallery'";
            $stmt = $pdo->prepare($query);
            $stmt->bindParam(':product_id', $product_id, PDO::PARAM_INT);
            $stmt->execute();
            $galleryImages = $stmt->fetchAll(PDO::FETCH_ASSOC);
            foreach ($galleryImages as $image) {?> 
        <img  height="40" src="./uploads/<?= $image['name_'] ?>">
      <?php }?>
      </td>
      <td>
      <?php 
            $query = "SELECT p.name_ FROM product_property pp
                    JOIN property p ON pp.property_id = p.id
                    WHERE pp.product_id = :product_id AND p.type_ = 'category'";
            $stmt = $pdo->prepare($query);
            $stmt->bindParam(':product_id', $product_id, PDO::PARAM_INT);
            $stmt->execute();
            $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $totalCategories = count($categories);
            foreach ($categories as $index => $category) {?> 
            <span><?php echo htmlspecialchars($category['name_']);
                    if($index < $totalCategories -1 ){
                        echo ', ';
                    }
                   ?></span>
            <?php }?>
      </td>
      <td>
      <?php 
            $query = "SELECT p.name_ FROM product_property pp
                    JOIN property p ON pp.property_id = p.id
                    WHERE pp.product_id = :product_id AND p.type_ = 'tag'";
            $stmt = $pdo->prepare($query);
            $stmt->bindParam(':product_id', $product_id, PDO::PARAM_INT);
            $stmt->execute();
            $tags = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $totalTags = count($tags);
            foreach ($tags as $index => $tag) {?> 
            <span><?php echo htmlspecialchars($tag['name_']);
                    if($index < $totalTags -1 ){
                        echo ', ';
                    }
                   ?></span>
            <?php }?>
      </td>
      <td>
      <input type="hidden" name="id" id="id">

        <button type="submit" value="<?= $row['id']?>" class="edit_button" >
        <i class="edit icon"></i>
        <p><?php echo $row['id']?></p>
        </button>
      
        <a  class="delete_button" href="">
        <i class="trash icon"></i>
        </a>
      </td>
    </tr>
    <?php }}else {?>
        <tr>
            <td colspan="9" style="text-align: center;">Product not found</td>
        </tr>
        <?php }?>
  </tbody>
</table>
</div>
<div class="pagination_box">    
<div class="ui pagination menu">

      <?php  
                echo "</br>";
        $query = "SELECT COUNT(*) FROM products"; 
        $count_stmt = $pdo->prepare($query);
        $count_stmt->execute();   
        $total_records = $count_stmt->fetchColumn();
          
    echo "</br>";     
        // Number of pages required.   
        $total_pages = ceil($total_records / $per_page_record);  

        $pagLink = "";       
      
        if($page>=2){   
            echo "<a class='item' href='index.php?page=".($page-1)."'>  Prev </a>";   
        }else{
            echo "<a class='item disabled'> Prev </a>";

        }       
                   
        for ($i=1; $i<=$total_pages; $i++) {   
          if ($i == $page) {   
              $pagLink .= "<a class = 'item active' href='index.php?page="  
                                                .$i."'>".$i." </a>";   
          }               
          else  {   
              $pagLink .= "<a class='item' href='index.php?page=".$i."'>   
                                                ".$i." </a>";     
          }   
        };     
        echo $pagLink;   
  
        if($page<$total_pages){   
            echo "<a class='item' href='index.php?page=".($page+1)."'>  Next </a>";   
        }   else {
            echo "<a class='item disabled'> Next </a>"; 
        }
  
      ?>    
      </div>  
      </div>  


</section>
<script src="./jquery/my_jquery_functions.js">
    
</script>


</body>
</html>