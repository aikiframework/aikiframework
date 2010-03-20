function  globalajaxify(file, targetwidget){
	$('<div id="loading_box">Loading please wait</div>').appendTo('body');
	$.get(file,function(data) {
		$(targetwidget).html(data);
		$('#loading_box').remove();
	});
}

$(document).ready(function(){
	$('a').live('click', function() {
		if($(this).attr('rel') && $(this).attr('href') && $(this).attr('rev')) {
			globalajaxify($(this).attr('href')+'?noheaders=true&noheaders=true&widget='+$(this).attr('rel'), $(this).attr('rev'));
			return false;
		}
	});
});