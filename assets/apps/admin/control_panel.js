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
				
				code_mirror();
				code_mirror_if_authorized();
				$('#edit_form').ajaxForm(function() { 
					
					stop = 0;
					
					$("#widget-form").html("Edited widget successfully");
					
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
    	    	  $('#widget-form').html(data);
    	    	  if (refreshtree == 1){
    	    	    $.tree_reference('databaseformstree').refresh();
    	    	    
    	    	    refreshtree = 0
    	    	  }
    	    });
    	    		
    	      
    		  
    	  }else{
 if (stop == 1){
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