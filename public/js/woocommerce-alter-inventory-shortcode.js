                    jQuery(document).ready(function() {
                        jQuery("#variants_table_wrap").hide();
                        //jQuery("#products_table_wrap_at").hide();
                        jQuery(".variants_at_tab").on("click", function() {
                        jQuery("#variants_table_wrap").show();                            
                        jQuery("#products_table_wrap_at").hide();
                        jQuery(this).toggleClass("active");
                        jQuery(".product_at_tab").removeClass("active")
                        });
                        jQuery(".product_at_tab").on("click", function() {
                        jQuery("#products_table_wrap_at").show();                            
                        jQuery("#variants_table_wrap").hide();
                        jQuery(this).toggleClass("active");
                        jQuery(".variants_at_tab").removeClass("active")
                        });                        
                            jQuery("#reviews").hide();
                        jQuery('.alter_inventory_page input[type="number"]').bootstrapNumber({
                                upClass: 'up_tckt',
                                downClass: 'dwn_tckt'
                              });
                    }); 
                    

