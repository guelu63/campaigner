;var CAMPAIGNER = {
	url: null,
	ajax: false,
};
;(function($) {

    CAMPAIGNER.REQUEST = {
        subscribe: function()
        {
            $(".letterForm").submit(function() {
            	var data = $(".letterForm").serializeArray();
            	var ajax = {name: 'ajax', value: CAMPAIGNER.ajax};
            	data.push(ajax);
			    $.ajax({
			        type: "POST",
			        url: CAMPAIGNER.url,
			        data: data,
			        dataType: "html",
			        success: function($data) {
			        	$('.notice').html($data);
			        	// var link = $data;
			        	// $('#popupinfo').css({'display' : 'inline'});
			        	// jQuery('#popupinfo').delay(500).fadeOut();
			        	// if ($data) {window.location = link};
			        }
			    });
			    return false;
		    });
            console.log('URL ' + CAMPAIGNER.url);
        }
    }
})(jQuery);