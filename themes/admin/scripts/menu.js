$(function(){
	var menu = site_structure = $("#site_menu").tree({
		data : {
			type : "json",
			opts : {
				url : site_uri('admin/menu')
			}
		},
		ui : {
			theme_name: false,
		},
		plugins : { 
			contextmenu : {
				items : {
					edit_node : {
						label : "Edit",
						action : function (NODE, TREE_OBJ) {
							$("#dialog").dialog('destroy').dialog({
								buttons : {
									'Update': function () {			
										$.post(site_uri('admin/menu/update/'+$(NODE).attr('id').substr(5)), {'title' : $('#title').val(), 'route' : $('#route').val()}, function() {}, 'json');
										
										$.tree.focused().rename($(NODE), $('#title').val());
										
										$(this).dialog("close");
									},
									
									'Cancel': function () {
										$(this).dialog("close");
									}
								},
								open: function(event, ui) {
									$("#dialog").load(site_uri('admin/menu/update/'+$(NODE).attr('id').substr(5)));
								},
								modal: true,
								title: 'New Menu Item'
							});
						}
					},
					rename : false,
					create : false,
					
					remove : {
						label : "Delete",
						action : function(NODE, TREE_OBJ) {
							
							// TODO show warning!
			
							$.post(site_uri('admin/menu/delete/'+$(NODE).attr('id').substr(5)), {}, function(data, status) {
								$.each(NODE, function () { TREE_OBJ.remove(this); });
							});
						}
					}
				}
			}
		}
	});
	
	$('#save_menu').click(function(){
		$.post(site_uri('admin/menu/update'), {tree : serialize($("> ul > li", menu))});
		
		return false;
	});
	
	$('#new_menu_item').click(function(){
		$("#dialog").dialog('destroy').dialog({
			buttons : {
				'Create': function () {			
					$.post(site_uri('admin/menu/create'), {'title' : $('#title').val(), 'route' : $('#route').val()}, function(data, status) {
						var target_node = (data.id == data.parent) ? -1 : $('#'+data.parent);
						$.tree.focused().create({ 'data' : data.title, attributes : { id: data.id } }, target_node);
					}, 'json');
					
					$(this).dialog("close");
				},
				
				'Cancel': function () {
					$(this).dialog("close");
				}
			},
			open: function(event, ui) {
				$("#dialog").load(site_uri('admin/menu/create'));
			},
			modal: true,
			title: 'New Menu Item'
		});
	});
	
	var serialize = function (elements) {
		var serialized = [];
		
		elements.each(function(i, elem) {
			if ($('> ul', elem).size() > 0)
				serialized.push('"' + elem.id + '":' + serialize($(' > ul > li', elem)));
			else
				serialized.push('"'+elem.id+'":{}');
		});

		return '{'+serialized.join(',')+'}';
	};
});