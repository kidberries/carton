(
	function(){

		tinymce.create(
			"tinymce.plugins.CartoNShortcodes",
			{
				init: function(d,e) {},
				createControl:function(d,e)
				{

					var ed = tinymce.activeEditor;

					if(d=="carton_shortcodes_button"){

						d=e.createMenuButton( "carton_shortcodes_button",{
							title: ed.getLang('carton.insert'),
							icons: false
							});

							var a=this;d.onRenderMenu.add(function(c,b){

								a.addImmediate(b, ed.getLang('carton.order_tracking'),"[carton_order_tracking]" );
								a.addImmediate(b, ed.getLang('carton.price_button'), '[add_to_cart id="" sku=""]');
								a.addImmediate(b, ed.getLang('carton.product_by_sku'), '[product id="" sku=""]');
								a.addImmediate(b, ed.getLang('carton.products_by_sku'), '[products ids="" skus=""]');
								a.addImmediate(b, ed.getLang('carton.product_categories'), '[product_categories number=""]');
								a.addImmediate(b, ed.getLang('carton.products_by_cat_slug'), '[product_category category="" per_page="12" columns="4" orderby="date" order="desc"]');

								b.addSeparator();

								a.addImmediate(b, ed.getLang('carton.recent_products'), '[recent_products per_page="12" columns="4" orderby="date" order="desc"]');
								a.addImmediate(b, ed.getLang('carton.featured_products'), '[featured_products per_page="12" columns="4" orderby="date" order="desc"]');

								b.addSeparator();

								a.addImmediate(b, ed.getLang('carton.shop_messages'), '[carton_messages]');

								b.addSeparator();

								c=b.addMenu({title:"Pages"});
										a.addImmediate(c, ed.getLang('carton.cart'),"[carton_cart]" );
										a.addImmediate(c, ed.getLang('carton.checkout'),"[carton_checkout]" );
										a.addImmediate(c, ed.getLang('carton.my_account'),"[carton_my_account]" );
										a.addImmediate(c, ed.getLang('carton.edit_address'),"[carton_edit_address]" );
										a.addImmediate(c, ed.getLang('carton.change_password'),"[carton_change_password]" );
										a.addImmediate(c, ed.getLang('carton.view_order'),"[carton_view_order]" );
										a.addImmediate(c, ed.getLang('carton.pay'),"[carton_pay]" );
										a.addImmediate(c, ed.getLang('carton.thankyou'),"[carton_thankyou]" );

							});
						return d

					} // End IF Statement

					return null
				},

				addImmediate:function(d,e,a){d.add({title:e,onclick:function(){tinyMCE.activeEditor.execCommand( "mceInsertContent",false,a)}})}

			}
		);

		tinymce.PluginManager.add( "CartoNShortcodes", tinymce.plugins.CartoNShortcodes);
	}
)();