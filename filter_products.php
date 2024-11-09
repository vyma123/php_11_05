<?php
require_once 'includes/db.inc.php';

$searchTerm = isset($_GET['search']) ? test_input($_GET['search']) : '';
$sort_by = $_GET['sort_by'] ?? 'id';
$order = $_GET['order'] ?? 'ASC';
$category = $_GET['category'] ?? 0;
$tag = $_GET['tag'] ?? 0;
$gallery = $_GET['gallery'] ?? '';  // Capture gallery filter
$date_from = $_GET['date_from'] ?? null;
$date_to = $_GET['date_to'] ?? null;
$price_from = $_GET['price_from'] ?? null;
$price_to = $_GET['price_to'] ?? null;

// Base query
$query = "
SELECT products.*, 
    GROUP_CONCAT(DISTINCT p_tags.name_ SEPARATOR ', ') AS tags, 
    GROUP_CONCAT(DISTINCT p_categories.name_ SEPARATOR ', ') AS categories
FROM products
LEFT JOIN product_property pp_tags ON products.id = pp_tags.product_id
LEFT JOIN property p_tags ON pp_tags.property_id = p_tags.id AND p_tags.type_ = 'tag'
LEFT JOIN product_property pp_categories ON products.id = pp_categories.product_id
LEFT JOIN property p_categories ON pp_categories.property_id = p_categories.id AND p_categories.type_ = 'category'
WHERE products.product_name LIKE :search_term
";

// Additional filters
if ($category != 0) {
    $query .= " AND pp_categories.property_id = :category_id";
}

if ($tag != 0) {
    $query .= " AND pp_tags.property_id = :tag_id";
}

if (!empty($gallery)) {
    // Assuming gallery is a string with comma-separated image names or IDs
    $query .= " AND products.gallery LIKE :gallery"; // Adjust gallery condition as needed
}

if (!empty($date_from)) {
    $query .= " AND products.date >= :date_from"; 
}

if (!empty($date_to)) {
    $query .= " AND products.date <= :date_to"; 
}

if (!empty($price_from)) {
    $query .= " AND products.price >= :price_from"; 
}

if (!empty($price_to)) {
    $query .= " AND products.price <= :price_to";
}

$query .= " GROUP BY products.id ORDER BY $sort_by $order";

$stmt = $pdo->prepare($query);

$searchTermLike = "%$searchTerm%";
$stmt->bindParam(':search_term', $searchTermLike, PDO::PARAM_STR);

if ($category != 0) {
    $stmt->bindParam(':category_id', $category, PDO::PARAM_INT);
}

if ($tag != 0) {
    $stmt->bindParam(':tag_id', $tag, PDO::PARAM_INT);
}

if (!empty($gallery)) {
    // If gallery is provided, bind it (use LIKE for matching the gallery name or ID)
    $galleryLike = "%$gallery%";
    $stmt->bindParam(':gallery', $galleryLike, PDO::PARAM_STR);
}

if (!empty($date_from)) {
    $stmt->bindParam(':date_from', $date_from);
}

if (!empty($date_to)) {
    $stmt->bindParam(':date_to', $date_to);
}

if (!empty($price_from)) {
    $stmt->bindParam(':price_from', $price_from);
}

if (!empty($price_to)) {
    $stmt->bindParam(':price_to', $price_to);
}

$stmt->execute();
$results = $stmt->fetchAll(PDO::FETCH_ASSOC);

foreach ($results as $row) {
    echo "<tr>";
    echo "<td>" . htmlspecialchars($row['date']) . "</td>";
    echo "<td>" . htmlspecialchars($row['product_name']) . "</td>";
    echo "<td>" . htmlspecialchars($row['sku']) . "</td>";
    echo "<td>" . htmlspecialchars($row['price']) . "</td>";
    echo "<td><img height='30' src='./uploads/" . htmlspecialchars($row['featured_image']) . "'></td>";
    
    // Add more columns as needed
    echo "</tr>";
}
?>
