/*
_steps global js array.
*/
var network = null;

$('i.glyphicon-refresh-animate').hide();

$('.btn-add-step').click(function (e) {
    e.preventDefault();
	var $this = $(this);
    var target = $this.data('target');
	var step = $('input.step[data-target="' + target + '"]').val();
	var category = $(' select.category[data-target="' + target + '"] option:selected').text();
	var step_type = $(' select.step_type[data-target="' + target + '"] option:selected').text();
    if (step && step.length && step_type && step_type.length) {
        $this.children('i.glyphicon-refresh-animate').show();
		$.post($this.attr('href'), {step: step, category: category, step_type:step_type}, function (r) {
			//Convert JSON string to array.
			data = $.parseJSON(r);
			//Display any errors.
			if(data.errors.length > 0) displayAlert( data.errors );
			console.log(r);
			get_steps();
        })
		.success(function() {
			$('input.step[data-target="' + target + '"]').val('');
		})
		.always(function () {
            $this.children('i.glyphicon-refresh-animate').hide();
        });
    }
	$('i.glyphicon-refresh-animate').hide();
	
    return false;
});

$('.btn-update-step').click(function (e) {
    e.preventDefault();
	var $this = $(this);
    //Get step form data.
	var step_id_pk = $('input.step_id_pk[data-target="steps"]').val();
	var step 	   = $('input.step[data-target="steps"]').val();
	var category  = $('select.category[data-target="steps"] option:selected').val();
	//console.log('Cat: ' + category );
	var step_type  = $('select.step_type[data-target="steps"] option:selected').val();
	
	//append_input = append_input ? append_input : null;
	//Validate input.
	if( !step_id_pk || !step_id_pk.length){
		alert('No step selected.');
		return;
	}
	if( !step || !step.length){
		alert('No step name entered.');
		return;
	}
	
	//if( !category || !category.length){
	//	alert('No category selected.');
	//	return;
	//}
	if(!(step_type && step_type.length)) {
		alert('No step type selected.');
		return;
	}
	
	if (step_id_pk && step && step_type) {
		data = {
			step_id_pk: step_id_pk,
			category: category,
			step: step,
			step_type: step_type,
		};
		$this.children('i.glyphicon-refresh-animate').show();
		$.post($this.attr('href'), data, function (r) {
			//Convert JSON string to array.
			data = $.parseJSON(r);
			//Display any errors.
			if(data.errors.length > 0) displayAlert( data.errors );
			console.log(r);
        })
		.success(function() {
			get_steps();
		})
		.always(function () {
            $this.children('i.glyphicon-refresh-animate').hide();
        });
    }
	return false;
});
$('.btn-delete-step').click(function (e) {
    e.preventDefault();
	var $this = $(this);
    var target = $this.data('target');
	var step_id_pk = $(' select.list[data-target="steps"] option:selected').val();
	var step 	= $(' select.list[data-target="steps"] option:selected').text();
    if (step_id_pk && step_id_pk.length) {
		var r = confirm('Delete step: "' + step + '" ?');
		if (r === true) {
			$this.children('i.glyphicon-refresh-animate').show();
			$.post($this.attr('href'), {step_id_pk: step_id_pk}, function (r) {
				console.log(r);
			})
			.success(function() {
				get_steps();
				$('input.step[data-target="' + target + '"]').val('');
			})
			.always(function () {
				$this.children('i.glyphicon-refresh-animate').hide();
			});
		} 
    }
	return false;
});

$('select.list[data-target="steps"]').change(function() {
	var step_id_pk = $('option:selected', this).val();
	var step = $('option:selected', this).data('step');
	var step_type = $('option:selected', this).data('step_type');
	var category = $('option:selected', this).data('category');
	
	$('input.step_id_pk[data-target="steps"]').val(step_id_pk);
	$('input.step[data-target="steps"]').val(step);
	$('select.category[data-target="steps"]').val(category.toLowerCase());
	$('select.step_type[data-target="steps"]').val(step_type.toLowerCase());
	get_items( step_id_pk );
	focusNode( step_id_pk );
});

$('.btn-get-steps').click(function (e) {
	e.preventDefault();
	clear_step_form();
	$('select.list[data-target="items"]').html(''); //Clear item list.
    get_steps();
});

function clear_step_form()
{
	$('input.step_id_pk[data-target="steps"]').val('');
	$('input.step[data-target="steps"]').val('');
	$('select.step_type[data-target="steps"]').val('');
}

