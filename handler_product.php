<?php 
require_once 'includes/db.inc.php';
require_once './includes/functions.php';


$categoryQuery = "SELECT id, name_ FROM property WHERE type_ = 'category'";
$categoryStmt = $pdo->prepare($categoryQuery);
$categoryStmt->execute();
$categories = $categoryStmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch tags from database
$tagQuery = "SELECT id, name_ FROM property WHERE type_ = 'tag'";
$tagStmt = $pdo->prepare($tagQuery);
$tagStmt->execute();
$tags = $tagStmt->fetchAll(PDO::FETCH_ASSOC);




if (isset($_POST['save_product'])) {

    $selected_categories = isset($_POST['categories']) ? json_decode($_POST['categories'], true) : [];
    $selected_tags = isset($_POST['tags']) ? json_decode($_POST['tags'], true) : [];

    
    $product_name = test_input($_POST['product_name']);
    $sku = test_input($_POST['sku']);
    $price = test_input($_POST['price']);
    $featured_image = $_FILES['featured_image'];
    $gallery_images = $_FILES['gallery'];
  



    if(!isValidInput($product_name) && !empty($product_name)){
        $errors[] = [
            'field' => 'product_name',
            'message' => "don't allow special character"
        ];
    }
    
    if(!isValidInput($sku) && !empty($sku)){
        $errors[] = [
            'field' => 'sku',
            'message' => "don't allow special character"
        ];
    }
    if(!isValidNumberWithDotInput($price) && !empty($price)){
        $errors[] = [
            'field' => 'price',
            'message' => 'just allow number'
        ];
    }

    if(!empty($errors)){
        $res = [
            'status' => '400',
            'errors' => $errors
        ];
        echo json_encode($res);
        return;
    }
    

//fetured image
    if(empty($product_name) || empty($sku) || empty($price) || $featured_image['error'] === UPLOAD_ERR_NO_FILE){
        $res = [
            'status' => 422,
            'message' => ' At least one field is required.'
        ];
        echo json_encode($res);
        return;
     }

     $errors = [];

     if (isset($_FILES['featured_image']) ) {
        $featured_image = $_FILES['featured_image'];
    
        // Check if there was an error during upload
        if ($featured_image['error'] === UPLOAD_ERR_OK) {
    
            $file_name = $featured_image['name'];
            move_uploaded_file($featured_image['tmp_name'], 'uploads/' . $file_name);

            $product_id = insert_product($pdo, $product_name,$sku, $price, $file_name);
           

            }else{
                echo "Error occurred during featured image upload. Error Code: " . $featured_image['error'];
            }


            if($gallery_images['error'] !== UPLOAD_ERR_NO_FILE){
                foreach ($gallery_images['error'] as $key => $error) {
                    if ($error === UPLOAD_ERR_OK) {
                        $gallery_file_name = $gallery_images['name'][$key];
                        move_uploaded_file($gallery_images['tmp_name'][$key], 'uploads/' . $gallery_file_name);
            
                        // Insert the gallery image into the property table
                       $property_id = insert_property($pdo, 'gallery', $gallery_file_name);
            
                        // Link the gallery image with the product
                        add_product_property($pdo, $product_id, $property_id);
            
                       
                    } 
                }
            }

            if (!empty($product_id)) {


                

                // if(empty($checked_tag)){
                //     $query = " DELETE product_property 
                //     FROM product_property 
                //     JOIN property ON product_property.property_id = property.id 
                //     WHERE product_property.product_id = :product_id 
                //     AND property.type_ = 'tag'";
                //     $stmt = $pdo->prepare($query);
                //     $stmt->execute(['product_id' => $productId]);
                // }
            
                // Check if $selected_categories is not empty before executing the delete query
                if (!empty($selected_categories)) {
                    $query = "DELETE pp FROM product_property pp
                        JOIN property p ON pp.property_id = p.id
                        WHERE pp.product_id = :product_id AND p.type_ = 'category'";
                    $stmt = $pdo->prepare($query);
                    $stmt->bindParam(':product_id', $product_id);
                    $stmt->execute();
                } else {
                    $query = "DELETE pp FROM product_property pp
                        JOIN property p ON pp.property_id = p.id
                        WHERE pp.product_id = :product_id AND p.type_ = 'category'";
                        $stmt = $pdo->prepare($query);
                        $stmt->bindParam(':product_id', $product_id);
                        $stmt->execute();
                }
            
                 // Check if $selected_categories is not empty before executing the delete query
                 if (!empty($selected_tags)) {
                    $query = "DELETE pp FROM product_property pp
                        JOIN property p ON pp.property_id = p.id
                        WHERE pp.product_id = :product_id AND p.type_ = 'tag'";
                    $stmt = $pdo->prepare($query);
                    $stmt->bindParam(':product_id', $product_id);
                    $stmt->execute();
                } else {
                    $query = "DELETE pp FROM product_property pp
                    JOIN property p ON pp.property_id = p.id
                    WHERE pp.product_id = :product_id AND p.type_ = 'tag'";
                    $stmt = $pdo->prepare($query);
                    $stmt->bindParam(':product_id', $product_id);
                    $stmt->execute();
                }
            
                                // Check if categories were selected
                if (!empty($selected_categories)) {
                    // Delete existing categories related to the product
                    $query = "DELETE pp FROM product_property pp
                        JOIN property p ON pp.property_id = p.id
                        WHERE pp.product_id = :product_id AND p.type_ = 'category'";
                    $stmt = $pdo->prepare($query);
                    $stmt->bindParam(':product_id', $product_id);
                    $stmt->execute();

                    // Insert new categories
                    $query = "INSERT INTO product_property (product_id, property_id) VALUES (:product_id, :property_id)";
                    $stmt = $pdo->prepare($query);
                    foreach ($selected_categories as $category) {
                        // Ensure category ID is valid
                        if (!empty($category[0])) {
                            $stmt->execute([
                                'product_id' => $product_id,
                                'property_id' => $category[0] // Access the category ID inside the array
                            ]);
                        }
                    }
                }

                // Check if tags were selected
                if (!empty($selected_tags)) {
                    // Delete existing tags related to the product
                    $query = "DELETE pp FROM product_property pp
                        JOIN property p ON pp.property_id = p.id
                        WHERE pp.product_id = :product_id AND p.type_ = 'tag'";
                    $stmt = $pdo->prepare($query);
                    $stmt->bindParam(':product_id', $product_id);
                    $stmt->execute();

                    // Insert new tags
                    $query = "INSERT INTO product_property (product_id, property_id) VALUES (:product_id, :property_id)";
                    $stmt = $pdo->prepare($query);
                    foreach ($selected_tags as $tag) {
                        // Ensure tag ID is valid
                        if (!empty($tag[0])) {
                            $stmt->execute([
                                'product_id' => $product_id,
                                'property_id' => $tag[0] // Access the tag ID inside the array
                            ]);
                        }
                    }
                }


                    

            }

           
        } else {
            echo "Error occurred during file upload. Error Code: " . $featured_image['error'];
        }
}


