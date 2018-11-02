$(window).ready(function(){
	$('#user').focus();

	var form = $('form'),
		user = $('#user'),
		password = $('#password');
	
	$('#sign-in').on('click', function(evt){
		evt.preventDefault();
		$this = $(this);

		$('.alert').hide();
		
		if(user.val() == ''){
			user.addClass('has-error').focus();
			$('<div class="alert alert-danger">All fields are required.</div>').fadeIn().prependTo(form);
			return;
		}
		
		if(password.val() == ''){
			user.removeClass('has-error');
			password.addClass('has-error').focus();
			$('<div class="alert alert-danger">All fields are required.</div>').fadeIn().prependTo(form);
			return;
		}
		
		user.removeClass('has-error');
		password.removeClass('has-error');
		
		$this.html('Validating...');

		$.post('../validator.php', form.serialize(), function(r){
			console.log(r);
			if(r.trim() == 'correct'){
				location.href='../products';
			}else{
				password.addClass('has-error');
				$('<div class="alert alert-danger">Invalid user or password.</div>').fadeIn().prependTo(form);
			}
			
			$this.html('Sign In');
		});

	});

});
