<?php
	
	$apiBaseUrl = '//funguy-exam.com';
	
	function get_api_url($url)
	{
		global $apiBaseUrl;
	
		return $apiBaseUrl . "/$url";
	}
	
?><!DOCTYPE html>
<html>
    <head>
        <title>Register</title>				
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous" />
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.4/jquery.min.js"></script>			
	</head>	
	<body>							
		<div class="container content">
			<div class="row">
				<div class="col-md-12">
					<h1 class="page-header">Register</h1>
					<form id="register" role="form">						
						<div class="form-group row">
							<label class="col-sm-3 col-md-2" for="user-name">Name: </label>
							<div class="col-sm-4">
								<input type="text" id="user-name" name="name" class="form-control" />
							</div>
						</div>
						<div class="form-group row">
							<label class="col-sm-3 col-md-2" for="user-phone">Phone Number: </label>
							<div class="col-sm-4">
								<input type="tel" id="user-phone" name="phone" class="form-control" />
							</div>
						</div>
						<div class="form-group row">
							<label class="col-sm-3 col-md-2" for="user-nationality">Nationality: </label>
							<div class="col-sm-4">
								<select id="user-nationality" name="nationality" class="form-control">
									<option value="">---</option>
								</select>
							</div>
						</div>
						<div class="form-group row">
							<div class="col-sm-3 col-sm-offset-3 col-md-2 col-md-offset-2">
								<button type="button" class="btn btn-default">Submit</button>
							</div>
						</div>
					</form>					
				</div>
			<div>			
		</div>		
		<script>
		jQuery(function($)
		{
			var $form = $('#register');
			
			$form.on('submit', function(evt)
			{
				evt.preventDefault();					
			});			
			
			// Populate nationality options. Retrieve list from the API by sending a GET request.
			$.ajax({
				url: '<?php echo get_api_url('list/nationalities'); ?>',				
				// We only accept JSON
				dataType: 'json',
				success: function(response)
				{
					if (response.success && response.data.list instanceof Array)
					{
						var options = [],
						    data = response.data;
					
						for (var i = 0; i < data.list.length; i++)
						{
							options.push('<option value="'+ data.list[i].id + '">' + data.list[i].name + '</option>');
						}						
						
						$('#user-nationality').append(options.join(''));
					}
				}
			});
		});
		</script>
	</body>
</html>