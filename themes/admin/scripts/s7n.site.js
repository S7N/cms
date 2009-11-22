$(function () {
	var site_structure = $("#site_structure").tree({
		data : {
			type : "json",
			opts : {
				url : site_uri('admin/site')
			}
		},
		ui : {
			theme_name: false,
			selected_delete: false
		},
		
		rules : {
			// only nodes of type root can be top level nodes
			valid_children : [ "page" ]
		},
		types : {
			/*"root" : {
				draggable : false,
				valid_children : [ "static", "redirect", "module" ],
				icon : {
					image : "root.png"
				}
			},*/
			"static" : {
				icon : {
					image : theme_uri('images/static.png')
				} 
			},
			"redirect" : {
				max_children : 0,
				icon : {
					image : theme_uri('images/redirect.png')
				} 
			},
			"module" : {
				max_children : 0,
				icon : {
					image : theme_uri('images/module.png')
				}
			}
		},
		
		callback : {
			onselect : function(NODE, TREE_OBJ) {
				$('.button_delete').unbind('click').click(function(){					
					TREE_OBJ.remove(NODE);					
					return false;
				});
	
				$.loadContent(site_uri('admin/site/update/'+$(NODE).attr('id').substr(5)));				
	        },
	        
	        onmove : function(NODE, REF_NODE, TYPE, TREE_OBJ, RB) {
	        	$.post(site_uri('admin/site/update_tree'), {tree : $.serializeTree($("> ul > li", site_structure))});
	        },
	        
	        ondelete : function(NODE, TREE_OBJ, RB) {
	        	$.post(site_uri('admin/route/delete/'+$(NODE).attr('id').substr(5)));
	        	
	        	$.emptyContent();
	        }
		}
	});
});