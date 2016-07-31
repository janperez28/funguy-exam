<?php		

	function get_api_url($url)
	{
		static $apiBaseUrl = '//funguy-exam.com/api/v1';
	
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
					<table class="table table-hover" id="user-records" data-last-id="">
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
							<tr class="hidden row-template record-row" data-user-id="">
								<td class="col-id"></td>
								<td class="col-name"></td>
								<td class="col-phone"></td>
								<td class="col-nationality col-nationality_id"></td>
								<td class="col-actions">
									<button type="button" class="close delete" aria-label="Close"><span aria-hidden="true">&times;</span></button>
								</td>
							</tr>
						</tbody>
					</table>
				</div>
			</div>
		</div>				
		
		<script>
		/**
		 * TODO
		 * Refactor this and (optionally) move this to a script file.
		 *
		 */
		jQuery(function($)
		{
			var $form = $('#register'),		
			    $formNotif = $form.find('.form-message'),
			    $submit = $form.find('.btn-submit'),
			    $lastI = $form.find(':input[name="user-id"]'),
			    $users = $('#user-records'),
				$userRowTemplate = $users.find('.row-template').first(),
			    nationalities = {};
				
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
				// Remove messages and error styles first		
				$form.find('.form-control-feedback').html('');
				$form.find('.has-error').removeClass('has-error');
				$formNotif.hide();
				
				$submit.show();
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
						// We expect a string for successful operation
						$formNotif.html(response.message);
						$formNotif.addClass('alert-success');
						$formNotif.show();						
												
						$submit.hide();			

						// Then fetch the new user record(s).
						updateUserRecords();
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
			
			var userColumns = ['name', 'phone', 'id', 'nationality_id'];	
			// This will determine the correct attachment method (prepend/append) of the user records to the list.
			var userRecordsInited = false;
			
			/**
			 * Update the list of users.			 
			 *
			 * @return $.ajax Returns the ajax request used for the operation.
			 */
			function updateUserRecords()
			{				
				var lastId = $users.data('last-id'),
					ajaxOptions = {
						url: '<?php echo get_api_url('user'); ?>',						
						dataType: 'json',
						success: function(response)
						{
							if (response.data instanceof Array && response.data.length > 0)
							{							
								var $row, user, text;
								
								// Clone the row template and set column details
								for (var i = 0; i < response.data.length; i++)
								{
									user = response.data[i];									
									$row = $userRowTemplate.clone();									
									
									for (var j = 0; j < userColumns.length; j++)
									{										
										// Map nationality name from its id if nationality column
										text = (userColumns[j] == 'nationality_id') ? nationalities[user.nationality_id] : user[userColumns[j]];
										
										$row.find('.col-' + userColumns[j]).text(text);
									}	
									
									// Set user id of the row.									
									$row.data('user-id', user.id);
									$row.removeClass('hidden row-template');
									
									if (!userRecordsInited)
									{									
										$users.find('tbody').append($row);
									}
									else 
									{										
										$users.find('tbody').prepend($row.fadeIn(1500));
									}
								}
								
								// Remember the last user id we received.
								$users.data('last-id', response.data[0].id);				
							}
							
							userRecordsInited = true;
						}
					};			
				
				lastId && (ajaxOptions.data = { last_id: lastId });
			
				return $.ajax(ajaxOptions);
			}
			
			// Bind delete buttons
			$users.on('click', '.delete', function(evt)
			{
				evt.preventDefault();
			
				// Get the row container of this record.
				var $row = $(this).closest('.record-row'),
				    userId = $row.data('user-id');

				// Show a simple confirmation dialog before proceeding
				if (!confirm('Are you sure you want to delete user #' + userId + '?')) 
				{
					return;
				}

				// Send DELETE request to the API
				$.ajax({
					url: '<?php echo get_api_url('user');  ?>/' + userId,
					dataType: 'json',
					type: 'DELETE',
					success: function()
					{
						// Delete the selected row from the list
						$row.fadeOut(1500, function() { $row.remove(); });
					}
				});
			});
			
			// Populate nationality options. Retrieve list from the API by sending a GET request.			
			// After receiving the list, we can then get the user records.
			$.ajax({
				url: '<?php echo get_api_url('list/nationalities'); ?>',				
				// We only accept JSON
				dataType: 'json',
				success: function(response)
				{
					if (response.data.list instanceof Array)
					{
						var options = [],
						    data = response.data;						
					
						for (var i = 0; i < data.list.length; i++)
						{
							options.push('<option value="'+ data.list[i].id + '">' + data.list[i].name + '</option>');
							// Add to our nationalities object keyed by its id 
							nationalities[data.list[i].id] = data.list[i].name;
						}						
						
						$('#user-nationality').append(options.join(''));
					}
				}
			}).done(updateUserRecords);
		});
		</script>
	</body>
</html>