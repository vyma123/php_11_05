
$(function(){
	$("#add_product").click(function(){
		$(".product_box").modal('show');
		
		$('#saveProduct')[0].reset(); 


		$('#errMessage_add').addClass('d-none');
		$('#err_valid_Message_product').addClass('d-none');
		$('#okMessage_product').addClass('d-none');
		$('#err_valid_Message_price').addClass('d-none');

		$('#uploadedImage').hide();
		$('#galleryPreviewContainer').hide();

		$('#featured_image').val('');
		$('#gallery').val('');
	});

	$(".product_box").modal({
		closable: true
	});
});

$(function(){
	$("#close_product").click(function(){
		$(".product_box").modal('hide');
	});
	$(".product_box").modal({
		closable: true
	});
});  

$(function(){
	$("#close_product").click(function(){
		$(".product_box").modal('hide');
	});
	$(".product_box").modal({
		closable: true
	});
});  

$('#featured_image').on('change', function() {
	if (this.files && this.files[0]) {
		var reader = new FileReader(); 
		reader.onload = function(e) {
			$('#uploadedImage').attr('src', e.target.result).show(); 
		};
		reader.readAsDataURL(this.files[0]); 
	}
});

$('#gallery').on('change', function() {
    $('#galleryPreviewContainer').empty();
    
    if (this.files) {
        for (let i = 0; i < this.files.length; i++) {
            let file = this.files[i];
            let reader = new FileReader();
            
            reader.onload = function(e) {
                const img = $('<img>', {
                    src: e.target.result,
                    alt: 'Gallery Image',
                    style: 'height: 80px;'
                });
                $('#galleryPreviewContainer').append(img);
                $('#galleryPreviewContainer').show();
            };
            reader.readAsDataURL(file); 
        }
    }
});



document.getElementById('add_product').addEventListener('click', function() {
    // Open the modal
    $('.ui.modal.product_box').modal('show');


    // Fetch categories and tags via AJAX
    fetch('handler_product.php')
        .then(response => response.json())
        .then(data => {
            const categoriesSelect = document.getElementById('categories_select');
            const tagsSelect = document.getElementById('tags_select');

            // Clear existing options
            categoriesSelect.innerHTML = '';
            tagsSelect.innerHTML = '';

            // Populate categories
            data.categories.forEach(category => {
                const option = document.createElement('option');
                option.value = category.id;
                option.textContent = category.name_;
                categoriesSelect.appendChild(option);
            });

            // Populate tags
            data.tags.forEach(tag => {
                const option = document.createElement('option');
                option.value = tag.id;
                option.textContent = tag.name_;
                tagsSelect.appendChild(option);
            });
        })
        .catch(error => console.error('Error fetching categories and tags:', error));
});

$(document).on('click', '#addProductButton', function() {
    $('#action_type').val('add_product');  // Set action type to add
});

$(document).on('click', '#editProductButton', function() {
    $('#action_type').val('edit_product');  // Set action type to edit
});

$(document).on('click', '.edit_button', function() {
    var productId = $(this).val(); // Get the product ID from the button's value
    $('#action_type').val('edit_product'); // Set action_type to edit
    $('#saveProduct').data('product-id', productId); // Store product ID in the form for later use
    $('.ui.modal.product_box').modal('show'); // Show the modal
});


$(document).on('click', '.edit_button', function() {
    var productId = $(this).val(); // Get the product ID from the button's value
    $('#action_type').val('edit_product'); // Set action_type to edit
    $('#product_id').val(productId); // Set the product ID in the hidden field
    $('.ui.modal.product_box').modal('show'); // Show the modal
});

$('#add_product').click(function() {
    // Show the #editProductButton when #add_product is clicked

    $('#editProductButton').css({
        'display': 'none',  
      
    });
	$('#addProductButton').css({
        'display': 'block',  
      
    });
});

$('.edit_button').click(function() {
    // Show the #editProductButton when #add_product is clicked

    $('#addProductButton').css({
        'display': 'none',  
      
    });
	
    $('#editProductButton').css({
        'display': 'block',  
      
    });
});




