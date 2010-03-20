/*
 * Aiki framework
 *
 * @author		Aikilab http://www.aikilab.com
 * @copyright  (c) 2008-2010 Aikilab
 * @license		http://www.fsf.org/licensing/licenses/agpl-3.0.html
 * @link		http://www.aikiframework.org
 */

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