function get_steps()
{
	var workflow_id_pk = $('input.workflow_id_pk[data-target="steps"]').val();
	var url = '/ticket/workflow/get-steps?id=' + workflow_id_pk  ;
	$.getJSON(url, {}, function (r) {
		_steps = r.steps;
		console.log( r );
		update_steps();
		update_to_step_dropdown();
	}).always(function () {
		refreshWorkflow();
	});
}

function update_steps() {
    var $list = $('select.list[data-target="steps"]');
    $list.html('<option></option>');
    var groups = {
        start: [$('<optgroup label="Start">'), false],
        flow:  [$('<optgroup label="Flow">'),  false],
        end:   [$('<optgroup label="End">'),   false],
    };
	$.each(_steps, function (i, row) {
		var group = row.step_type.toLowerCase();
		$('<option>').text('['+row.step_id_pk+'] '+ row.step + ' (' + row.category +')')
		.val(row.step_id_pk)
		.attr('data-step', row.step)
		.attr('data-category', row.category)
		.attr('data-step_type', row.step_type)
		.appendTo(groups[group][0]);
    });
	$.each(groups, function () {
		if (this) {
			$list.append(this);
        }
    });
}

//Update to_step dropdown of the item form.
function update_to_step_dropdown() {
    var $list = $('select.to_step_id_fk[data-target="items"]');
	$list.html('<option></option>');
    var groups = {
        start: [$('<optgroup label="Start">'), false],
        flow:  [$('<optgroup label="Flow">'),  false],
        end:   [$('<optgroup label="End">'),   false],
    };
	$.each(_steps, function (i, row) {
		var group = row.step_type.toLowerCase();
		$('<option>').text('['+row.step_id_pk+'] '+row.step + ' (' + row.category +')')
			.val(row.step_id_pk)
			.attr('data-step_type', row.step_type.toLowerCase())
			.appendTo(groups[group][0]);
    });
	$.each(groups, function () {
		if (this) {
			$list.append(this);
        }
    });
}
	



$('.btn-add-item').click(function (e) {
    e.preventDefault();
	
	var $this = $(this);
    var target = $this.data('target');
	//Get item form data.
	var step_id_fk 	  = $('select.list[data-target="steps"] option:selected').val();
	var item 		  = $('input.item[data-target="' + target + '"]').val();
	var to_step_id_fk = $('select.to_step_id_fk[data-target="items"] option:selected').val();
	var append_input  = $("input.append_input[type='checkbox'][data-target='items']:checked").length;
	
	//Validate input.
	if( !step_id_fk || !step_id_fk.length){
		alert('No step selected.');
		return;
	}
	if(!(item && item.length)) {
		alert('No item entered.');
		return;
	}
	
	if (item && item.length) {
        $this.children('i.glyphicon-refresh-animate').show();
		data = {
			step_id_fk: step_id_fk,
			item: item,
			to_step_id_fk: to_step_id_fk,
			append_input: append_input
		};
		$.post($this.attr('href'), data, function (r) {
			//Convert JSON string to array.
			data = $.parseJSON(r);
			//Display any errors.
			if(data.errors.length > 0) displayAlert( data.errors );
			console.log( r ); 
			get_items( step_id_fk );
        })
		.success(function() {
			$('input.item[data-target="items"]').val('');
			$("input.append_input[type='checkbox'][data-target='items']").removeAttr('checked');
			//$('select.to_step_id_fk[data-target="items"]')[0].selectedIndex = 0;
		})
		.always(function () {
            $this.children('i.glyphicon-refresh-animate').hide();
        });
    }
	$('i.glyphicon-refresh-animate').hide();
	
    return false;
});


$('.btn-update-item').click(function (e) {
    e.preventDefault();
	
	var $this = $(this);
    //var target = $this.data('target');
	var step_id_fk 	  = $('select.list[data-target="steps"] option:selected').val();
	//console.log('step_id_fk: ' + step_id_fk);
	//Get item form data.
	var item_id_pk 	  = $('input.item_id_pk[data-target="items"]').val();
	var item 		  = $('input.item[data-target="items"]').val();
	var to_step_id_fk = $('select.to_step_id_fk[data-target="items"] option:selected').val();
	console.log('to_step_id_fk: ' + to_step_id_fk);
	var append_input  = $("input.append_input[type='checkbox'][data-target='items']:checked").length;
	
	//append_input = append_input ? append_input : null;
	//Validate input.
	if( !item_id_pk || !item_id_pk.length){
		alert('No item selected.');
		return;
	}
	if( !step_id_fk || !step_id_fk.length){
		alert('No step selected.');
		return;
	}
	if(!(item && item.length)) {
		alert('No item entered.');
		return;
	}
	
	if (item_id_pk && item_id_pk.length && item && item.length) {
        $this.children('i.glyphicon-refresh-animate').show();
		data = {
			step_id_fk: step_id_fk,
			item_id_pk: item_id_pk,
			item: item,
			to_step_id_fk: to_step_id_fk,
			append_input: append_input
		};
		$.post($this.attr('href'), data, function (r) {
			//Convert JSON string to array.
			data = $.parseJSON(r);
			//Display any errors.
			if(data.errors.length > 0) displayAlert( data.errors );
			console.log( r );
			get_items( step_id_fk );
        })
		.success(function() {
			get_items( step_id_fk );
		})
		.always(function () {
            $this.children('i.glyphicon-refresh-animate').hide();
			refreshWorkflow();
			//focusNode(step_id_fk + '.' + item_id_pk);
        });
    }
	return false;
});

