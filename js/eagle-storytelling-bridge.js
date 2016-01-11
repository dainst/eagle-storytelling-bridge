jQuery(document).ready(function() {

      
	jQuery('#esa_multifilter')
	.autocomplete({
		minLength: 2,
		source: function(request, response) {
			jQuery('#esa_multifilter_selected').val('');
			
			//console.log('source: ' + jQuery('#esa_multifilter_select').val());
			
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
		  //console.log('item:', ui.item);
		  jQuery('#esa_multifilter_selected').val(ui.item.itemid);
		},

    })
    .data("ui-autocomplete")._renderItem = function(ul, item) {
        return jQuery("<li></li>")
		        .data("item.autocomplete", item)
		        .append(item.label)
		        .appendTo(ul);
	};
    
	jQuery('#esa_multifilter').on('focus', function(ev){
		//jQuery('#esa_multifilter').data('ui-autocomplete')._trigger('select', 'autocompleteselect', {item:{value:jQuery(this).val()}})
		jQuery('#esa_multifilter').autocomplete('search', '###top###');
	});
	
	
	jQuery('#esa_multifilter_select').on('change', function(ev) {
		var xxx = {
			'users': 'Enter author\'s name',
			'keywords': 'Enter a keyword',
			'language': 'Enter a language'
		}
		jQuery('#esa_multifilter').attr('placeholder', xxx[jQuery('#esa_multifilter_select').val()]);
		jQuery('#esa_multifilter').val('');
		jQuery('#esa_multifilter').autocomplete('search', '###top###');
		jQuery('#esa_multifilter').focus();
	});
	
});