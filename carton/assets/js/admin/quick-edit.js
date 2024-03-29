jQuery(document).ready(function(){  
    jQuery('#the-list').on('click', '.editinline', function(){  
		
		inlineEditPost.revert();

		var post_id = jQuery(this).closest('tr').attr('id');
		
		post_id = post_id.replace("post-", "");
		
		var $ctn_inline_data = jQuery('#carton_inline_' + post_id );
		
		var sku 				= $ctn_inline_data.find('.sku').text();
		var regular_price 		= $ctn_inline_data.find('.regular_price').text();
		var sale_price 			= $ctn_inline_data.find('.sale_price').text();
		var weight 				= $ctn_inline_data.find('.weight').text();
		var length 				= $ctn_inline_data.find('.length').text();
		var width 				= $ctn_inline_data.find('.width').text();
		var height	 			= $ctn_inline_data.find('.height').text();
		var visibility	 		= $ctn_inline_data.find('.visibility').text();
		var stock_status	 	= $ctn_inline_data.find('.stock_status').text();
		var stock	 			= $ctn_inline_data.find('.stock').text();
		var featured	 		= $ctn_inline_data.find('.featured').text();
		var manage_stock		= $ctn_inline_data.find('.manage_stock').text();
		var menu_order			= $ctn_inline_data.find('.menu_order').text();
		
		jQuery('input[name="_sku"]', '.inline-edit-row').val(sku);
		jQuery('input[name="_regular_price"]', '.inline-edit-row').val(regular_price);
		jQuery('input[name="_sale_price"]', '.inline-edit-row').val(sale_price); 
		jQuery('input[name="_weight"]', '.inline-edit-row').val(weight); 
		jQuery('input[name="_length"]', '.inline-edit-row').val(length); 
		jQuery('input[name="_width"]', '.inline-edit-row').val(width); 
		jQuery('input[name="_height"]', '.inline-edit-row').val(height);
		jQuery('input[name="_stock"]', '.inline-edit-row').val(stock); 
		jQuery('input[name="menu_order"]', '.inline-edit-row').val(menu_order); 
		
		jQuery('select[name="_visibility"] option, select[name="_stock_status"] option').removeAttr('selected');
		
		jQuery('select[name="_visibility"] option[value="' + visibility + '"]', '.inline-edit-row').attr('selected', 'selected');
		jQuery('select[name="_stock_status"] option[value="' + stock_status + '"]', '.inline-edit-row').attr('selected', 'selected');
		
		if (featured=='yes') {
			jQuery('input[name="_featured"]', '.inline-edit-row').attr('checked', 'checked'); 
		} else {
			jQuery('input[name="_featured"]', '.inline-edit-row').removeAttr('checked'); 
		}
		
		if (manage_stock=='yes') {
			jQuery('.stock_qty_field', '.inline-edit-row').show().removeAttr('style');
			jQuery('input[name="_manage_stock"]', '.inline-edit-row').attr('checked', 'checked'); 
		} else {
			jQuery('.stock_qty_field', '.inline-edit-row').hide();
			jQuery('input[name="_manage_stock"]', '.inline-edit-row').removeAttr('checked'); 
		}
		
		// Conditional display
		var product_type		= $ctn_inline_data.find('.product_type').text();
		var product_is_virtual	= $ctn_inline_data.find('.product_is_virtual').text();
		
		if (product_type=='simple' || product_type=='external') {
			jQuery('.price_fields', '.inline-edit-row').show().removeAttr('style');
		} else {
			jQuery('.price_fields', '.inline-edit-row').hide();
		}
		
		if (product_is_virtual=='yes') {
			jQuery('.dimension_fields', '.inline-edit-row').hide();
		} else {
			jQuery('.dimension_fields', '.inline-edit-row').show().removeAttr('style');
		}
	
		if (product_type=='grouped') {
			jQuery('.stock_fields', '.inline-edit-row').hide();
		} else {
			jQuery('.stock_fields', '.inline-edit-row').show().removeAttr('style');
		}
    });  
    
    jQuery('#the-list').on('change', '.inline-edit-row input[name="_manage_stock"]', function(){  
    
    	if (jQuery(this).is(':checked')) {
    		jQuery('.stock_qty_field', '.inline-edit-row').show().removeAttr('style');
    	} else {
    		jQuery('.stock_qty_field', '.inline-edit-row').hide();
    	}
    
    });
    
    jQuery('#wpbody').on('click', '#doaction, #doaction2', function(){  

		jQuery('select, input.text', '.inline-edit-row').val('');
		jQuery('select option', '.inline-edit-row').removeAttr('checked');
		jQuery('#carton-fields-bulk .inline-edit-group .alignright').hide();
		
	});
	
	 jQuery('#wpbody').on('change', '#carton-fields-bulk .inline-edit-group .change_to', function(){  
    
    	if (jQuery(this).val() > 0) {
    		jQuery(this).closest('div').find('.alignright').show();
    	} else {
    		jQuery(this).closest('div').find('.alignright').hide();
    	}
    
    });
    
    jQuery('.product_shipping_class-checklist input').change(function(){
    	
    	jQuery(this).closest('li').siblings().find('input:checked').removeAttr('checked');
    	
    });
});  