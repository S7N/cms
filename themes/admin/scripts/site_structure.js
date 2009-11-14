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
	
				$("#ajax_content").load(site_uri('admin/site/update/'+$(NODE).attr('id').substr(5)));				
	        },
	        
	        onmove : function(NODE, REF_NODE, TYPE, TREE_OBJ, RB) {
	        	$.post(site_uri('admin/site/update_tree'), {tree : serialize($("> ul > li", site_structure))});
	        },
	        
	        ondelete : function(NODE, TREE_OBJ, RB) {
	        	$.post(site_uri('admin/route/delete/'+$(NODE).attr('id').substr(5)));
	        	
	        	$('#ajax_content').empty();
	        }
		}
	});
	
	$('.button_add').live('click', function(){
		$('#ajax_content').load(this.href);
		return false;
	});
	
	$('#ajaxform').live('submit', function() {
		
		$(this).ajaxSubmit({
			dataType : 'json',
			success: function(response) { 
	        	console.log(response);
	        	
	        	$('.form span.error').empty();
        		
	        	if (response.errors)
	        	{
	        		for (var key in response.errors)
	        		{
	        			$('.form label[for='+key+']').next('span').html(response.errors[key]);
	        		}
	        	}
	        	else
	        	{
	        		if (response.callback == 'create')
	        		{
	        			console.log('type:', response.type);
		        		var target_node = (response.id == response.parent) ? -1 : $('#'+response.parent);
		        		$.tree.focused().create({ 'data' : response.title, attributes : { id: response.id, rel: response.type } }, target_node);
		        		$.tree.focused().select_branch($('#'+response.id));
	        		}
	        		else if (response.callback == 'update')
	        		{
	        			$.tree.focused().rename($('#'+response.id), response.title);
	        		}
	        	}
	    	}
		});
		
		return false;
	});
	
	var serialize = function (elements) {
		var serialized = [];
		var _this = this;
		
		elements.each(function(i, elem) {
			if ($('> ul', elem).size() > 0)
				serialized.push('"' + elem.id + '":' + serialize($(' > ul > li', elem)));
			else
				serialized.push('"'+elem.id+'":{}');
		});

		return '{'+serialized.join(',')+'}';
	};
});