$('.btn-delete-item').click(function (e) {
    e.preventDefault();
	var $this = $(this);
    var target = $this.data('target');
	var item_id = $(' select.list[data-target="' + target + '"] option:selected').val();
	var item 	= $(' select.list[data-target="' + target + '"] option:selected').text();
	var step_id_fk = $('select.list[data-target="steps"] option:selected').val();
	
    if (item_id && item_id.length) {
		var r = confirm('Delete item: "' + item + '" ?');
		if (r === true) {
			$this.children('i.glyphicon-refresh-animate').show();
			$.post($this.attr('href'), {item_id: item_id}, function (r) {
				get_items( step_id_fk );
			})
			.success(function() {
				$('input.item[data-target="' + target + '"]').focus();
			})
			.always(function () {
				$this.children('i.glyphicon-refresh-animate').hide();
			});
		} 
    }
	return false;
});


$('.btn-get-items').click(function (e) {
    e.preventDefault();
	var step_id_fk 	  = $('select.list[data-target="steps"] option:selected').val();
	if(step_id_fk && step_id_fk.length ){
		get_items(step_id_fk);
	}
	else{
		clear_item_form();
	}
	return false;
});

$('select.list[data-target="items"]').click(function() {
	var item_id = $('option:selected', this).val();
	var item = $('option:selected', this).text();
	var to_step_id_fk = $('option:selected', this).data('to_step_id_fk');
	var append_input = $('option:selected', this).data('append_input') == 1 ? true : false;
	$('input.item_id_pk[data-target="items"]').val(item_id);
	$('input.item[data-target="items"]').val(item);
	$('select.to_step_id_fk[data-target="items"]').val(to_step_id_fk);
	$("input.append_input[type='checkbox'][data-target='items']").prop('checked', append_input);
	
	var step_id_fk 	  = $('select.list[data-target="steps"] option:selected').val();
	//console.log('step_id: ' + step_id_fk + ';  item_id: ' + item_id);
	focusNode( step_id_fk + '.' + item_id );
});

function get_items( step_id )
{
	var url = '/ticket/workflow/get-items?id=' + step_id;
	clear_item_form();
	$.getJSON(url, {}, function (r) {
		console.log( r );
		_items = r.items;
		update_items();
	}).always(function () {
		//refreshWorkflow();
	});
}

function clear_item_form()
{
	$('input.item_id_pk[data-target="items"]').val('');
	$('input.item[data-target="items"]').val('');
	$('select.to_step_id_fk[data-target="items"]').val('');
	$("input.append_input[type='checkbox'][data-target='items']").removeAttr('checked');
}

function update_items() {
    var $list = $('select.list[data-target="items"]');
    $list.html('');
    $.each(_items, function (i, row) {
		append_input = row.append_input ? 1 : 0;
		to_step = return_step_by_id(row.step_id_fk);
		$('<option>').text(row.item).val(row.item_id_pk)
		.attr('data-to_step_id_fk', row.to_step_id_fk )
		.attr('data-append_input', row.append_input )
		.attr('data-item', row.item )
		.appendTo( $list );
    });
}

function return_step_by_id (step_id_fk)
{
	var step_name = '';
	$.each(_steps, function(i, row) {
		if (row.step_id_pk == step_id_fk) {
			step_name = row.step;
			return false;
		}
	});
	return step_name;
}

