/*
 * Aiki framework
 *
 * @author		Aikilab http://www.aikilab.com
 * @copyright  (c) 2008-2010 Aikilab
 * @license		http://www.fsf.org/licensing/licenses/agpl-3.0.html
 * @link		http://www.aikiframework.org
 */

function code_mirror_if_authorized(){
	    
	   var if_authorized = CodeMirror.fromTextArea('if_authorized', {
	        height: "350px",
	        parserfile: ["parsexml.js", "parsecss.js", "tokenizejavascript.js", "parsejavascript.js",
	                     "tokenizephp.js", "parsephp.js", "parsephphtmlmixed.js"],
	        stylesheet: ["assets/javascript/codemirror/css/xmlcolors.css", "assets/javascript/codemirror/css/jscolors.css", "assets/javascript/codemirror/css/csscolors.css", "assets/javascript/codemirror/css/phpcolors.css"],
	        path: "assets/javascript/codemirror/js/",
	        continuousScanning: 500,
	        lineNumbers: true,
	      }); 

	}

function code_mirror(){
	
   var htmleditor = CodeMirror.fromTextArea('widget', {
        height: "400px",
        parserfile: ["parsexml.js", "parsecss.js", "tokenizejavascript.js", "parsejavascript.js",
                     "tokenizephp.js", "parsephp.js", "parsephphtmlmixed.js"],
        stylesheet: ["assets/javascript/codemirror/css/xmlcolors.css", "assets/javascript/codemirror/css/jscolors.css", "assets/javascript/codemirror/css/csscolors.css", "assets/javascript/codemirror/css/phpcolors.css"],
        path: "assets/javascript/codemirror/js/",
        continuousScanning: 500,
        lineNumbers: true,
      }); 
    
   var sqleditor = CodeMirror.fromTextArea('normal_select', {
        height: "250px",
        parserfile: "parsesparql.js",
        stylesheet: ["assets/javascript/codemirror/css/sparqlcolors.css"],
        path: "assets/javascript/codemirror/js/"
      });  
	
}


function refreshthetree(tree){
	$.tree_reference(tree).refresh();
}

function create_form(selector, id, name, code, tree){
   
	$(selector).click(function(event){
		$('#tools_box').remove();
			$("#widget-form").load("admin_tools/new/"+id,  {limit: 25}, function(){
				
				var current_form = $("#new_record_form").html();
				if (code == 1){
					code_mirror();
					}
				$('#new_record_form').ajaxForm(function() { 
					refreshthetree(tree);

					$("#widget-form").html("Added new " + name + " successfully");

	           }); 

			});	
	   });		
}

function mod_basic(){
	
	$('#tools_box').remove();
	
	hide_advanced();
	hide_permissions();
	$('.display_urls').show();
	
	
	$('<div id="tools_box" style="background: none repeat scroll 0pt 0pt rgb(221, 221, 221); border-width: 1px 0pt 0pt; border-style: solid none none; border-color: rgb(170, 170, 170) -moz-use-text-color -moz-use-text-color; margin-left: auto; margin-right: auto; position: relative; top: 47px; z-index: 10000; width: 269px;">' +
'<ul style="cursor:pointer;">' +
'<li style="display:block; float: left; padding:5px;" class="widget_editor_li" id="widget_tools"><a>Widget</a>' +
'<li style="display:block; float: left; padding:5px;" class="widget_editor_li" id="sql_tools"><a>Sql</a>' +
'<li style="display:block; float: left; padding:5px;" class="widget_editor_li" id="style_tools"><a>Style</a></li>' +
'<li style="display:block; float: left; padding:5px;" class="widget_editor_li" id="permissions_tools"><a>Permissions</a>' +
'<li style="display:block; float: left; padding:5px;" class="widget_editor_li" id="advanced_mod"><a>Advanced</a>' +
'<li style="display:block; float: left; padding:5px;" class="widget_editor_li" id="full_mod"><a>All</a>' +
'</li></ul></div>').appendTo('body');
	
	$("#style_tools").click(function(event){
		
		$('.widget_editor_li').css('background','#FFFFFF');
		$(this).css('background','#CCCCCC');

		show_style();
		hide_sql();
		hide_permissions();
		hide_advanced();
		$('.widget').hide();
	});	 
	
	$("#widget_tools").click(function(event){
		
		$('.widget_editor_li').css('background','#FFFFFF');
		$(this).css('background','#CCCCCC');
		
		hide_permissions();
		hide_style();
		hide_advanced();
		hide_sql();
		$('.widget').show();
	});
	
	$("#permissions_tools").click(function(event){
		$('.widget_editor_li').css('background','#FFFFFF');
		$(this).css('background','#CCCCCC');
		hide_sql();
		show_permissions();
		hide_style();
		hide_advanced();
	    $('.widget').hide();

	});
	
	$("#advanced_mod").click(function(event){
		$('.widget_editor_li').css('background','#FFFFFF');
		$(this).css('background','#CCCCCC');

		hide_sql();
		hide_style();
		hide_permissions();
		$('.widget').hide();
		show_advanced();
		
	});
	
	$("#full_mod").click(function(event){
		$('.widget_editor_li').css('background','#FFFFFF');
		$(this).css('background','#CCCCCC');

		show_style();
		show_sql();
		show_permissions();
		show_advanced();
		$('.widget').show();
	});	
	
	$("#sql_tools").click(function(event){
		$('.widget_editor_li').css('background','#FFFFFF');
		$(this).css('background','#CCCCCC');

		show_sql();	
		$('.widget').hide();
	});	
	
}

