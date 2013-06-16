$(function() {
	function disableButton(button) {
	button.prop('disabled', 'true');
	}

	function enableButton(button) {
	button.removeAttr('disabled');
	}

	function setValue(element, value) {
	element.prop('value', value);
	}

	$(".delete_button").click(function() {
	    var id_parts = this.id.split('_');
	    var id = id_parts[id_parts.length-1];
	    var del_btn = $("#del_btn_" + id);
	    var fav_btn = $("#fav_btn_" + id);

	    //turn off buttons for the time being.
	    disableButton(fav_btn);
	    disableButton(del_btn);
	    del_btn.prop('value', 'Deleting...');

	    //ajax call using id
	    $.ajax({
	      type: "POST",
	      url: "deletemanager.php",
	      data: {'lid':id},
	      dataType: "json",
	      success: function(data) {
	        if(data == 'true')
	        {
	          del_btn.prop('value', 'Deleted!');
	        }
	        else
	        {
	          enableButton(del_btn);
	          enableButton(fav_btn);
	          del_btn.prop('value', 'Delete');
	          alert('Something went wrong. - ' + data);
	        }
	      },
	      error: function(XMLHTTPRequest, textStatus, errorThrown) {
	        alert('error: ' + XMLHTTPRequest.responseText);
	      }
	    });
	});

	$(".favorite_button").click(function() {
		var id_parts = this.id.split('_');
		var id = id_parts[id_parts.length-1];
		var fav_btn = $("#fav_btn_" + id);
		var del_btn = $("#del_btn_" + id);
		var favOriginalValue = fav_btn.val();

		//disable buttons
		disableButton(fav_btn);
		disableButton(del_btn);
		setValue(fav_btn, 'Saving...');

		//ajax call using id
		$.ajax({
		  type: "POST",
		  url: "favoritemanager.php",
		  data: {'lid':id},
		  dataType: "json",
		  success: function(data) {
		    enableButton(fav_btn);
		    setValue(fav_btn, data);
		    enableButton(del_btn);
		    //alert('favorited ' + id);
		  },
		  error: function(XMLHTTPRequest, textStatus, errorThrown) {
		    enableButton(fav_btn);
		    enableButton(del_btn);
		    setValue(fav_btn, favOriginalValue);
		    alert('error: ' + XMLHTTPRequest.responseText);
		  }
		});
	});
});