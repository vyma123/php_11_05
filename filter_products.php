<!-- filter_products.php -->
<?php
require_once 'includes/db.inc.php';

$searchTerm = isset($_GET['search']) ? test_input($_GET['search']) : '';
$sort_by = $_GET['sort_by'] ?? 'id';
$order = $_GET['order'] ?? 'ASC';
$category = $_GET['category'] ?? 0;
$tag = $_GET['tag'] ?? 0;
$gallery = $_GET['gallery'] ?? '';  
$date_from = $_GET['date_from'] ?? null;
$date_to = $_GET['date_to'] ?? null;
$price_from = $_GET['price_from'] ?? null;
$price_to = $_GET['price_to'] ?? null;



$query = "
SELECT products.*, 
    GROUP_CONCAT(DISTINCT p_tags.name_ SEPARATOR ', ') AS tags, 
    GROUP_CONCAT(DISTINCT p_categories.name_ SEPARATOR ', ') AS categories,
    GROUP_CONCAT(DISTINCT g_images.name_ SEPARATOR ', ') AS gallery_images
FROM products
LEFT JOIN product_property pp_tags ON products.id = pp_tags.product_id
LEFT JOIN property p_tags ON pp_tags.property_id = p_tags.id AND p_tags.type_ = 'tag'
LEFT JOIN product_property pp_categories ON products.id = pp_categories.product_id
LEFT JOIN property p_categories ON pp_categories.property_id = p_categories.id AND p_categories.type_ = 'category'
LEFT JOIN product_property pp_gallery ON products.id = pp_gallery.product_id
LEFT JOIN property g_images ON pp_gallery.property_id = g_images.id AND g_images.type_ = 'gallery'
WHERE products.product_name LIKE :search_term
";

if ($category != 0) {
    $query .= " AND pp_categories.property_id = :category_id";
}

if ($tag != 0) {
    $query .= " AND pp_tags.property_id = :tag_id";
}

if (!empty($gallery)) {
    $query .= " AND g_images.name_ LIKE :gallery"; 
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
  

  
    $galleryImages = $row['gallery_images'];
    if (!empty($galleryImages)) {
        $galleryImagesArray = explode(', ', $galleryImages);
        ?>
<td>
    <?php 
        foreach ($galleryImagesArray as $image) {
            echo "<img height='30' src='./uploads/" . htmlspecialchars($image) . "'>";
        }
        ?>
        </td>
        <?php
    } else {
        echo "<td>No gallery images</td>";
    }
    echo "<td>" . htmlspecialchars($row['categories']) . "</td>";

    echo "<td>" . htmlspecialchars($row['tags']) . "</td>";

    echo "<td>
    <input type='hidden' name='id' value='" . htmlspecialchars($row['id']) . "'>
    <button  type='submit' value='" . htmlspecialchars($row['id']) . "' class='edit_button'>
        <i class='edit icon'></i>
    </button>
    <a class='delete_button' href=''>
        <i class='trash icon'></i>
    </a>
    </td>";


    echo "</tr>";
}
?>
<script src="jquery/my_jquery_functions.js"></script>