$(document).on('submit', '#saveProduct', function(e){
	e.preventDefault();

	var formData = new FormData(this);

	if ($('#featured_image')[0].files.length > 0) {
        var file = $('#featured_image')[0].files[0]; // Lấy ảnh đã chọn từ input
        formData.append('featured_image', file); // Thêm ảnh vào formData
    }

	// if ($('#gallery')[0].files.length > 0) {
    //     $.each($('#gallery')[0].files, function(i, file) {
    //         formData.append('gallery[]', file); // Append each file with 'gallery[]' name
    //     });
    // }
	 
    formData.append("action_type", $('#action_type').val());

	if ($('#action_type').val() === 'edit_product') {
		var productId = $(this).data('product-id'); 
        console.log("Product ID:", productId); 
    }
	


	var categories = [];
    var tags = [];

    $("select[name='categories[]']").each(function() {
      categories.push($(this).val());
    });

    $("select[name='tags[]']").each(function() {
      tags.push($(this).val());
    });

	


    formData.append('categories', JSON.stringify(categories));
    formData.append('tags', JSON.stringify(tags));

	$.ajax({
		type: "POST",
		url: "handler_product.php",
		data: formData,
		dataType: "",
		processData:false,
		contentType:false,
		success: function(response) {
            var res = jQuery.parseJSON(response);
			$('#okMessage').addClass('d-none'); 
			$('#okMessage_add').addClass('d-none'); 
            $('#errMessage').addClass('d-none'); 
            $('#err_valid_Message').addClass('d-none'); 
            $('#err_valid_Message_product').addClass('d-none'); 
			$('#product_name').removeClass('err_border'); 
            $('#sku').removeClass('err_border');
            $('#price').removeClass('err_border');

            if (res.status == 400) {
				
				setTimeout(function() {
					$('#err_valid_Message_product').fadeOut(400, function() {
						$(this).addClass('d-none');
					});
				}, 3500);
				setTimeout(function() {
					$('#err_valid_Message_price').fadeOut(400, function() {
						$(this).addClass('d-none');
					});
				}, 2500);
			
				
				
				res.errors.forEach(function(error) {

				if (error.field === 'empty') {
					$('#errMessage_add').removeClass('d-none').fadeIn(400); 
					setTimeout(function() {
						$('#errMessage_add').fadeOut(400, function() {
							$(this).addClass('d-none');
						});
					}, 2500);
				}
				if (error.field === 'empty') {
					$('#err_valid_Message_sku').removeClass('d-none').fadeIn(400); 
				setTimeout(function() {
					$('#err_valid_Message_sku').fadeOut(400, function() {
						$(this).removeClass('d-none');
					});
				}, 2500);
			}
	
				if (error.field === 'product_name') {
					$('#err_valid_Message_product').removeClass('d-none').fadeIn(400);
					$('#product_name').addClass('err_border');
				} 
				if (error.field === 'sku') {
					$('#err_valid_Message_product').removeClass('d-none').fadeIn(400);
					$('#sku').addClass('err_border');
				}
				if (error.field === 'price') {
					$('#err_valid_Message_price').removeClass('d-none').fadeIn(400);
					$('#price').addClass('err_border');
				}
				if (error.field === 'featured_image') {
					$('#err_valid_Message_product').removeClass('d-none').fadeIn(400);
					$('#featured_image').addClass('err_border');
				}
				if (error.field === 'exist') {
					$('#err_valid_Message_sku').removeClass('d-none').fadeIn(400);
					$('#sku').addClass('err_border');
				}
				
			});
			
			}
			else if (res.status == 200) {
				if(res.action == 'add'){

					$('#okMessage_product').removeClass('d-none').fadeIn(400); 
					$('#uploadedImage').attr('src', '').hide();
					$('#featured_image').val(''); 
					$('#galleryPreviewContainer').empty();
					$('#saveProduct')[0].reset();
					$('#tableID').load(location.href + " #tableID");
					
					setTimeout(function() {
						$('#okMessage_product').fadeOut(400, function() {
							$(this).addClass('d-none');
						});
					}, 2000);
				}else if(res.action == 'edit'){

					$('#featured_image').val(''); 
					$('#okMessage_product_update').removeClass('d-none').fadeIn(400); 
					$('#tableID').load(location.href + " #tableID");
					$('.pagination_box').load(location.href + " .pagination_box");
					$('#gallery').val('');

					setTimeout(function() {
						$('#okMessage_product_update').fadeOut(400, function() {
							$(this).addClass('d-none');
						});
					}, 2000);
	
				}

            }
		}
	});
})





//end add product

$(document).on('click', '.edit_button', function(e) {
    e.preventDefault();

    var product_id = $(this).val();
    console.log(product_id);
	
	$('.ui.button[type="submit"]:contains("Add")').addClass('d-none'); 
    $('.ui.button[type="submit"]:contains("Update")').removeClass('d-none'); 

    $.ajax({
        type: "GET",
        url: "handler_product.php?product_id=" + product_id,
        dataType: "json", 
        success: function(res) {
			$('.ui.modal.product_box').modal('show');

            if(res.status == 422){
                alert(res.message);
				
            } else if(res.status == 200){				

                // Populate product details
                $('#product_id').val(res.data.id);
                $('#product_name').val(res.data.product_name);
                $('#sku').val(res.data.sku);
                $('#price').val(res.data.price);


				$('#uploadedImage').show();
				$('#okMessage_product').hide();
				$('#galleryPreviewContainer').show();
				
                $('#uploadedImage').attr('src', './uploads/' + res.data.featured_image); 
			
                $('#galleryPreviewContainer').empty();

				$.each(res.gallery, function(index, image) {
					var imagePath = './uploads/' + image.name_;  
				
				
					var imgElement = $('<img>')
						.attr('src', imagePath)
						.attr('alt', 'Gallery Image')
						.css('height', '80px');  
				
					$('#galleryPreviewContainer').append(imgElement);
				});
				
				$('#categories_select').empty();


				$.each(res.categories, function(index, category) {
					var option = $('<option></option>')
						.attr('value', category.id)  
						.text(category.name_);  
				 
						console.log(category.name_);
						
					$('#categories_select').append(option);
					
						$.each(res.categoriesse, function(i, selectedCategory) {
							if (selectedCategory.name_ === category.name_) {
								$('#categories_select option[value="' + category.id + '"]').prop('selected', true);
							}
						});
				});
				
				$('#tags_select').empty();
				

				$.each(res.tags, function(index, tag) {
					var option = $('<option></option>')
						.attr('value', tag.id)  
						.text(tag.name_);  
				
					$('#tags_select').append(option);
				
					$.each(res.tagsse, function(i, selectedTag) {
						if (selectedTag.name_ === tag.name_) {
							$('#tags_select option[value="' + tag.id + '"]').prop('selected', true);
						}
					});
				});

				




            }
        }
    });
});