function show_sql(){
	$('.normal_select').show();
	$('.records_in_page').show();
	$('.display_in_row_of').show();
	$('.if_no_results').show();
	$('.link_example').show();
}

function hide_sql(){
	$('.normal_select').hide();
	$('.records_in_page').hide();
	$('.display_in_row_of').hide();
	$('.if_no_results').hide();
	$('.link_example').hide();
}


function hide_advanced(){
	$('.widget_site').hide();
	$('.widget_target').hide();
	$('.widget_type').hide();
	$('.kill_urls').hide();
	$('.nogui_widget').hide();
	$('.pagetitle').hide();
	$('.remove_container').hide();
	$('.widget_cache_timeout').hide();
	$('.custome_output').hide();
	$('.custome_header').hide();
	$('.is_father').hide();
	$('.father_widget').hide();
	$('.display_urls').hide();
	
}

function show_advanced(){
	$('.widget_site').show();
	$('.widget_target').show();
	$('.widget_type').show();
	$('.kill_urls').show();
	$('.nogui_widget').show();
	$('.pagetitle').show();
	$('.remove_container').show();
	$('.widget_cache_timeout').show();
	$('.custome_output').show();
	$('.custome_header').show();
	$('.is_father').show();
	$('.father_widget').show();
	$('.display_urls').show();
}


function hide_permissions(){
	$('.authorized_select').hide();
	$('.is_admin').hide();
	$('.if_authorized').hide();
	$('.permissions').hide();
}

function show_permissions(){
	$('.authorized_select').show();
	$('.is_admin').show();
	$('.if_authorized').show();
	$('.permissions').show();
}


function show_style(){
	$('.css').show();
	$('.style_id').show();
	$('.display_order').show();

}

function hide_style(){
	$('.css').hide();
	$('.style_id').hide();
	$('.display_order').hide();
}



function urls_widgets_tree(){

    var formoptions = { 
        target:        '#widget-form',
        success:       refreshthetree  
    }; 
	
    create_form("#create_new_widget", 1, "Widget", 1, "widgettree");   
    
   var stop = 1;
   
   $("#widgettree").tree( {
	   
   
      data  : {
        type  : "xml_flat",
        url   : "assets/apps/admin/urls_widgets.php"
      },
      
      rules : {
        deletable : "all",
        draggable : "all"      	
      },
      
      callback : {
        onselect : function(NODE,TREE_OBJ) {
    	  
    	    	  
    	  if (isNaN(NODE.id)){
    	  
    	  }else{
    		 
  if (stop == 1){
			$("#widget-form").load('admin_tools/edit/20/'+NODE.id,  {limit: 25}, function(){
				
				mod_basic();
				
				code_mirror();
				code_mirror_if_authorized();
				$('#edit_form').ajaxForm(function() { 
					
					stop = 0;
					
					$("#widget-form").html("Edited widget successfully");

					$('#tools_box').remove();
					
					$.tree_reference('widgettree').refresh();
					
					stop = 1;
					
	           }); 

			});	
  }
    		  
    	  }
        },

	
		beforedelete    : function(NODE, TREE_OBJ,RB) { 
				
		$("#deletewidgetdialog").dialog({
			bgiframe: true,
			resizable: false,
			height:140,
			modal: true,
			overlay: {
				backgroundColor: '#000',
				opacity: 0.5
			},
			close: function(event, ui) {
				$(this).dialog('destroy');
			},
			buttons: {
				'Delete widget': function() {


					if(isNaN(NODE.id)) {

					}else{
						$("#widget-form").load("admin_tools/delete/20/"+NODE.id+":yes",  {limit: 25}, function(){
							$.tree_reference('widgettree').refresh();
						    $('#tools_box').remove();
     					});
					}
     				
     				$(this).dialog('close');
     				$(this).dialog('destroy');
     				
				},
				Cancel: function() {
					$(this).dialog('close');
					$(this).dialog('destroy');
				}
			}
		});

				

 
		}
        
       }

    } );
}


