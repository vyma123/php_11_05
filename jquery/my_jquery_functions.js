//start add product
$(function(){
	// Show the modal when the Add Product button is clicked
	$("#add_product").click(function(){
		$(".product_box").modal('show');
		
		// Clear the form fields
		$('#saveProduct')[0].reset(); // Reset form fields

		// Hide any previously shown messages
		$('#errMessage_add').addClass('d-none');
		$('#err_valid_Message_product').addClass('d-none');
		$('#okMessage_product').addClass('d-none');
		$('#err_valid_Message_price').addClass('d-none');

		// Clear the image previews
		$('#uploadedImage').hide();
		$('#galleryImage').hide();

		// Clear file input values (important for clearing file input)
		$('#featured_image').val('');
		$('#gallery').val('');
	});

	// Initialize the modal
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
	$(".edit_button").click(function(){
		$(".product_box").modal('show');
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
	// Check if a file has been selected
	if (this.files && this.files[0]) {
		var reader = new FileReader(); // Create a FileReader to read the file
		reader.onload = function(e) {
			$('#uploadedImage').attr('src', e.target.result).show(); // Set the src to the file's data URL and show the image
		};
		reader.readAsDataURL(this.files[0]); // Read the selected file as a data URL
	}
});


$('#gallery').on('change', function() {
	// Clear previous images in the gallery preview container
	$('#galleryPreviewContainer').empty();
	
	// Loop through each selected file
	if (this.files) {
		for (let i = 0; i < this.files.length; i++) {
			let file = this.files[i];
			let reader = new FileReader();
			
			reader.onload = function(e) {
				// Create a new img element for each gallery image
				const img = $('<img>', {
					src: e.target.result,
					alt: 'Gallery Image',
					style: 'height: 80px;'
				});
				// Append the new img element to the gallery preview container
				$('#galleryPreviewContainer').append(img);
			};
			reader.readAsDataURL(file); // Read each file
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


$(document).on('submit', '#saveProduct', function(e){
	e.preventDefault();

	var formData = new FormData(this);
	formData.append("save_product", true);

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
			


            if (res.status == 422) {
				$('#errMessage_add').removeClass('d-none').fadeIn(400); 
                setTimeout(function() {
                    $('#errMessage_add').fadeOut(400, function() {
                        $(this).addClass('d-none');
                    });
                }, 3500);



            }else if (res.status == 400) {

				
				setTimeout(function() {
					$('#err_valid_Message_product').fadeOut(400, function() {
						$(this).addClass('d-none');
					});
				}, 3500);
				setTimeout(function() {
					$('#err_valid_Message_price').fadeOut(400, function() {
						$(this).addClass('d-none');
					});
				}, 3500);
				res.errors.forEach(function(error) {

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
			});
			}
			else if (res.status == 200) {
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
                }, 3500);
            }
		}
	});
})


//end add product


$(document).on('click', '.edit_button', function(e) {
    e.preventDefault();

    var product_id = $(this).val();

    $.ajax({
        type: "GET",
        url: "handler_product.php?product_id=" + product_id,
        data: "",
        dataType: "json",  // Set the dataType to json to parse the response automatically
        success: function(response) {
            if(response.status == 422){
                alert(response.message);
            } else if(response.status == 200){
                // Populate product details
                $('#product_id').val(response.data.product_id);
                $('#product_name').val(response.data.product_name);
                $('#sku').val(response.data.sku);
                $('#price').val(response.data.price);

                // Display the featured image
                $('#uploadedImage').attr('src', './uploads/' + response.data.featured_image); // Replace 'path_to_images/' with the correct path

                // Clear existing gallery previews
                $('#galleryPreviewContainer').empty();

                // Display gallery images
                $.each(response.data.gallery, function(index, image) {
                    var imgElement = $('<img>').attr('src', './uploads/' + image) // Replace 'path_to_images/' with the correct path
                                              .attr('alt', 'Gallery Image')
                                              .css('height', '80px')  
                    $('#galleryPreviewContainer').append(imgElement);
                });

				$.each(response.data.categories, function(index, categoryId) {
                    $('#categories_select option[value="' + categoryId + '"]').prop('selected', true);
                });
                
                // Lặp qua danh sách tags và đánh dấu các option có id tương ứng
                $.each(response.data.tags, function(index, tagId) {
                    $('#tags_select option[value="' + tagId + '"]').prop('selected', true);
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


