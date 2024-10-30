jQuery(document).ready(function(){
	jQuery( '.wpsos_kdc_media_link' ).click(function( e ){
		e.preventDefault();
		tb_show('Keyword Count & Density', '#TB_inline?width=600&height=550&inlineId=wpsos-keyword-count','');
		jQuery('#TB_ajaxContent').width('100%');
		var data = {
				'action': 'get_word_counts',
				'text': jQuery("<div/>").html(jQuery('textarea#content').val()).text()
			};
			jQuery.post(ajaxurl, data, function(response) {
				jQuery('#TB_ajaxContent').html(response);
			});
	});
});