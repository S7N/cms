$(function(){
	$('.button_add').live('click', function(){
		$('#ajax_content').load(this.href);
		return false;
	});
	
	/*$('#ajaxform').live('submit', function() {
		
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
	        		// callback
	        	}
	    	}
		});
		
		return false;
	});*/
});