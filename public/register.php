<?php	
	function get_api_url($url)
	{
		static $apiBaseUrl = '//funguy-exam.com';
	
		return $apiBaseUrl . "/$url";
	}
	
?><!DOCTYPE html>
<html>
    <head>
        <title>Register</title>				
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous" />
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.4/jquery.min.js"></script>			
		
		<style>			
			.form-message {
				display: none;
			}
			
			#register .btn-reset,
			#register .btn-delete {
				display: none;				
			}
			
			#register .form-control-feedback {
				position: relative;
				width: 100%;
				height: auto;
				text-align: left;
			}				
		</style>
	</head>	
	<body>							
		<div class="container content">
			<div class="row">
				<div class="col-md-12">
					<h1 class="page-header">Register</h1>					
					<form id="register" role="form" action="<?php echo get_api_url('user'); ?>">
						<div class="alert form-message"></div>
						<div class="name form-group row">
							<label class="col-form-label col-sm-3 col-md-2" for="user-name">Name: </label>
							<div class="col-sm-4">
								<input type="text" id="user-name" name="name" class="form-control" />								
								<span class="form-control-feedback"></span>
							</div>							
						</div>
						<div class="phone form-group row">
							<label class="col-form-label col-sm-3 col-md-2" for="user-phone">Phone Number: </label>
							<div class="col-sm-4">
								<input type="tel" id="user-phone" name="phone" class="form-control" />
								<span class="form-control-feedback"></span>
							</div>
						</div>
						<div class="nationality form-group row">
							<label class="col-form-label col-sm-3 col-md-2" for="user-nationality">Nationality: </label>
							<div class="col-sm-4">
								<select id="user-nationality" name="nationality" class="form-control">
									<option value="">---</option>
								</select>
								<span class="form-control-feedback"></span>
							</div>
						</div>
						<div class="form-group row">
							<div class="col-sm-3 col-sm-offset-3 col-md-2 col-md-offset-2">
								<button type="submit" class="btn btn-primary btn-submit">Submit</button>
							</div>
						</div>
						<!-- This input item will be populated once form is submitted successfully -->
						<input type="hidden" name="user-id" value="" />
					</form>					
				</div>
			<div>						
		</div>		
		<script>
		jQuery(function($)
		{
			var $form = $('#register'),		
			    $formNotif = $form.find('.form-message'),
			    $submit = $form.find('.btn-submit'),
			    $userId = $form.find(':input[name="user-id"]');
				
			// Get form values as flat object
			function getFormValues()
			{
				var values = $form.serializeArray(),
				    result = {};
				
				for (var i = 0; i < values.length; i++)
				{
					result[values[i].name] = values[i].value;
				}				
				
				return result;
			}
			
			$form.on('submit', function(evt)
			{
				// Remove error messages and error styles first		
				$form.find('.form-control-feedback').html('');
				$form.find('.has-error').removeClass('has-error');
				
				// Send a JSON encoded string instead of form encoded values as stated on the requirements.
				$.ajax({
					url: '<?php echo get_api_url('user'); ?>',
					data: JSON.stringify(getFormValues()),
					contentType: 'application/json; charset=utf-8',
					dataType: 'json',
					type: 'post',
					success: function(response)
					{
						if (response.success)
						{													
							// Set the user_id given for the user. 
							// This will be used for any delete request hereafter.					
							$userId.val(response.data.user_id);
														
							// We expect a string for successful operation
							$formNotif.html(response.message);
							$formNotif.addClass('alert-success');
							$formNotif.show();
							
							// Show delete option on the form and disable the submit button.
							$submit.hide();
						}
						else if (typeof response.message == 'object')
						{
							// Show error messages on their respective fields
							$.each(response.message, function(key, message)
							{
								var $group = $form.find('.form-group.' + key); 
								
								$group.addClass('has-error');
								$group.find('.form-control-feedback').html(message);
							});		
						}
					}
				});
			
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