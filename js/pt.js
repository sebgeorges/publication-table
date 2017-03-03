jQuery(document).ready(function($) {

$('#pubsearch').keyup(function() {
	var searchField = $('#pubsearch').val();
	var myExp = new RegExp(searchField, "i");
	var row = '';
	$('.pubrow').each(function(){
		var cell = $(this).text();
		var therow = $(this);
		 if ((cell.search(myExp) == -1)) {
				
			 		therow.hide(400,'swing');

			 }else if ((cell.search(myExp) != -1)){

			 		therow.show(400,'swing');
			 }

	});
		
});

if (!$('.filter-section div input').length){
      $('.filter-section').detach();   
}
    
 })