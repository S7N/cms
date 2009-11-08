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
		},
		plugins : { 
			contextmenu : {
				items : {
					edit_node : {
						label : "Edit",
						action : function (NODE, TREE_OBJ) {
							
							// modal öffnen bei type = module und redirect
							
							// redirect bei type = page
							if ($(NODE).attr('rel') == 'static')
							{
								$.get(site_uri('admin/site/get_page_update_location'), {id : $(NODE).attr('id').substr(5)}, function(data, status){
									window.location = data;
								});
							}
							else
							{
								$('#dialog').dialog('destroy').dialog({
									buttons: {
										'Update': function () {
											$.post(site_uri('admin/site/update/'+$(NODE).attr('id').substr(5)), {
												'uri' : $('#uri').val(),
												'target' : $('#target').val()
											}, function() {}, 'json');
											
											$(this).dialog("close");
										},
										'Cancel': function () {
											$(this).dialog("close");
										} 
									},
									open: function(event, ui) {
										$("#dialog").load(site_uri('admin/site/update/'+$(NODE).attr('id').substr(5)));
									},
									modal: true,
									title: 'New Node'
								});
							}
						}
					},
					rename : false,
					create : false,
					
					remove : {
						label : "Delete",
						action : function(NODE, TREE_OBJ) {
							
							// TODO show warning!
			
							$.post(site_uri('admin/site/delete/'+$(NODE).attr('id').substr(5)), {}, function(data, status) {
								//console.log(data);
								$.each(NODE, function () { TREE_OBJ.remove(this); });
							});
						}
					}
				}
			}
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
		}
	});
	
	$('#store_tree').click(function() {
		var serialized = serialize($("> ul > li", site_structure));
		$.post(site_uri('admin/site/update_tree'), {tree : serialized});
		return false;
	});
	
	$('#type').live('change', function(){
		if ($(this).val().length > 0)
			$('#type_form').load(site_uri('admin/site/create/'+$(this).val()));
		else
			$('#type_form').empty();
	});
	
	$('#new_node').click(function(){
		$("#dialog").dialog('destroy').dialog({
			buttons: {
				"Create": function () {
					var nodedata, title;
					
					switch ($('#type').val()) {
						case 'redirect':
							nodedata = {
								type : 'redirect',
								uri : $('#type_form #uri').val(),
								target : $('#type_form #target').val()
							};
							
							break;
							
						case 'module':
							nodedata = {
								type : 'module',
								uri : $('#type_form #uri').val(),
								target : $('#type_form #target').val()
							};
								
							break;
							
						case 'static':
							nodedata = {
								type : 'static',
								uri : $('#type_form #uri').val()
							};
							break;
		
						default:
							$(this).dialog("close");
							return;
							break;
					}
					
					$.post(site_uri('admin/site/create/'+nodedata.type), nodedata, function(data, status) {
						if (nodedata.type == 'static')
						{
							window.location = data.location;
							return;
						}
						
						var target_node = (data.id == data.parent) ? -1 : $('#'+data.parent);
						$.tree.focused().create({ 'data' : data.title, attributes : { id: data.id, rel: data.type} }, target_node);
						
					}, 'json');
					
					$(this).dialog("close");
					
				},
				"Cancel" : function () {
					$(this).dialog("close");
				}
			},
			open: function(event, ui) {
				$("#dialog").load(site_uri('admin/site/create'));
			},
			modal: true,
			title: 'New Node'
		}).dialog('open');
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