if (isset($_GET['product_id'])) {
    $productId = $_GET['product_id'];

    

    // Truy vấn lấy thông tin sản phẩm theo id
    $query = "SELECT * FROM products WHERE id = :productId";
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':productId', $productId, PDO::PARAM_INT); 
    $stmt->execute();

    if ($stmt->rowCount() > 0) {
        // Fetch product data
        $product = $stmt->fetch(PDO::FETCH_ASSOC);

        // Fetch gallery images from 'property' table based on product_id
        $galleryImages = [];
        $galleryQuery = "SELECT name_ FROM property WHERE type_ = 'gallery' AND id IN (SELECT property_id FROM product_property WHERE product_id = :productId)";
        $galleryStmt = $pdo->prepare($galleryQuery);
        $galleryStmt->bindParam(':productId', $productId, PDO::PARAM_INT);
        $galleryStmt->execute();

        // Fetch all gallery images into an array
        while ($row = $galleryStmt->fetch(PDO::FETCH_ASSOC)) {
            $galleryImages[] = $row['name_'];  // Assuming 'name_' contains the image file name
        }

        $categories = [];
        $categoryQuery = "SELECT name_ FROM property WHERE type_ = 'category' AND id IN (SELECT property_id FROM product_property WHERE product_id = :productId)";
        $categoryStmt = $pdo->prepare($categoryQuery);
        $categoryStmt->bindParam(':productId', $productId, PDO::PARAM_INT);
        $categoryStmt->execute();

        // Fetch all categories into an array
        while ($row = $categoryStmt->fetch(PDO::FETCH_ASSOC)) {
            $categories[] = $row['name_'];  // Assuming 'name_' contains the category name
        }

        $tags = [];
        $tagQuery = "SELECT name_ FROM property WHERE type_ = 'tag' AND id IN (SELECT property_id FROM product_property WHERE product_id = :productId)";
        $tagStmt = $pdo->prepare($tagQuery);
        $tagStmt->bindParam(':productId', $productId, PDO::PARAM_INT);
        $tagStmt->execute();

        // Fetch all tags into an array
        while ($row = $tagStmt->fetch(PDO::FETCH_ASSOC)) {
            $tags[] = $row['name_'];  // Assuming 'name_' contains the tag name
        }

        // Prepare response
        $res = [
            'status' => 200,
            'message' => 'Product fetched successfully by ID',
            'data' => [
                'product_id' => $product['id'],
                'product_name' => $product['product_name'],
                'sku' => $product['sku'],
                'price' => $product['price'],
                'featured_image' => $product['featured_image'],  
                'gallery' => $galleryImages,  
                'categories' => $categories,  
                'tags' => $tags  
            ]
        ];

    } else {
        $res = [
            'status' => 404,
            'message' => 'Product ID not found'
        ];
        echo json_encode($res);
    }

}

$res = [
    'status' => 200,
    'categories' => $categories,
    'tags' => $tags,
];

echo json_encode($res);
return false;
?>














