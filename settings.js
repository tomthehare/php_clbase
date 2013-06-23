$(function() {

	  /////////////////
	 //  Variables  //
	/////////////////
	var submitBtn = $('#new_search_term_btn');
	var newSearchTermTxtBox = $('#new_search_term_txt');

	  //////////////////////
	 //  event handlers  //
	//////////////////////
	$('#new_search_term_btn').click(function() {

		disableButton(submitBtn);
		setValue(submitBtn, 'Working..');

		var newSearchLocationName = getNewSearchLocationText();

		if(newSearchLocationName)
		{
			makeInsertSearchLocationAjaxCall(newSearchLocationName);
		}
		else
		{
			//focus on text box, it would be nice to have a little pop up or something
		}

	});

	$('.del_btn').click(function() {
		var id = this.id;
		var delBtn = $('#' + id);

		disableButton(delBtn);
		setValue(delBtn, 'Deleting..');

		makeDeleteSearchLocationAjaxCall(id);
	});

	  /////////////////
	 //  functions  //
	/////////////////

	function disableButton(button) {
		button.prop('disabled', 'true');
	}

	function enableButton(button) {
		button.removeAttr('disabled');
	}

	function setValue(element, value) {
		element.prop('value', value);
	}

	function getNewSearchLocationText() {
		return $('#new_search_term_txt').val();
	}

	function clearText(element) {
		element.prop('value', '');
	}

	function makeInsertSearchLocationAjaxCall(location_name) {

		$.ajax({
			type:  "POST",
			url:   "insert_search_location.php",
			data:  {"location":location_name},
			dataType: "json",	
			success:   function(id) {
				if(id > 0)
				{
					appendTermToTable(id, location_name);
				}
			},
			error:     function(XMLHTTPRequest, textStatus, errorThrown) {
			    alert('error: ' + XMLHTTPRequest.responseText);
			}
		}).done(function() {
			clearText(newSearchTermTxtBox);
			enableButton(submitBtn);
			setValue(submitBtn, 'Submit');
		});
	}

	function makeDeleteSearchLocationAjaxCall(id_tag) {
		
		//strip the del_ off of the id tag
		var id_parts = id_tag.split('_');
		var id = id_parts[id_parts.length-1];

		$.ajax({
			method: 'POST',
			url: 'delete_search_location.php',
			data: {'id':id},
			dataType: 'json',
			success: function(return_id) {
				if(return_id > 0)
				{
					removeTermFromTable(return_id);
				}
			},
			error: function(XMLHTTPRequest, textStatus, errorThrown) {
			    alert('error: ' + XMLHTTPRequest.responseText);
			}
		});
	}

	function appendTermToTable(id, location_name) {
		var table = $('#settings_table');

		if(table)
		{
			var del_input = '<input type=\'button\' value=\'Delete\' class=\'del_btn\' id=\'del_'+id+'\' />';

			table.append("<tr id='row_" + id + "'><td>"+location_name+"</td><td>"+del_input+"</td></tr>");
		}
	}

	function removeTermFromTable(id) {
		var row = $('#row_' + id);

		row.remove();
	}
});