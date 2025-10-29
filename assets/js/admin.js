jQuery(function($){
	$('#bpxpi-import-form').on('submit', function(e){
		if (!confirm(bpxpi.confirm_import)) {
			e.preventDefault();
		}
	});
});
