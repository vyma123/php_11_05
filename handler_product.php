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
    $editId = isset($_POST['id']) && !empty($_POST['id']) ? $_POST['id'] : null;


   if($editId = isset($_POST['id']) ? $_POST['id'] : null){

        $product_name = test_input($_POST['product_name']);
        $sku = test_input($_POST['sku']);
        $price = test_input($_POST['price']);
        $selected_categories = isset($_POST['categories']) ? json_decode($_POST['categories'], true) : [];
        $selected_tags = isset($_POST['tags']) ? json_decode($_POST['tags'], true) : [];
    
    
        $featured_image_src = isset($_POST['featured_image_src']) ? $_POST['featured_image_src'] : null;
        $gallery_images_src = isset($_POST['gallery_images_src']) ? json_decode($_POST['gallery_images_src'], true) : [];
    
    
        // if ($featured_image_src) {
        //     // Process the image source, for example, store it in the database or handle it as needed
        //     echo "Featured Image Source: " . $featured_image_src; // You can process it further
        // }
    
        // if (!empty($gallery_images_src)) {
        //     echo "Gallery Images Sources: <br>";
        //     foreach ($gallery_images_src as $image_src) {
        //         echo $image_src . "<br>";
        //     }
        // }

    
        $res = [
        'action' => 'edit',

        ];
        echo 'edit success'.$editId;
        echo json_encode($res);
        return;
    }
else{
    // Decode the selected categories and tags arrays
    $selected_categories = isset($_POST['categories']) ? json_decode($_POST['categories'], true) : [];
    $selected_tags = isset($_POST['tags']) ? json_decode($_POST['tags'], true) : [];

    // Prepare product data
    $product_name = test_input($_POST['product_name']);
    $sku = test_input($_POST['sku']);
    $price = test_input($_POST['price']);
    $featured_image = $_FILES['featured_image'];
    $gallery_images = $_FILES['gallery'];
    $errors = [];
    $responses = [];

    // Validate and check required fields
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

    if(empty($product_name) || empty($sku) || empty($price) || $featured_image['error'] === UPLOAD_ERR_NO_FILE){
        $res = [
            'status' => 422,
            'message' => ' At least one field is required.'
        ];
        echo json_encode($res);
        return;
     }


    // Upload featured image and insert product

    if ($featured_image['error'] === UPLOAD_ERR_OK) {
        $file_name = $featured_image['name'];
        move_uploaded_file($featured_image['tmp_name'], 'uploads/' . $file_name);

        // Insert product into the database
        $product_id = insert_product($pdo, $product_name, $sku, $price, $file_name);
        if (!$product_id) {
            echo json_encode(['status' => 500, 'message' => 'Failed to insert product.']);
            return;
        }
    }


    if (!empty($selected_categories) && is_array($selected_categories[0])) {
        $selected_categories = $selected_categories[0];
    }
    
    $categoryStmt = $pdo->prepare("INSERT INTO product_property (product_id, property_id) VALUES (:product_id, :property_id)");
    
    foreach ($selected_categories as $category) {
        $categoryStmt->execute([
            ':product_id' => $product_id,
            ':property_id' => $category
        ]);
    }
    
    $responses[] = ['status' => 200, 'message' => 'Categories added successfully.'];
    

    if (!empty($selected_tags) && is_array($selected_tags[0])) {
        $selected_tags = $selected_tags[0];
    }
    
    // Insert selected tags for the product
        $tagStmt = $pdo->prepare("INSERT INTO product_property (product_id, property_id) VALUES (:product_id, :property_id)");
        foreach ($selected_tags as $tag) {
                $tagStmt->execute([
                    ':product_id' => $product_id,
                    ':property_id' => $tag
                ]);
        }
        $responses[] = ['status' => 200, 'message' => 'Tags added successfully.'];

    // Handle gallery images
    if (!empty($gallery_images['name'][0])) { // Check if at least one gallery image is selected
        foreach ($gallery_images['error'] as $key => $error) {
            if ($error === UPLOAD_ERR_OK) {
                $gallery_file_name = $gallery_images['name'][$key];
                move_uploaded_file($gallery_images['tmp_name'][$key], 'uploads/' . $gallery_file_name);

                // Insert gallery image and link to product
                $property_id = insert_property($pdo, 'gallery', $gallery_file_name);
                add_product_property($pdo, $product_id, $property_id);
                $responses[] = [
                    'status' => 200,
                    'message' => 'Gallery image ' . $gallery_file_name . ' uploaded successfully.'
                ];
            }
        }
    }

    echo json_encode(['status' => 200, 'responses' => $responses, 'action' => 'add']);
    return;
}
}


if (isset($_GET['product_id'])) {
    $product_id = (int)$_GET['product_id'];


    // Fetch product details
    $query = "SELECT * FROM products WHERE id = :product_id";
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':product_id', $product_id, PDO::PARAM_INT);
    $stmt->execute();

    if ($stmt->rowCount() > 0) {
        $product = $stmt->fetch(PDO::FETCH_ASSOC);  // Fetch the product details

        // Fetch categories associated with the product
        $categoryQuery = "SELECT id, name_ FROM property WHERE type_ = 'category'";
        $categoryStmt = $pdo->prepare($categoryQuery);
        $categoryStmt->execute();
        $categories = $categoryStmt->fetchAll(PDO::FETCH_ASSOC);

        $categorySelected = "SELECT p.name_ FROM product_property pp
                    JOIN property p ON pp.property_id = p.id
                    WHERE pp.product_id = :product_id AND p.type_ = 'category'";
        $stmt = $pdo->prepare($categorySelected);
        $stmt->bindParam(':product_id', $product_id, PDO::PARAM_INT);
        $stmt->execute();
        $categoriesse = $stmt->fetchAll(PDO::FETCH_ASSOC);

        
        // Fetch tags from database
        $tagQuery = "SELECT id, name_ FROM property WHERE type_ = 'tag'";
        $tagStmt = $pdo->prepare($tagQuery);
        $tagStmt->execute();
        $tags = $tagStmt->fetchAll(PDO::FETCH_ASSOC);

        
        $tagSelected = "SELECT p.name_ FROM product_property pp
                    JOIN property p ON pp.property_id = p.id
                    WHERE pp.product_id = :product_id AND p.type_ = 'tag'";
        $stmt = $pdo->prepare($tagSelected);
        $stmt->bindParam(':product_id', $product_id, PDO::PARAM_INT);
        $stmt->execute();
        $tagsse = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $galleryQuery = "SELECT p.name_ FROM product_property pp
                    JOIN property p ON pp.property_id = p.id
                    WHERE pp.product_id = :product_id AND p.type_ = 'gallery'";
        $galleryStmt = $pdo->prepare($galleryQuery);
        $galleryStmt->bindParam(':product_id', $product_id, PDO::PARAM_INT);
        $galleryStmt->execute();
        $gallery = $galleryStmt->fetchAll(PDO::FETCH_ASSOC);


        // Combine all data into a response array
        $res = [
            'status' => 200,
            'data' => $product,
            'categories' => $categories,
            'tags' => $tags,
            'gallery' => $gallery,
            'categoriesse' => $categoriesse,
            'tagsse' => $tagsse,

        ];
        
    } else {
        // Product not found
        $res = [
            'status' => 404,
            'message' => 'Product not found',
        ];
    }
    
    echo json_encode($res);
}



// if (!isset($_POST['save_product']) && !isset($_GET['product_id'])) {
//     $res = [
//         'categories' => $categories,
//         'tags' => $tags,
//     ];
//     echo json_encode($res);
//     return;
// }




?>














