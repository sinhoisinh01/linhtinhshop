/**
 * jQuery functions file for Pay To Post plugin
 *
 * Author: Colin Beeby
 */
$j = jQuery.noConflict();

$j(function(){

	//Handle the Add New Limit button
	$j('#add_new_payment').on('click', function(e){
		e.preventDefault();
		resetAddEditForm();
		$j('#add_rule_submit').css('display', 'block');
		$j('#edit_rule_submit').css('display', 'none');
		tb_show('New Pay To Post rule','TB_inline?height=320&width=275&inlineId=pay-to-post-tb');
		resizeThickbox();

		return false;
	});

	function resizeThickbox(){
		$j('#add-edit-pay-to-post-form').parent().parent().css({
			"max-width":"275px",
			"max-height":"315px"
		});
	}

	//Reset the add edit form back to default values
	function resetAddEditForm(){
		$j("#user_roles_select option[value='Contributor']").prop('selected', true);
		$j("#post_types_select option[value='Post']").prop('selected', true);
		$j("#cost").val('0.00');
	}


	//Handle the submit on the add/edit form
	$j('#add_rule_submit').on('click', function(e){
		e.preventDefault();
		addNewCostRule();
		tb_remove();
		return false;
	});

	//Add a new cost rule
	function addNewCostRule(){
		var tableRow = getRuleAsTableRow();
		$j('#rules_list').find('tbody').append(tableRow);
	}

	function getRuleAsTableRow(){
		var tableRow = $j('<tr>');
		
		var counter = existingRulesCount();
		
		var userRole = $j('#user_roles_select').find(':selected').text();
		var postType = $j('#post_types_select').find(':selected').val();
		var postTypeDisplay = $j('#post_types_select').find(':selected').text();
		var cost = $j('#cost').val();
		
		
		tableRow.append('<td>'+ userRole +'<input type="hidden" name="pay_to_post[rule][' + counter + '][user_role]" value="'+userRole+'"></td>');
		tableRow.append('<td>'+ postTypeDisplay +'<input type="hidden" name="pay_to_post[rule][' + counter + '][post_type]" value="' + postType + '"><input type="hidden" name="pay_to_post[rule][' + counter + '][post_type_display]" value="' + postTypeDisplay + '"></td>');
		tableRow.append('<td>' + cost + '<input type="hidden" name="pay_to_post[rule][' + counter + '][cost]" value="' + cost + '"></td>');
		tableRow.append('<td><span class="edit_rule button-primary">Edit</span> <span class="remove_rule button-primary">Remove</span></td>');

		tableRow.append('</tr>');
		
		return tableRow;
	}

	//Get a count of the number of existing limit rules
	function existingRulesCount(){
		var numberOfRows = $j('#rules_list tbody tr').length;
		
		return numberOfRows;
	}

	//The current row in the rules tableRow
	var currentRow;

	//Handle the edit rule button
	$j(document.body).on('click', '.edit_rule', function(e){
		e.preventDefault();
		
		currentRow = $j(this).parent().parent();
		
		populateAddEditForm();
		
		$j('#add_rule_submit').css('display', 'none');
		$j('#edit_rule_submit').css('display', 'block');
		
		tb_show('New Pay To Post rule','TB_inline?height=320&width=275&inlineId=pay-to-post-tb');
		resizeThickbox();
	});

	//Populate the add edit form with existing values
	function populateAddEditForm(){
		var existingValues = new Array(); 
		
		currentRow.find('input').each(function(index,value){
			existingValues[index] = $j(value).val();
		});
		
		var userRoleSelectIndex = 0;
		var postTypeSelectIndex = 1;
		var costIndex = 3;

		$j('#user_roles_select option[value="'+existingValues[userRoleSelectIndex]+'"]').prop('selected', true);
		$j('#post_types_select option[value="'+existingValues[postTypeSelectIndex].toLowerCase()+'"]').prop('selected', true);
		$j('#cost').val(existingValues[costIndex]);
	}

	//Handle the remove rule button
	$j(document.body).on('click', '.remove_rule', function(e){
		e.preventDefault();
		
		$j(this).parent().parent().remove();
		
		return false;
	});	

	//Handle the submit on the add/edit form
	$j('#edit_rule_submit').on('click', function(e){
		e.preventDefault();
		updateLimitRule();
		tb_remove();
		return false;
	});

	function updateLimitRule(){
		var tableRow = getRuleAsTableRow();
		
		currentRow.replaceWith(tableRow);
	}

});