<?php 

function isValidInput($input){
    return preg_match('/^[\p{L}0-9 .,–\-]+$/u', $input);
}


function isValidNumberWithDotInput($input) {
    return preg_match('/^[0-9.]+$/', $input);
}

function insert_product(object $pdo, string $product_name, string $sku, string $price, string $featured_image){
    $data = [
        'product_name' => $product_name, 
        'sku' => $sku, 
        'price' => $price, 
        'featured_image' => $featured_image, 

        ];
        
        $query = "INSERT INTO products (product_name, sku, price,featured_image, date) VALUES (:product_name, :sku, :price,:featured_image, NOW())";
        $stmt = $pdo->prepare($query);
        $stmt->bindParam(":product_name", $product_name);
        $stmt->bindParam(":sku", $sku);
        $stmt->bindParam(":price", $price);
        $stmt->bindParam(":featured_image", $featured_image);
        $stmt->execute($data);
}


function insert_property(object $pdo, string $type_, string $name_) {
    try {
        $data = [
            'type_' => $type_,
            'name_' => $name_
        ];

        $query = "INSERT INTO property (type_, name_) VALUES (:type_, :name_)";
        $stmt = $pdo->prepare($query);
        
        if ($stmt->execute($data)) {
            return true; 
        } else {
            return false; 
        }
    } catch (PDOException $e) {
        error_log($e->getMessage());
        return false; 
    }
}

function select_property($pdo, $type_){
    $query = "SELECT id,name_ FROM property WHERE type_ = :type_;";
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(":type_", $type_);
    $stmt->execute();
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    return $result;
}



function check_duplicate(object $pdo, string $type_, string $name_) {
    try {
        $query = "SELECT COUNT(*) FROM property WHERE type_ = :type_ AND name_ = :name_";
        $stmt = $pdo->prepare($query);
        $stmt->execute(['type_' => $type_, 'name_' => $name_]);
        
        return $stmt->fetchColumn() > 0; 
    } catch (PDOException $e) {
        error_log($e->getMessage());
        return false;
    }
}



?>