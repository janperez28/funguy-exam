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
			
			#register .form-control-feedback {
				position: relative;
				width: 100%;
				height: auto;
				text-align: left;
			}				
			
			#user-records .col-id {
				width: 10%;
			}
			
			#user-records .col-name {
				width: 35%;
			}
			
			#user-records .col-phone {
				width: 20%;
			}
			
			#user-records .col-nationality {
				width: 20%;
			}
			
			#user-records .col-actions {
				width: 15%;
			}
			
			#user-records button.close {
				float: none;
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
								<button type="reset" class="btn btn-secondary btn-reset">New</button>
							</div>
						</div>						
					</form>					
				</div>
			</div>			
			<div class="row">
				<div class="col-md-12">
					<h1 class="page-header">User Records</h1>
					<table class="table table-hover" id="user-records">
						<thead>
							<tr>
								<th class="col-id">ID</th>
								<th class="col-name">Name</th>
								<th class="col-phone">Phone Number</th>
								<th class="col-nationality">Nationality</th>
								<th class="col-actions">Actions</th>
							</tr>
						</thead>
						<tbody>	
							<tr class="hidden row-template">
								<td class="col-id"></td>
								<td class="col-name"></td>
								<td class="col-phone"></td>
								<td class="col-nationality"></td>
								<td class="col-actions">
									<button type="button" class="close" aria-label="Close"><span aria-hidden="true">&times;</span></button>
								</td>
							</tr>
						</tbody>
					</table>
				</div>
			</div>
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
			
			function cleanUpForm()
			{
				// Remove error messages and error styles first		
				$form.find('.form-control-feedback').html('');
				$form.find('.has-error').removeClass('has-error');
			}
			
			$form.on('submit', function(evt)
			{
				cleanUpForm();
				
				// Send a JSON encoded string instead of form encoded values as stated on the requirements.
				$.ajax({
					url: '<?php echo get_api_url('user'); ?>',
					data: JSON.stringify(getFormValues()),
					contentType: 'application/json; charset=utf-8',
					dataType: 'json',
					type: 'post',
					success: function(response)
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
					},
					error: function(response)
					{						
						if (typeof response.responseJSON == 'object')
						{
							// Show error messages on their respective fields
							$.each(response.responseJSON.message, function(key, message)
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
						
			$form.on('reset', cleanUpForm);				
			
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