(function($){
	$(window).on('load', function(){

		$(document).on('click', 'body.post-type-jobs #publish', function(){
			var job_confirmation_email = $('#job_confirmation_email').val();
			if( job_confirmation_email == '' ){
				$('.tab_job_notification a').click();
			}
		});

		/* Conditional logic function */
		$('[data-conditional-logic]').change(function(i){
			var id 		= $(this).data('conditional-logic');
			var id2 	= $(this).data('conditional-logic2');
			var invert 	= $(this).data('conditional-logic-invert');
			var checked = $(this).prop('checked');

			if( invert == 'invert' ){
				if(checked == true){
					checked = false;
				}else{
					checked = true;
				}
			}

			if( checked == true ){
				if(id) $('#'+id).slideDown();
				if(id2) $('#'+id2).slideUp();
			}else{
				if(id) $('#'+id).slideUp();
				if(id2) $('#'+id2).slideDown();
			}
		}).trigger('change');



		//Location name hint
		$(document).on('change', '.job_remote_data_type', function(){
			$('.job_remote_data_type').each(function(){
				var type = $(this).val();
				var name = $(this).parents('.jfw_repeater_row').find('.job_remote_data_name');
				var example = $(this).parents('.jfw_repeater_row').find('.example');
				var hint = "";

				switch(type){
					case 'Country':
						hint = $(this).data('hint-country');
						name.attr('placeholder', hint);
						example.html('&nbsp;('+hint+')');
					break;

					case 'State':
						hint = $(this).data('hint-state');
						name.attr('placeholder', hint);
						example.html('&nbsp;('+hint+')');
					break;

					//State
					default:
						name.attr('placeholder', '');
						example.html('');
					break;
				}

			});
		}).trigger('change');


		$('.resetStyleSettings').click(function(e){
			e.preventDefault();

			$('.jobs_button_bg_color').val('');
			$('.jobs_button_bg_color_hover').val('');
			$('.jobs_button_text_color').val('');
			$('.jobs_heading_text_color').val('');
			$('.jobs_subheading_text_color').val('');
			$('.jobs_list_item_bg').val('');
			$('.jobs_list_item_border').val('');
			$('.jobs_content_heading_color').val('');
			$('.jobs_content_text_color').val('');

			$('#stylesForm').submit();
		});

		$( "#js-datepicker" ).datepicker({
			dateFormat: jpsd.date_format
		});


		$('.jobs_button_roundness').change(function(){
			var px = $(this).val();
			$('.preview_apply_btn, .borderRadius').css('borderRadius', px);
		}).trigger('change');

		$('.jobs_box_roundness').change(function(){
			var px = $(this).val();
			$('.job-preview, .job-side, .boxBorderRadius').css('borderRadius', px);
		}).trigger('change');



		$('.jobs_filters_styles').change(function(){
			var style = $(this).val();

			var wrapper = $('.job-postings-filters');

			var cl =  wrapper.attr("class").split(" ");
	    var newcl =[];
	    for(var i=0;i<cl.length;i++){
	        r = cl[i].search(/filter-style-+/);
	        if(r)newcl[newcl.length] = cl[i];
	    }
	    wrapper.removeClass().addClass(newcl.join(" "));
			wrapper.addClass(style);
		}).trigger('change');


		if( $('#jobs_button_bg_color').length ){

			$('#jobs_button_bg_color').ColorPicker({
				color: '#22c0f1',
				onShow: function (colpkr) {
					$(colpkr).fadeIn(500);
					return false;
				},
				onHide: function (colpkr) {
					$(colpkr).fadeOut(500);
					return false;
				},
				onChange: function (hsb, hex, rgb) {
					$('#jobs_button_bg_color div').css('backgroundColor', '#' + hex);
					$('.preview_apply_btn').css('backgroundColor', '#' + hex);
					$('.jobs_button_bg_color').val('#' + hex);
				}
			});
			$('#jobs_button_bg_color').ColorPickerSetColor( $('#jobs_button_bg_color').data('color') );


			$('#jobs_button_bg_color_hover').ColorPicker({
				color: '#22c0f1',
				onShow: function (colpkr) {
					$(colpkr).fadeIn(500);
					return false;
				},
				onHide: function (colpkr) {
					$(colpkr).fadeOut(500);
					return false;
				},
				onChange: function (hsb, hex, rgb) {
					$('#jobs_button_bg_color_hover div').css('backgroundColor', '#' + hex);
					$('.jobs_button_bg_color_hover').val('#' + hex);
				}
			});
			$('#jobs_button_bg_color_hover').ColorPickerSetColor( $('#jobs_button_bg_color_hover').data('color') );


			$('#jobs_button_text_color').ColorPicker({
				color: '#ffffff',
				onShow: function (colpkr) {
					$(colpkr).fadeIn(500);
					return false;
				},
				onHide: function (colpkr) {
					$(colpkr).fadeOut(500);
					return false;
				},
				onChange: function (hsb, hex, rgb) {
					$('#jobs_button_text_color div').css('backgroundColor', '#' + hex);
					$('.preview_apply_btn').css('color', '#' + hex);
					$('.jobs_button_text_color').val('#' + hex);
				}
			});
			$('#jobs_button_text_color').ColorPickerSetColor( $('#jobs_button_text_color').data('color') );



			$('#jobs_heading_text_color').ColorPicker({
				color: '#000000',
				onShow: function (colpkr) {
					$(colpkr).fadeIn(500);
					return false;
				},
				onHide: function (colpkr) {
					$(colpkr).fadeOut(500);
					return false;
				},
				onChange: function (hsb, hex, rgb) {
					$('#jobs_heading_text_color div').css('backgroundColor', '#' + hex);
					$('.job_heading').css('color', '#' + hex);
					$('.jobs_heading_text_color').val('#' + hex);
				}
			});
			$('#jobs_heading_text_color').ColorPickerSetColor( $('#jobs_heading_text_color').data('color') );


			$('#jobs_subheading_text_color').ColorPicker({
				color: '#373737',
				onShow: function (colpkr) {
					$(colpkr).fadeIn(500);
					return false;
				},
				onHide: function (colpkr) {
					$(colpkr).fadeOut(500);
					return false;
				},
				onChange: function (hsb, hex, rgb) {
					$('#jobs_subheading_text_color div').css('backgroundColor', '#' + hex);
					$('.job_subheading').css('color', '#' + hex);
					$('.jobs_subheading_text_color').val('#' + hex);
				}
			});
			$('#jobs_subheading_text_color').ColorPickerSetColor( $('#jobs_subheading_text_color').data('color') );



			$('#jobs_list_item_bg').ColorPicker({
				color: '#f0f0f0',
				onShow: function (colpkr) {
					$(colpkr).fadeIn(500);
					return false;
				},
				onHide: function (colpkr) {
					$(colpkr).fadeOut(500);
					return false;
				},
				onChange: function (hsb, hex, rgb) {
					$('#jobs_list_item_bg div').css('backgroundColor', '#' + hex);
					$('.job-preview, .job-side').css('backgroundColor', '#' + hex);
					$('.jobs_list_item_bg').val('#' + hex);
				}
			});
			$('#jobs_list_item_bg').ColorPickerSetColor( $('#jobs_list_item_bg').data('color') );


			$('#jobs_list_item_border').ColorPicker({
				color: '#e9e9e9',
				onShow: function (colpkr) {
					$(colpkr).fadeIn(500);
					return false;
				},
				onHide: function (colpkr) {
					$(colpkr).fadeOut(500);
					return false;
				},
				onChange: function (hsb, hex, rgb) {
					$('#jobs_list_item_border div').css('backgroundColor', '#' + hex);
					$('.job-preview, .job-side, .jobs-row-label span').css('borderColor', '#' + hex);
					$('.jobs_list_item_border').val('#' + hex);
				}
			});
			$('#jobs_list_item_border').ColorPickerSetColor( $('#jobs_list_item_border').data('color') );




			$('#jobs_content_heading_color').ColorPicker({
				color: '#000000',
				onShow: function (colpkr) {
					$(colpkr).fadeIn(500);
					return false;
				},
				onHide: function (colpkr) {
					$(colpkr).fadeOut(500);
					return false;
				},
				onChange: function (hsb, hex, rgb) {
					$('#jobs_content_heading_color div').css('backgroundColor', '#' + hex);
					$('.jobs_content_heading').css('color', '#' + hex);
					$('.jobs_content_heading_color').val('#' + hex);
				}
			});
			$('#jobs_content_heading_color').ColorPickerSetColor( $('#jobs_content_heading_color').data('color') );


			$('#jobs_content_text_color').ColorPicker({
				color: '#000000',
				onShow: function (colpkr) {
					$(colpkr).fadeIn(500);
					return false;
				},
				onHide: function (colpkr) {
					$(colpkr).fadeOut(500);
					return false;
				},
				onChange: function (hsb, hex, rgb) {
					$('#jobs_content_text_color div').css('backgroundColor', '#' + hex);
					$('.jobs_content_text').css('color', '#' + hex);
					$('.jobs_content_text_color').val('#' + hex);
				}
			});
			$('#jobs_content_text_color').ColorPickerSetColor( $('#jobs_content_text_color').data('color') );

		} // colorpickers

		var preview_location = true;
		var employment_type = true;

		$('#jobs_preview_location').change(function(){

			if( $(this).prop('checked') == false ){
				preview_location = true;
				$('.job_subheading.city').show();
				if(employment_type) $('.job_separator').show();
			}else{
				preview_location = false;
				$('.job_subheading.city').hide();
				if(employment_type) $('.job_separator').hide();
			}

		}).trigger('change');


		$('#jobs_preview_employment_type').change(function(){

			if( $(this).prop('checked') == false ){
				employment_type = true;
				$('.job_subheading.type').show();
				if(preview_location) $('.job_separator').show();
			}else{
				employment_type = false;
				$('.job_subheading.type').hide();
				if(preview_location) $('.job_separator').hide();
			}

		}).trigger('change');

		$('#jobs_offer_ended_message_enabled').change(function(){

			if( $(this).prop('checked') == true ){
				$('#jobs_offer_ended_message_enabled_text').show();
			}else{
				$('#jobs_offer_ended_message_enabled_text').hide();
			}

		}).trigger('change');


		$('.jobs-wrapper').removeClass('menu-instructions-inactive');

		function updateHeights(){
			$('.jobs_match_height').matchHeight({property: 'min-height'});
		}

		$('.jobs-gear-icon').on('click', function(e){
			e.preventDefault();
			$(this).parents('.jobs-row').toggleClass('open');
		});



		$( ".jobs-wrapper-sortable-left, .jobs-wrapper-sortable-right, .jobs-wrapper-sortable-disabled" ).sortable({
			connectWith: ".connectedSortable",
			handle: ".jobs-row-label",
	      	placeholder: "ui-state-highlight-place",

	      	stop: function( event, ui ) {
		      	if( $(ui.item).hasClass('type-tinymce') ){
		      		var editorId = $(ui.item).find('.wp-editor-area').attr('id');
		      		reintTinymce( editorId );
		      	}
		      	updateIndexes();
	      	}
	  	});

		

		// Settings fields sorting
		$( ".jobs-settings-sortable-left, .jobs-settings-sortable-right, .jobs-settings-sortable-disabled" ).sortable({
			connectWith: ".connectedSortable",
			handle: ".jobs-row-label",
	      	placeholder: "ui-state-highlight-place",

	      	stop: function( event, ui ) {

		      	updateIndexes();
	      	}
	  	});



	  	function reintTinymce(editorId){
	    	if(!editorId) return false;

      		// remove old
			tinyMCE.EditorManager.execCommand('mceFocus', false, editorId);
			tinyMCE.EditorManager.execCommand('mceRemoveEditor', true, editorId);

			// and init new
			tinymce.init(tinyMCEPreInit.mceInit[editorId]);
	  	}

	    function updateIndexes(){
	    	$( ".jobs-wrapper-left .jobs-row" ).each(function( index ){
	    		var sort_pos = $(this).parents('.jobs-wrapper').data('sort');
	    		$(this).find('.item-sort-value').val( sort_pos + '-' + index );
	    	});

	    	$( ".jobs-wrapper-right .jobs-row" ).each(function( index ){
	    		var sort_pos = $(this).parents('.jobs-wrapper').data('sort');
	    		$(this).find('.item-sort-value').val( sort_pos + '-' + index );
	    	});

	    	$( ".jobs-wrapper-disabled .jobs-row" ).each(function( index ){
	    		var sort_pos = $(this).parents('.jobs-wrapper').data('sort');
	    		$(this).find('.item-sort-value').val( sort_pos + '-' + index );
	    	});

	    }
	    updateIndexes();


		$('.jobs-row .jobs-remove-icon').on('click', function() {
			$(this).parents('.jobs-row').appendTo('.jobs-wrapper-sortable-disabled');
			updateIndexes();
			updateHeights();
		});


		$('.jobs-row .jobs-setleft').on('click', function() {
			var append = $(this).data('append');
			$(this).parents('.jobs-row').appendTo('.'+append);
			var editorId = $(this).parents('.jobs-row').find('.wp-editor-area').attr('id');

			updateIndexes();
			updateHeights();
		    if(editorId && editorId != undefined) reintTinymce( editorId );
		});

		$('.jobs-row .jobs-setright').on('click', function() {
			var append = $(this).data('append');
			$(this).parents('.jobs-row').appendTo('.'+append);
			var editorId = $(this).parents('.jobs-row').find('.wp-editor-area').attr('id');

			updateIndexes();
			updateHeights();
		    if(editorId && editorId != undefined) reintTinymce( editorId );
		});


		$('#position_button-style').on('change', function(e){
			var value = $(this).val();

			switch(value){
				case 'secondary-style':
					$(this).parents('.jobs-row').find('.jp-input').removeClass('primary-style').addClass('secondary-style');
				break

				default:
					$(this).parents('.jobs-row').find('.jp-input').removeClass('secondary-style').addClass('primary-style');
				break;
			}


		});



		// ---------------------------------------------------------
		// Tabs
		// ---------------------------------------------------------

		$(".job_tabs").each(function(){

			$(this).find(".job_tab a").on('click', function() {


				$(this).parent().parent().find("a").removeClass("current");
				$(this).addClass("current");
				$(this).parents('.job_tabs').find(".job_tab_content").hide();
				var activeTab = $(this).attr("href");
				$(activeTab).show();

				/*
				var http_referer = $('input[name="_wp_http_referer"]');
				var http_referer_url = http_referer.val();

				http_referer_url = http_referer_url.split('#');

				http_referer.val( http_referer_url[0] + activeTab );

				$('.jfw_last_screen').val(activeTab);
				*/

				if( $('.jobs_plugin_settings').length ) $('form').attr('action', 'options.php' + activeTab + '-settings-tab');

				return false;

			});

		});


		$(document).on('change', '#checkbox-OTHER', function(){
			if( $(this).prop('checked') != false ){
				$('.jobs-row-input .options_group .other_input').show();
			}else{
				$('.jobs-row-input .options_group .other_input').hide();
			}
			//console.log( 'OTHER', $(this).prop('checked') );
		});
		$('#checkbox-OTHER').trigger('change');

		/*
		if($('.jfw_last_screen').val() != ''){
			var last = $('.jfw_last_screen').val();
			$('.job_tab').find('a[href="'+last+'"]').click();
		}
		*/

		///*
		var hash = window.location.hash;
		if( hash ){
			hash = hash.replace('-settings-tab', '');
			$('a[href$="'+hash+'"]').click();

			if(hash == '#anonymous_metrics'){
				$('a[href$="#jobs_help"]').click();
			}
		}
		//*/


		//$('.jp-textarea').autogrow({onInitialize: true});
		$( '.inputfile' ).each( function(){
			var $input	 = $( this ),
				$label	 = $input.next( 'label' ),
				labelVal = $label.html();

			$input.on( 'change', function( e ){
				var fileName = '';

				if( this.files && this.files.length > 1 )
					fileName = ( this.getAttribute( 'data-multiple-caption' ) || '' ).replace( '{count}', this.files.length );
				else if( e.target.value )
					fileName = e.target.value.split( '\\' ).pop();

				if( fileName )
					$label.find( 'span' ).html( fileName );
				else
					$label.html( labelVal );
			});

			// Firefox bug fix
			$input
			.on( 'focus', function(){ $input.addClass( 'has-focus' ); })
			.on( 'blur', function(){ $input.removeClass( 'has-focus' ); });
		});


		// ---------------------------------------------------------
		// Toggle
		// ---------------------------------------------------------


	    var allPanels = $('.toggle-box');
	    var allPanels2 = $('.trigger');

		$('.trigger').on('click', function() {
			allPanels.slideUp(300);
			allPanels2.removeClass('active');

			if($(this).next().css('display') != 'block') $(this).addClass('active').next().slideDown(300);

			return false;
		});


		// ---------------------------------------------------------
		// Repeater
		// ---------------------------------------------------------
		if( $('.jfw_repeater').length ){
			$('.jfw_repeater').repeater({
	            // (Optional)
	            // "defaultValues" sets the values of added items.  The keys of
	            // defaultValues refer to the value of the input's name attribute.
	            // If a default value is not specified for an input, then it will
	            // have its value cleared.
	            defaultValues: {
	            },
	            // (Optional)
	            // "show" is called just after an item is added.  The item is hidden
	            // at this point.  If a show callback is not given the item will
	            // have $(this).show() called on it.
	            show: function () {
	                $(this).slideDown(100, function(){

	                });
	            },
	            // (Optional)
	            // "hide" is called when a user clicks on a data-repeater-delete
	            // element.  The item is still visible.  "hide" is passed a function
	            // as its first argument which will properly remove the item.
	            // "hide" allows for a confirmation step, to send a delete request
	            // to the server, etc.  If a hide callback is not given the item
	            // will be deleted.
	            hide: function (deleteElement) {
	                if(confirm('Are you sure you want to delete this row?')) {
	                    $(this).slideUp(100, deleteElement);
	                }
	            },
	            ready: function (setIndexes) {
                	//console.log('setIndexes', setIndexes);
            	},
	            // (Optional)
	            // Removes the delete button from the first list item,
	            // defaults to false.
	            isFirstItemUndeletable: false
	        })

		    $( ".jfw_sortable" ).sortable({
		      	placeholder: "ui-state-highlight-place",
		      	//cancel: ".jfw_repeater_heading"
		    });


		    $(document).on('change', '.field_type_select', function(e){
		    	var value = $(this).val();

		    	var jfw_options_row = $(this).parents('.jfw_repeater_row').find('.jfw_options_row');
		    	var jfw_radio_row 	= $(this).parents('.jfw_repeater_row').find('.jfw_radio_row');
		    	var jfw_select_row 	= $(this).parents('.jfw_repeater_row').find('.jfw_select_row');
		    	var jfw_file_row 	= $(this).parents('.jfw_repeater_row').find('.jfw_file_row');
				var jfw_placehold_row = $(this).parents('.jfw_repeater_row').find('.jfw_col_placeholders');
				var jfw_col_required = $(this).parents('.jfw_repeater_row').find('.jfw_col_required');
				var jfw_multi_file_row = $(this).parents('.jfw_repeater_row').find('.jfw_multi_file_row');
				
				
				if( value == 'section' ){
		    		jfw_placehold_row.addClass('vis-hide');
		    		jfw_col_required.addClass('vis-hide');
		    	}else{
		    		jfw_placehold_row.removeClass('vis-hide');
		    		jfw_col_required.removeClass('vis-hide');
				}
				
		    	if( value == 'checkbox' ){
		    		jfw_options_row.removeClass('hide');
		    	}else{
		    		jfw_options_row.addClass('hide');
		    	}

		    	if( value == 'radio' ){
		    		jfw_radio_row.removeClass('hide');
		    	}else{
		    		jfw_radio_row.addClass('hide');
		    	}

		    	if( value == 'select' ){
		    		jfw_select_row.removeClass('hide');
		    	}else{
		    		jfw_select_row.addClass('hide');
		    	}

		    	if( value == 'file' ){
		    		jfw_file_row.removeClass('hide');
		    	}else{
		    		jfw_file_row.addClass('hide');
		    	}

		    	if( value == 'file_multi' ){
		    		jfw_multi_file_row.removeClass('hide');
		    	}else{
		    		jfw_multi_file_row.addClass('hide');
		    	}

		    	//console.log(value);
		    }).trigger('change');

			setTimeout(function(){
				$('.field_type_select').trigger('change');
			}, 100);
		}
		


		//
		// Analytics
		//
		$('.jobs-metrics-agree, .jobs-metrics-cancel').click(function(e){
			e.preventDefault();

			var self = this;

			var status = 1;
			if( $(this).hasClass('jobs-metrics-cancel') ) status = 2;

			$(this).parent().find('.spinner').addClass('show');

			$.ajax({
		        url: jpsd.ajaxurl,
		        type: 'POST',
		        data: {
		        	action: 'jobs_metrics_notice_seen',
		        	status: status
		        },
		        success: function (data) {

		            if( data == 'ok' ){
		            	$(self).parents('.notice').slideUp();

		            	if( $(self).hasClass('insettings') ){
		            		$(self).fadeOut(300, function(){
		            			location.reload();
		            		});
		            	}
			        }

	    			return false;
		        }
		    });
		});


		//
		// File Storage Notice
		//
		$('.dismissAttachemntLocation').click(function(e){
			e.preventDefault();

			var self = this;

			$(this).parent().find('.spinner').addClass('show');

			$.ajax({
		        url: jpsd.ajaxurl,
		        type: 'POST',
		        data: {
		        	action: 'jobs_metrics_attachemnt_notice_seen',
		        	status: status
		        },
		        success: function (data) {

		            if( data == 'ok' ){
		            	$(self).parents('.notice').slideUp();

		            	if( $(self).hasClass('insettings') ){
		            		$(self).fadeOut(300, function(){
		            			location.reload();
		            		});
		            	}
			        }

	    			return false;
		        }
		    });
		});



		//
		// Job Posting Guide meta box
		//
		if( $('#job-postings-guide').length ){
			var bar_req 	= $('#job-postings-guide .job-completeness-required .bar');
			var bar_recom 	= $('#job-postings-guide .job-completeness-recommended .bar');
			var bar 		= $('#job-postings-guide .job-completeness-all .bar');

			function recalculate_jobs_guide(){

				var total_requireds 	= $('.job_tab_content .connectedSortable .jobs-row[data-need="1"]').length;
				var total_recommendeds 	= $('.job_tab_content .connectedSortable .jobs-row[data-need="2"]').length;
				var total_all 			= $('.job_tab_content .connectedSortable .jobs-row').length;

				var requireds 		= [];
				var recommendeds 	= [];
				var all 			= [];

				//console.info(total_requireds, total_recommendeds, total_all);

				$('.job_tab_content .connectedSortable .jobs-row').each(function(){
					var type 	= $(this).data('type');
					var need 	= $(this).data('need');
					var label 	= $(this).find('.jobs-row-label label').text();


					switch( type  ){
						case "text":
						case "valid_through":
							var input = '';
							$(this).find('.jobs-row-input .jp-input').each(function(){
								input += $(this).val();
							});
							//console.log( label, input );
							if(input){
								if( need == '1' ) {
									requireds.push( input );
								}else if( need == '2' ){
									recommendeds.push( input );
								}
								all.push( input );
							}
						break;


						case "empty_hiring_logo":
							var input = '';
							$(this).find('.jobs-row-input .jp-input').each(function(){
								input += $(this).val();
								input += $(this).attr('placeholder');
							});
							//console.log( label, input );
							if(input){
								if( need == '1' ) {
									requireds.push( input );
								}else if( need == '2' ){
									recommendeds.push( input );
								}
								all.push( input );
							}
						break;
						
						case "textarea":
							var input = '';
							$(this).find('.jobs-row-input .jp-textarea').each(function(){
								input += $(this).val();
							});
							//console.log( label, input );
							if(input){
								if( need == '1' ) {
									requireds.push( input );
								}else if( need == '2' ){
									recommendeds.push( input );
								}
								all.push( input );
							}
						break;

						case "empty_pdf_export":
						case "empty_apply_now":
						case "empty_date":
							all.push( 'in' );
						break;

						case "tinymce":
							var editor_id = $(this).find('.jobs-row-input .wp-editor-area').attr('id');

							//console.log('tinymce', tinymce, tinymce.editors);
							if( tinymce != null ){
								var editor_content = tinymce.get(editor_id).getContent();
								var input = '';
								if( editor_content != '' ) input = 'content in';
								//console.log( label, editor_content );
								if(input){
									if( need == '1' ) {
										requireds.push( input );
									}else if( need == '2' ){
										recommendeds.push( input );
									}
									all.push( input );
								}
							}
						break;

						case "location":
							var input = '';
							$(this).find('.jobs-row-input .jp-input').each(function(){
								input += $(this).val();
							});
							//console.log( label, input );
							if(input){
								if( need == '1' ) {
									requireds.push( input );
								}else if( need == '2' ){
									recommendeds.push( input );
								}
								all.push( input );
							}
						break;
					}
					
				});

				var req_percent = recalculate_get_percent(total_requireds, requireds.length);
				if( req_percent >= 0 && req_percent <= 100 ){
					bar_req.css('width', req_percent + '%');
				}
				
				var reco_percent = recalculate_get_percent(total_recommendeds, recommendeds.length);
				if( reco_percent >= 0 && reco_percent <= 100 ){
					bar_recom.css('width', reco_percent + '%');
				}

				var all_percent = recalculate_get_percent(total_all, all.length);
				if( all_percent >= 0 && all_percent <= 100 ){
					bar.css('width', all_percent + '%');
				}

				//console.info(requireds, recommendeds, all);
				//console.info(req_percent);
			}

			function recalculate_get_percent(total, number){
				return Number(number) / (Number(total) / 100);
			}

			recalculate_jobs_guide();

			setInterval(function(){
				recalculate_jobs_guide();
			}, 2500);
		} 

	});
})(jQuery);
