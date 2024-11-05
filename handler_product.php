<?php 
require_once 'includes/db.inc.php';
require_once './includes/functions.php';





if (isset($_POST['save_product'])) {

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


            foreach ($gallery_images['error'] as $key => $error) {
                if ($error === UPLOAD_ERR_OK) {
                    $gallery_file_name = $gallery_images['name'][$key];
                    move_uploaded_file($gallery_images['tmp_name'][$key], 'uploads/' . $gallery_file_name);
        
                    // Insert the gallery image into the property table
                   $property_id = insert_property($pdo, 'gallery', $gallery_file_name);
        
                    // Link the gallery image with the product
                    add_product_property($pdo, $product_id, $property_id);
        
                    $res = [
                        'status' => 200,
                    ];
        
                    echo json_encode($res);
                    return;
                    echo "<strong>Gallery Image Uploaded:</strong> " . htmlspecialchars($gallery_file_name) . "<br>";
                } else {
                    echo "Error occurred during gallery image upload. Error Code: " . $error;
                }
            }

            echo "<strong>File Name:</strong> " . htmlspecialchars($featured_image['name']) . "<br>";
            
        } else {
            echo "Error occurred during file upload. Error Code: " . $featured_image['error'];
        }
    }