function refreshWorkflow()
{
	var workflow_id_pk = $('input.workflow_id_pk[data-target="steps"]').val();
	var url = '/ticket/workflow/get-workflow?id=' + workflow_id_pk;
	
	var node_data = [];
	var edge_data = [];
	
	$.getJSON(url, {}, function (r) {
		_workflow = r.workflow;
		_steps = r.steps;
		
		$.each(_steps, function(i, row) {
			
			var node_shape = 'box';
			var node_color = 'black';
			var node_font_color = 'white';
			var node_border_color = 'blue';
			
			if (row.step_type == 'Flow') {
				node_shape = 'ellipse';
				node_color = 'white';
				node_font_color = 'blue';
			} else if (row.step_type == 'Start') {
				node_shape = 'ellipse';
				node_color = 'green';
				node_border_color = 'green';
				node_font_color = 'white';
			} else if (row.step_type == 'End') {
				node_shape = 'ellipse';
				node_color = 'red';
				node_border_color = 'red';
				node_font_color = 'white';
			} else{
				node_shape = 'box';
				node_color = 'black';
				node_font_color = 'white';
				node_border_color = 'blue';
			}

			var obj = {
				id: row.step_id_pk,
				label:  row.step,
				shape:  node_shape,
				color:  {background:node_color, border: node_border_color},
				font: {
					color: node_font_color,
					//size: 20
					
				}
			};
			node_data.push(obj);
			
		});
		
		$.each(_workflow, function(i, row) {
			
			var node_color = 'blue';
			var node_border_color = 'blue';
			var node_font_color = 'white';
			
			if (row.append_input == '1') {
				node_font_color = 'black';
				node_color = 'orange';
				//node_border_color = 'orange';
			}
			
			//Add item nodes.
			var obj = {
				id: row.step_item_id,
				label: row.item,
				shape: 'box',
				color:  {background:node_color, border: node_border_color},
				font: {
					color: node_font_color
				}
			};
			node_data.push(obj);
			//Step to item edge.
			obj = {
				from: row.step_id_pk,
				to: row.step_item_id
			};
			edge_data.push(obj);
			//Item to next-step edge.
			obj = {
				from: row.step_item_id,
				to: row.to_step_id_fk
			};
			edge_data.push(obj);
		});
		
		//Add unique steps.
		//var rs = jQuery.inArray( "John", arr )
		//console.log( rs );
	})
	.always(function () {
		updateWorkflow(node_data, edge_data);
	});
}

function updateWorkflow (node_data, edges_data)
{
	var container = document.getElementById('mynetwork');
	container.innerHTML = 'Loading';

	//node_data.push({id: 1000, y: 100, x: 500, label: 'Internet', group: 'internet', value: 1, fixed: true, physics:false});
	//node_data.push({id: 1001, y: 0, x: 500, label: 'Switch', group: 'switch', value: 1, fixed: true,  physics:false});
	//node_data.push({id: 1002, y: 0, x: 0, label: 's', group: 's', value: 1, fixed: true,  physics:false});
	
	// create an array with nodes
	var nodes = new vis.DataSet(node_data);
	// create an array with edges
	var edges = new vis.DataSet(edges_data);
	// legend
	
	
	// create a network
	var data = {
		nodes: nodes,
		edges: edges
	};
	var options = {
		layout: {
			hierarchical: {
				enabled: true,
				direction: "LR",
				sortMethod: "directed",
				levelSeparation: 200,
				nodeSpacing: 50,
				treeSpacing: 50,
				blockShifting: true,
			}
		},
		nodes: {
			borderWidth: 2
		},
		interaction: {
			hover: true
		}
	};
	network = new vis.Network(container, data, options);
}

function focusNode( nodeId )
{
	//var xWidth = document.getElementById('mynetwork').offsetWidth;
	//var x = -(xWidth/2-50);
	var options = {
	  scale: 1,
	  offset: {x: 0 , y:0},
	  animation: {
		duration: 1000,
		easingFunction: 'easeInOutQuad'
	  }
	};
	network.focus(nodeId, options);
	//network.selectNodes(nodeId, true);
}

function displayAlert(errors)
{
	var error_str = '';
	$.each(errors, function( index, value ) {
		error_str += "<b>" + index + "</b>: " + value + '<br/>';
	});
	var msgObj = '';
	msgObj += '<div class="alert alert-warning alert-dismissible" role="alert">';
    msgObj += ' <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>';
    msgObj += ' <strong>Error!</strong><div>' + error_str + '</div>';
    msgObj += '</div>';
	$('#messages').html(msgObj).show().fadeOut(6000);
}
get_steps();
refreshWorkflow();

/*
var Workflow = {

	init: function(givenNumber) {
	  var val = 0;
	
	  this.options = val;
	  return this;
	},
	
	divideBy: function(_divider) {
	  this.number = (this.number / _divider);
	  return this;
	},
	
	result: function () {
	  return this.number;
	}
}
*/


