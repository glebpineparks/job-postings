Number.prototype.clamp = function(min, max) {
	return Math.min(Math.max(this, min), max);
};

// Create the events.
function CustomJSEvent ( event, params ) {
    params = params || { bubbles: false, cancelable: false, detail: undefined };
    var evt = document.createEvent( 'CustomEvent' );
    evt.initCustomEvent( event, params.bubbles, params.cancelable, params.detail );
    return evt;
}

CustomEvent.prototype = window.Event.prototype;

window.CustomEvent = CustomEvent;

var re_type = jpsd.re_type;
var site_key = jpsd.site_key;

if( re_type != 'on' && site_key ){
	var onloadCallback = function() {
		var verifyCallback = function(token) {
			jQuery('input[name="captcha_response"]').val( token );
			//console.log('verifyCallback', token);
		};
		
		if(typeof grecaptcha !== 'undefined'){
			grecaptcha.render('jobs_google_recaptcha', {
				'sitekey' : site_key,
				'callback' : verifyCallback
			});
		}
	};
}


(function($){
	$.fn.filterFind = function(selector) {
        return this.find('*')         // Take the current selection and find all descendants,
                   .addBack()         // add the original selection back to the set
                   .filter(selector); // and filter by the selector.
    };

    $.fn.svgDraw = function(progress) {
        this.filterFind('path').each(function() {
            var pathLength = this.getTotalLength();
            $(this).css('strokeDasharray', pathLength + ' ' + pathLength);
            $(this).css('strokeDashoffset', pathLength * ((1 - progress)).clamp(0, 1));
        });

        return this;
	};
	

	$('#jobs-modal-form select').select2();

	$(document).ready(function(){

		if( re_type == 'on' ){
			if( site_key ){
				if(typeof grecaptcha !== 'undefined'){
					grecaptcha.ready(function() {
						grecaptcha.execute(site_key, {action: 'homepage'}).then(function(token) {
							jQuery('input[name="captcha_response"]').val( token );
							//console.log( 'token', token );
						});
					});
				}
			}
		}
	  

		//if( $('#jobs-modal-form').length ){
			
			var jobs_fields_modernized = false;
			var sending_form = false;
		
			setTimeout(function(){
				$('.jobs-modal').removeClass('hide');
			}, 500);

			$( document ).on( "click", ".jp-apply-button", function(e) {
				e.preventDefault();
				
				if(!jobs_fields_modernized) jobs_modernize_fields();
				
				$('body').addClass('jobs-modal-open');
				
				if( $('.jobs-modal').hasClass('hide') ){
					$('.jobs-modal').removeClass('hide');
					setTimeout(function(){
						$('.jobs-modal').addClass('open');
					}, 200);
				}else{
					$('.jobs-modal').addClass('open');
				}
			});

			if( $('.jobs-row-apply #jobs-modal-form').length ){
				if(!jobs_fields_modernized) jobs_modernize_fields();
			}
			
			
			$( document ).on( "click", ".jobs-modal .modal-close", function(e) {
				e.preventDefault();
				$(this).parents('.jobs-modal').removeClass('open');
				$('body').removeClass('jobs-modal-open');
			});
			
			function jobs_modernize_fields(){
				
				jobs_fields_modernized = true

				$('.jobs-modal-content').each(function(k){

					$(this).find( '.inputfile' ).each(function(k){

						var accept_msg 	= $(this).parents('.jobs-modal-input').find('.message').text();
						var accept 		= $(this).attr('accept');
						accept = accept ? accept.split(','):'';

						
						var label	 	= this.nextElementSibling;
						var labelVal 	= label.innerHTML;

						//console.log('label', label);

						$(this).change(function(e){

							validateSize(this, e, false);

							var fileName = '';
							if( this.files && this.files.length > 1 )
								fileName = ( this.getAttribute( 'data-multiple-caption' ) || '' ).replace( '{count}', this.files.length );
							else
								fileName = e.target.value.split( '\\' ).pop();
							
							var ext = true;
							if(accept) ext = validateFileExt( this, fileName, accept, accept_msg);
							//console.log('fileName', fileName, this.files);

							if( ext == false ) {
								label.innerHTML = labelVal;
								return false;
							}

							if( fileName ){
								label.querySelector( 'span' ).innerHTML = fileName;
								var num = $(this).parents('.modal-input-fileinput').attr('data-files');
								num = Number(num) + 1;
								$(this).parents('.modal-input-fileinput').attr('data-files', num);
							}else{
								label.innerHTML = labelVal;
							}
						});

						$(this).focus(function(){ $(this).addClass('has-focus') });
						$(this).blur(function(){ $(this).removeClass('has-focus') });
					});

				});

				function validateFileExt( file, sFileName, _validFileExtensions, accept_msg) {
					//var sFileName = oInput.value;
					if (sFileName.length > 0) {
						var blnValid = false;
						for (var j = 0; j < _validFileExtensions.length; j++) {
							var sCurExtension = _validFileExtensions[j];
							if (sFileName.substr(sFileName.length - sCurExtension.length, sCurExtension.length).toLowerCase() == sCurExtension.toLowerCase()) {
								blnValid = true;
								break;
							}
						}
						
						if (!blnValid) {
							if( accept_msg != ''){
								alert(accept_msg);
							} else {
								alert("Sorry, " + sFileName + " is invalid, allowed extensions are: " + _validFileExtensions.join(", "));
							}
							
							var label	 = file.nextElementSibling;
							label.querySelector( 'span' ).innerHTML = '';
							$(file).val('');
							return false;
						}
					}

					return true;
				}


				if( $('.choose_file_multi_add').length ){
					var i = 1;
					
					$( document ).on( "click", ".choose_file_multi_add", function(e) {
					//$('.choose_file_multi_add').click(function(e){
						//e.preventDefault();
						
						var key_id = $(this).data('key');

						var parent = $(this).parents('.modal-input-fileinput');
						var input = $('#file-input-tpl-'+key_id).html();
						var label = $('#file-label-tpl-'+key_id).html();

						var id =  key_id + '-'+i;
						var key = key_id + '-key-'+i;


						input = input.replace( '{id}', id ).replace( '{id}', id ).replace( '{id}', id );
						input = input.replace( '{nr}', i ).replace( '{nr}', i ).replace( '{nr}', i );
						input = input.replace( '{key}', key ).replace( '{key}', key ).replace( '{key}', key );

						label = label.replace( '{id}', id ).replace( '{id}', id ).replace( '{id}', id );


						$(input).insertBefore( $('#'+key_id+' .choose_file_multi_add') );
						$(label).insertBefore( $('#'+key_id+' .choose_file_multi_add') );

						var accept_msg 	= parent.find('.message').text();
						var accept 		= parent.find('input[type=file]').attr('accept');
						
						accept = accept ? accept.split(','):'';

						
						$( document ).on( "change", '#'+id, function(e) {
						//$('#'+id).change(function(e){
							var fileName = '';
							
							validateSize(this, e, true);

							if( this.files && this.files.length > 1 )
								fileName = ( this.getAttribute( 'data-multiple-caption' ) || '' ).replace( '{count}', this.files.length );
							
							else
								fileName = e.target.value.split( '\\' ).pop();

							
							var ext = true;
							if(accept) ext = validateFileExt( this, fileName, accept, accept_msg);

							if( ext == false ) {
								$('#'+id).remove();
								$('#file-input-tpl-'+id).remove();
								$('#label-'+id).remove();
								return false;
							}

							if( fileName ){
								$('#label-'+id).find('span.name').text( fileName );
								var num = $(this).parents('.modal-input-fileinput').attr('data-files');
								num = Number(num) + 1;
								$(this).parents('.modal-input-fileinput').attr('data-files', num);
							}

						});

						
						$( document ).on( "click", ".choose_file_multi .remove", function(e) {
						//$('.choose_file_multi .remove').click(function(e){
							e.preventDefault();
							var id = $(this).parents('.choose_file_multi').attr('id');
							id = id.replace('label-', 'jobgroup-');

							var num = $(this).parents('.modal-input-fileinput').attr('data-files');
							num = Number(num) - 1;
							$(this).parents('.modal-input-fileinput').attr('data-files', num);

							$('.'+id).remove();

							recalculateInputs();

							return false;
						});


						$('#'+id).click();

						recalculateInputs();

						$( document ).on( "mousemove", function(e) {
							setTimeout(function(){
								var current_obj = $('#' + id)[0];
								if( typeof current_obj !== 'undefined' && ! current_obj.value ){
									$('#label-'+id).find('.remove').trigger('click');
								}
							}, 300);
							
							return false;
							
						} );

						i++;
					});
				}

				//$('.progress-button .progress-circle').svgDraw(0);
			}
			
			
			$( document ).on( "submit", "form#jobs-modal-form", function(e) {
				e.preventDefault();

				var self 		= this;
				var toreturn	= true;
				//var errors 		= [];

				$('#jobs-modal-form .input-reqired').each(function(){
					var value = $(this).val();

					var is_checked = false;

					if( $(this).hasClass('input-job_email') ){
						if( value == '' || !validateEmail(value) ){
							$(this).addClass('jobs-alert').parents('.jobs-modal-input').addClass('jobs-alert');
							toreturn = false;
							//errors.push( $(this) );
						}else{
							$(this).removeClass('jobs-alert').parents('.jobs-modal-input').removeClass('jobs-alert');
							if(toreturn) toreturn = true;
						}

						//console.log('toreturn 1', toreturn);
					}else if( $(this).hasClass('modal-input-checkbox') ){
						var checkboxes_checked = 0;
						$(this).parents('.checkbox_field').find('input[type="checkbox"]').each(function(){
							var checked = $(this).prop('checked');
							if( checked == true ){
								checkboxes_checked++;
							}
						});

						if( checkboxes_checked == 0 ){
							$(this).addClass('jobs-alert').parents('.jobs-modal-input').addClass('jobs-alert');
							toreturn = false;
							//errors.push( $(this) );
						}else{
							$(this).removeClass('jobs-alert').parents('.jobs-modal-input').removeClass('jobs-alert');
							if(toreturn) toreturn = true;
						}

						//console.log('toreturn 2', toreturn);

					}else if( $(this).hasClass('modal-input-radio') ){
						var checkboxes_checked = 0;
						$(this).parents('.radio_field').find('input[type="radio"]').each(function(){
							var checked = $(this).prop('checked');
							if( checked == true ){
								checkboxes_checked++;
							}
						});

						if( checkboxes_checked == 0 ){
							$(this).addClass('jobs-alert').parents('.jobs-modal-input').addClass('jobs-alert');
							toreturn = false;
							//errors.push( $(this) );
						}else{
							$(this).removeClass('jobs-alert').parents('.jobs-modal-input').removeClass('jobs-alert');
							if(toreturn) toreturn = true;
						}

						//console.log('toreturn 3', toreturn);

					}else{
						if( value == '' ){
							$(this).addClass('jobs-alert').parents('.jobs-modal-input').addClass('jobs-alert');
							toreturn = false;
							//errors.push( $(this) );
						}else{
							$(this).removeClass('jobs-alert').parents('.jobs-modal-input').removeClass('jobs-alert');
							if(toreturn) toreturn = true;
						}

						//console.log('toreturn 4', toreturn);
					}

				});

				//console.log(toreturn, sending_form);

				if( toreturn && sending_form == false ){
					/*
					var progressBTN = $(self).find('.progress-button');
					progressBTN.addClass('loading');

		        	var progressCRCL = $(self).find('.progress-circle');
				    var progress = 0;

				    var intervalId = setInterval(function() {
				        progress += Math.random() * 0.5;
				        progressCRCL.svgDraw(progress);

				        if(progress >= 1) {
				            clearInterval(intervalId);
				        }
				    }, 200);
				    */

	 			    sending_form = true;
	 			   	$('.job-submit').hide();
				    $('.jobs-sending').show();

	 			    var formData 			= new FormData($("form#jobs-modal-form")[0]);
						var valid_holder 	= $(self).find('.jobs-submit-validation');
						valid_holder.html('');

				    $.ajax({
				    	type: 'POST',
				        url: jpsd.ajaxurl,
				        data: formData,
				        processData: false,
				        contentType: false,

				        success: function (data) {

							var data = $.parseJSON( data );

							//console.log( 'data', data );
							//clearInterval(intervalId);
							//progressCRCL.svgDraw(1);

							var status = data.status

							// Clear all spaces and newlines that can happen on response on some servers
							//data = data.replace(/\s/g, "");

							switch( status ){
								case 'ok':
									$(self).slideUp();
									$('#job-apply-confirmation').slideDown();

									if ( window.CustomJSEvent ) {
										var event = new CustomJSEvent("application_success", {
											detail: {
												message: data,
												time: new Date(),
											},
											bubbles: true,
											cancelable: true
										});
										e.currentTarget.dispatchEvent(event);
									}
								break;

								case 'error':
									var messages = data.messages
									console.log(data);
									if( $.isArray(messages) ){

										$(messages).each(function(i){
											var msg = messages[i];
	
											switch (msg) {
												case 'recaptcha_not_valid':
														msg = jpsd['re_message'];
													break;
											
												default:
														//msg = msg.split('_').join(' ');
														msg = jpsd[ msg ];
													break;
											}
	
											valid_holder.append('<div class="jobs-submit-error-msg">'+msg+'</div>');
										});
									}

									$('.jobs-sending').hide();
									$('.job-submit').show();
									sending_form = false;
									
									// Define that the event name is 'application_error'.
									if ( window.CustomJSEvent ) {
										var event = new CustomJSEvent("application_error", {
											detail: {
												message: data,
												time: new Date(),
											},
											bubbles: true,
											cancelable: true
										});
										e.currentTarget.dispatchEvent(event);
									}
								break;
							}

			            	

			    			return false;
				        },
				    });


			    	return false;
				}

			    return false;
			});
		
			
			function validateSize(file, event, multifile) {

				//return true;

				var allowed 		= jpsd.max_filesize ? jpsd.max_filesize : 10; //Defaults to 10MB
				var validation 	= jpsd.validation.replace('%1$s', allowed);

				var FileSize 		= file.files[0].size / 1024 / 1024; // in MB

				var validation_holder = $(file).parents('.jobs-modal-input').find('.validation');

				//console.log('file size', FileSize);
				//console.log('allowed', allowed );

				if (FileSize > allowed) {

					if( file.files && file.files.length > 1 )
						fileName = ( file.getAttribute( 'data-multiple-caption' ) || '' ).replace( '{count}', file.files.length );
					else
						fileName = event.target.value.split( '\\' ).pop();

					validation = validation.replace('%2$s', fileName);

					if( multifile ){
						var label	 = file.nextElementSibling;
						$(label).remove();
						label.querySelector( 'span' ).innerHTML = '';
						$(file).val(''); //for clearing with Jquery
						$(file).remove();
					}else{
						var label	 = file.nextElementSibling;
						label.querySelector( 'span' ).innerHTML = '';
						$(file).val(''); //for clearing with Jquery
					}
					//console.log( validation );

					validation_holder.html( validation );

				} else {
					//console.log('size OK');
					validation_holder.html( '' );
				}
				recalculateInputs();
			}

			function recalculateInputs(){
				if( $('.modal-input-fileinput.multiple .modal-input-multifile').length == 0 ){
					$('.disabled-file-placeholder').addClass('input-reqired');
				}else{
					$('.disabled-file-placeholder').removeClass('input-reqired');
				}
			}

			function validateEmail(email) {
				var re = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
				return re.test(email);
			}


			/*
			$('.progress-button').on('click', function() {
			    var $button = $(this);
			    $(this).addClass('loading');

			    var $progress = $(this).find('.progress-circle');
			    var progress = 0;
			    var intervalId = setInterval(function() {
			        progress += Math.random() * 0.5;
			        $progress.svgDraw(progress);

			        if(progress >= 1) {
			            clearInterval(intervalId);
			            //console.log("cleared interval");
			            $button.removeClass('loading');
			            if($button.attr('data-result') == "true") {
			                $button.addClass('success');
			            }
			            else {
			            		$button.addClass('error');
			            }

			        }
			    }, 300);

			    // Now that we finished, unbind
			    $(this).off('click');
			});
			*/


		//} // end .jobs-modal length

	});


})(jQuery);