function database_forms_tree(){

    var formoptions = { 
        target:        '#widget-form'
 
    }; 
	
    var refreshtree = 1;
    var stop = 1;
    
   //create_form("#create_new_table", 3, "Table", 0);
   create_form("#create_new_form", 6, "Form", 0, "databaseformstree");   
    
    
   $("#databaseformstree").tree( {
      
      data  : {
        type  : "xml_flat",
        url   : "assets/apps/admin/database_forms.php"
      },
      
      rules : {
        deletable : "all",
        draggable : "all"      	
      },
      
      callback : {
        onselect : function(NODE,TREE_OBJ) {
    	  if (isNaN(NODE.id)){
    	 
    	      $.get('admin_tools/auto_generate/'+NODE.id,function(data) { 
   	    	    $('#tools_box').remove();
    	    	  $('#widget-form').html(data);
    	    	  if (refreshtree == 1){
    	    	    $.tree_reference('databaseformstree').refresh();
    	    	    
    	    	    refreshtree = 0
    	    	  }
    	    });
    	    		
    	      
    		  
    	  }else{
 if (stop == 1){
	    $('#tools_box').remove();

	    $("#widget-form").load('admin_tools/array/id/form_name/form_array/aiki_forms/'+NODE.id,  {limit: 25}, function(){

    					$('#edit_form').ajaxForm(function() { 
    						
    						stop = 0;
    						
    						$("#widget-form").html("Edited form successfully");
    						
    						$.tree_reference('databaseformstree').refresh();

    						stop = 1;
    						
    		           }); 

    				});	
    	  }    		  
  		  
    	  }
        }
       }

    } );
}


function config_tree(){

    var formoptions = { 
        target:        '#widget-form'
 
    }; 
	
    var refreshtree = 1;
    var stop = 1;
    

    $('#tools_box').remove();
    
   //create_form("#create_new_table", 3, "Table", 0);
   //create_form("#create_new_form", 6, "Form", 0, "databaseformstree");   
    
    
   $("#configtree").tree( {
      
      data  : {
        type  : "xml_flat",
        url   : "assets/apps/admin/config.php"
      },
      
      rules : {
        deletable : "all",
        draggable : "all"      	
      },
      
      callback : {
        onselect : function(NODE,TREE_OBJ) {

 if (stop == 1){
	    $('#tools_box').remove();

    				$("#widget-form").load('admin_tools/array/config_id/config_type/config_data/aiki_config/'+NODE.id,  {limit: 25}, function(){

    					$('#edit_form').ajaxForm(function() { 
    						
    						stop = 0;
    						
    						$("#widget-form").html("Edited config successfully");
    						
    						$.tree_reference('configtree').refresh();

    						stop = 1;
    						
    		           }); 

    				});	
    	  }    		  
  		  
    	  
        }
       }

    } );
}


function system_accordion(){
	$("#system_accordion").accordion({
		fillSpace: true
	});
	config_tree();	
}

function structur_accordion(){
		$("#structur_accordion").accordion({
			fillSpace: true
});
		$("#structur_button").addClass('active');
		urls_widgets_tree(); 
}

function widget_accordion(){
	$("#widget_accordion").accordion({
		fillSpace: true
	});

}

$().ready(function() {
	$("#dialog").dialog({ autoOpen: false });
	$("#aiki-icon-button").click(function(){
		$("#dialog").dialog('open');
	});
	
	var outerLayout; // init global vars

		// PAGE LAYOUT
		outerLayout = $('body').layout({
			applyDefaultStyles:	true
			// AUTO-RESIZE Accordion widget when west pane resizes
		,	west__onresize:		function () { $("#structur_accordion").accordion("resize"); }
		,	west__onopen:		function () { $("#structur_accordion").accordion("resize"); }
		,	center__onresize:	function () { $("#accordion-center").accordion("resize"); }
		,	center__onopen:		function () { $("#accordion-center").accordion("resize"); }
		,	west__size:			300
		});

		structur_accordion();

		widget_accordion();

	 	 
	   $("#database_forms").click(function(event){
		   database_forms_tree();
	   });	 
	   
	   $("#urls_widgets").click(function(event){
		   urls_widgets_tree();
	   });	   
	   
	   	   
	   
});