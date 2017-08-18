	/**
	 * @function add_field
	 * @argument {string} step 		The step name.
	 * @argument {array}  options 	The dropdown list items.
	 * @example
	 * 		add_field ('line', ['line 1', 'line 2', 'line 3']);
	 * @returns {} Appends field group to last dyn-gen div element.
	 **/
	function add_field ( step, category, options )
	{
		//Count the number of dynamically  generated fields. 
		var n = $('div.dyn-gen').length; //Exact count no ++ because the first one is a dummy.
		//console.log('Adding element #: [' + n + ']'); //Debug

		//Create elements to add to the form group.
		var grp  = $('<div>', {'class':'form-group field-ticketdata-val dyn-gen'});
		var lbl  = $('<label>', {'class':'control-label', 'for': 'ticketdata-val' + n }).html( step );
		var inp1 = $('<input>', {'id':'ticketdata-category' + n,'type':'text', 'class':'form-control category', 'name':'TicketData['+n+'][category]'}).val(category).attr('readonly', true).hide();
		var inp2 = $('<input>', {'id':'ticketdata-obj' + n,'type':'text', 'class':'form-control obj', 'name':'TicketData['+n+'][obj]'})
			.val(step)
			.attr('readonly', true)
			.attr('data-n', n) //for on change reference and append of extra input.
			.hide();
		var sel  = $('<select>',{'id':'ticketdata-val' + n, 'class':'form-control', 'name':'TicketData['+n+'][val]'}).append( $('<option></option>').val('').html('--Select--') );
		var lnk  = ( n > 1 )  ? $('<a>', { 'href':'#', 'class':'remove-items'}).html( '[ X ]' ) : '';
		var hlp  = $('<div>', {'class':'help-block'});
		
		//console.log('options:');
		//console.log(options);
		$.each(options, function(item, extra_input){
			var opt = $('<option>')
				.val( item )
				.html( item )
				.attr('data-append', extra_input)
				;
			sel.append(opt);
		});
		grp.append([lbl, inp1, inp2, sel, lnk, hlp]).on('change', function(){
			on_change( $(this) ); //Remove all fields after, get next field.
		});
		$('.form div.dyn-gen:last').after(grp); //.fadeIn('slow');
	}
	
	/**
	 * @description Removes current and all following elements.
	 * @argument {mix} el The inner form field element.
	 * @returns {}
	 **/
	function remove_field ( el )
	{
		// If a dynamic field exists:
		if($('div.dyn-gen').length > 1) {
			// Get and fade the wrapping field-group element.
			$( el ).parent().fadeOut(250, function(){
				//Remove field-groups after current.
				dyn = $( this ).nextAll('div.dyn-gen').remove();
				//Then remove current field-group.
				$(this).remove();
			});
		}
	}
	
	/**
	 * @function next_wf_step
	 * @argument {string} workflow	The selected process.
	 * @argument {string} step 	The 'obj' input field.
	 * @argument {string} val 	The 'val' of the dropdown field.
	 * @returns {} On success, executes add_field function.
	 **/
	function next_wf_step(workflow, step, val)
	{
		//console.log('wf:' + workflow + ' s:' + step + ' v:' + val);
		$.getJSON({ type: "POST", url: '/ticket/workflow/next-step',
			data: {
				workflow: workflow,
				step: step,
				val: val
			},
			success: function (data) {
				if (data.count > 0) {
					$('#new-ticket-button').attr('disabled', true); //Disable submit button.
					console.log(data);
					add_field( data.step, data.category, data.options ); //Append input group.
				} else {
					$('#new-ticket-button').removeAttr('disabled'); //Enable submit button.
				}
			},
			error: function (exception) {
				alert(exception.statusText + ': Contact the administrator.');
			}
		});
	}
	
	/**
	 * @function on_change
	 * @description Attached to dropdown element, triggered by on change.
	 * 	First disables the submit button, then gets the selected step
	 * 	and dropdown field values in order to fetch the next workflow step
	 * 	and dropdown values.
	 * @argument {obj} el The dropdown field.
	 **/
	function on_change( el )
	{
		//Get group object.
		el = $( el );
		$('#new-ticket-button').attr('disabled', true);
		wf   = $('#ticket-process option:selected').val();
		
		//Get step val within the current group.
		sel  = el.find('select'); //get select object for append extra input.
		step = el.find('input.obj').val();
		n    = el.find('input.obj').data('n'); //which count.
		val  = el.find('option:selected').val();
		app  = el.find('option:selected').data('append');
		console.log('wf:' + wf + ' step:'+ step + ' val:' + val + ' n:'+ n + ' append:' + app);
		
		//Destroy any extras if they exist.
		dyn = $( el ).nextAll('.extra').remove();
		//If append is set:
		if (app == 1) {
			// Tack on a input field.
			var lbl = $('<label>', {'class':'control-label extra', 'for': 'ticketdata-extra' + n }).html( 'Which ' + val + '?');
			var inp = $('<input>', {'id':'ticketdata-extra' + n,'type':'text', 'class':'form-control extra', 'name':'TicketData['+n+'][extra]'});
			$('.form div.dyn-gen:last').after( lbl.append(inp) );
			//$([lbl, inp]).insertAfter( el );
			//el.append(lbl, inp);
		} 
		
		//Remove anything after current dropdown group then get new selection options.
		dyn = $( el ).nextAll('div.dyn-gen').remove();
		if(val && val.length > 0) {
			next_wf_step(wf, step, val);
		}
	}
	
	/**
	 * @function {anonymous}
	 * @description Triggers removal of current and following field groups.
	 **/
	$('.form').on('click', '.remove-items', function(){
		el = $(this);
		remove_field( el );
	});
	
	
	/**
	 * @function {anonymous}
	 * @description Remove all dynamically generated content if the process
	 * 	dropdown field changes.
	 **/
	$( '#ticket-process' ).change( function(){
		$( 'div.dyn-gen' ).nextAll('div.dyn-gen').remove();
		wf_sel = $('option:selected',this);
		if(wf_sel.val().length) {
			next_wf_step(wf_sel.text(), null, null);
		}
	});
	
	
