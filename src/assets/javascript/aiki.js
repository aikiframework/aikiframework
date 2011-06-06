/*
 * Aiki framework
 *
 * @author		Aikilab http://www.aikilab.com
 * @copyright  (c) 2008-2011 Aiki Lab Pte Ltd
 * @license		http://www.fsf.org/licensing/licenses/agpl-3.0.html
 * @link		http://www.aikiframework.org
 */

var stop = 0;

function  globalajaxify(file, targetwidget){

if (stop != 1){
	stop = 1;
	$('<div id="loading_box"><span>Loading...</span></div>').hide().appendTo(targetwidget).fadeIn("fast");
	$.get(file,function(data) {
		$('#loading_box').fadeOut("fast", function() { $(this).remove(); 
		$(targetwidget).hide().fadeIn(500).html(data);
		$('form.edit_form').ajaxForm(function() { $("form.edit_form").html("Edited successfully"); });
		});
		stop = 0;
	});
}
}

$(document).ready(function(){

	$('a').live('click', function() {
		if( $(this).attr('rel') &&  $(this).attr('href') && $(this).attr('rev')) {
                globalajaxify($(this).attr('href')+'?noheaders=true&noheaders=true&widget='+$(this).attr('rel'), $(this).attr('rev'));
                return false;            
        }    
	});
	
    $('span').live('click', function() {
		if($(this).attr('rel') ) {
            if ($(this).attr('rev')) {
                globalajaxify('?noheaders=true&noheaders=true&widget='+$(this).attr('rel'), $(this).attr('rev'));
                return false;
            }
          }  
	});
});
