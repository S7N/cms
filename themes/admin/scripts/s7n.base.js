(function($) {
	$.log = function () { if(window.console) { console.debug(arguments); } };
	
	$.loadContent = function (uri) { $.emptyContent(); $("#ajax_content").load(uri); };
	$.emptyContent = function () { $("#ajax_content").empty(); };
	
	$.serializeTree = function (elements) {
		var serialized = [];
		
		elements.each(function(i, elem) {
			if ($('> ul', elem).size() > 0)
				serialized.push('"' + elem.id + '":' + $.serializeTree(jQuery(' > ul > li', elem)));
			else
				serialized.push('"' + elem.id + '":{}');
		});
	
		return '{' + serialized.join(',') + '}';
	};
})(jQuery);

$(function(){
	$('.tooltip').tipsy({gravity: 'w'});
	
	$('.button_add').live('click', function(){
		$.loadContent(this.href);
		return false;
	});
	
	$('#ajaxform').live('submit', function() {
		
		$(this).ajaxSubmit({
			dataType : 'json',
			success: function(response) { 
	        	$.log(response);
	        	
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
	
});