$("#close_product").click(function() {
	$(".product_box").modal('hide');
});



///////////////////////////////////////////////////////////////////////////////////////////////////////////

//start add property
$(function(){
	$("#add_property").click(function(){
		$(".category_box").modal('show');
	});
	$(".category_box").modal({
		closable: true
	});
});   
$(function(){
	$("#close_property").click(function(){
		$(".category_box").modal('hide');
	});
	$(".category_box").modal({
		closable: true
	});
});  


$(document).on('submit', '#saveProperty', function(e){
	e.preventDefault();

	var formData = new FormData(this);
	formData.append("save_property", true);

	$.ajax({
		type:"POST",
		url: "handler_property.php",
		data: formData,
		processData:false,
		contentType:false,
		success: function(response) {
            var res = jQuery.parseJSON(response);
			$('#okMessage').addClass('d-none'); 
            $('#errMessage').addClass('d-none'); 
            $('#err_valid_Message').addClass('d-none'); 
            $('#input_cate').removeClass('err_border'); 
            $('#input_tag').removeClass('err_border'); 

			console.log(formData); // Log the serialized form data

            if (res.status == 422) {
				$('#errMessage').removeClass('d-none').fadeIn(400); 
                setTimeout(function() {
                    $('#errMessage').fadeOut(400, function() {
                        $(this).addClass('d-none');
                    });
                }, 3500);
            }else if (res.status == 400) {

				$('#err_valid_Message').removeClass('d-none').fadeIn(400);
				
				setTimeout(function() {
					$('#err_valid_Message').fadeOut(400, function() {
						$(this).addClass('d-none');
					});
				}, 3500);
               res.errors.forEach(function(error) {

				if (error.field === 'category') {
					$('#input_cate').addClass('err_border');
				} 
				if (error.field === 'tag') {
					$('#input_tag').addClass('err_border');
				}
			});
			}else if (res.status == 200) {
				$('#okMessage').removeClass('d-none').fadeIn(400); 
                $('#saveProperty')[0].reset();
				$('#load_property').load(location.href + " #load_property");

                setTimeout(function() {
                    $('#okMessage').fadeOut(400, function() {
                        $(this).addClass('d-none');
                    });
                }, 3500);
            }
		}
	})
})
//end add property


//pagination

//filter_search
function applyFilters(event) {
	event.preventDefault();
	
	const search = document.getElementById("search").value;
	const sortBy = document.getElementById("sort_by").value;
	const order = document.getElementById("order").value;
	const category = document.getElementById("category").value;
	const tag = document.getElementById("tag").value;
	const dateFrom = document.getElementById("date_from").value;
	const dateTo = document.getElementById("date_to").value;
	const priceFrom = document.getElementById("price_from").value;
	const priceTo = document.getElementById("price_to").value;
	const gallery = document.getElementById("gallery").value;  
	
	$.ajax({
	  url: 'filter_products.php', 
	  type: 'GET',
	  data: {
		search: search,
		sort_by: sortBy,
		order: order,
		category: category,
		tag: tag,
		date_from: dateFrom,
		date_to: dateTo,
		price_from: priceFrom,
		price_to: priceTo,
		gallery: gallery  
	  },
	  success: function(response) {
		$('#productTableBody').html(response); 
	  },
	  error: function(error) {
		console.error("Error loading data:", error);
	  }
	});
  }
  
  function isNumber(evt) {
    evt = (evt) ? evt : window.event;
    var charCode = (evt.which) ? evt.which : evt.keyCode;
    var inputValue = evt.target.value;

    // Kiểm tra nếu ký tự không phải là số (48-57) hoặc dấu chấm (46)
    if (charCode > 31 && (charCode < 48 || charCode > 57) && charCode !== 46) {
        return false;  // Chặn các ký tự không phải là số và dấu chấm
    }

    // Kiểm tra nếu đã có dấu chấm trong input, nếu có thì không cho phép thêm dấu chấm
    if (charCode === 46 && inputValue.indexOf('.') !== -1) {
        return false;  // Chặn dấu chấm nếu đã có trong input
    }

    return true;  // Chỉ cho phép số và một dấu chấm
}
