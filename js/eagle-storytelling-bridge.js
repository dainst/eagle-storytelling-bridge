jQuery(document).ready(function() {

      
	jQuery('#esa_multifilter')


	.autocomplete({
		minLength: 2,
		source: function(request, response) {
			jQuery('#esa_multifilter_selected').val('');
			
			console.log('source: ' + jQuery('#esa_multifilter_select').val());
			
			jQuery.ajax({
			  dataType: "json",
			  url: esa.ajax_url,
			  method: 'POST',
			  data: {
				action: 'esa_autocomplete',
				set: jQuery('#esa_multifilter_select').val(),
				q: request.term
			  },

			  success: response 
			});

		},
		select: function(event, ui) {
		  console.log('heyo', ui.item);
		  jQuery('#esa_multifilter_selected').val(ui.item.itemid);
		},

    }).data("ui-autocomplete")._renderItem = function(ul, item) {
        return jQuery("<li></li>")
		        .data("item.autocomplete", item)
		        .append(item.label)
		        .appendTo(ul);
	};
    
});