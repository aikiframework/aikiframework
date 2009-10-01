function globalajaxify(file, targetwidget){ 

		      $('<div class="loading"></div>').html("Loading please wait").appendTo('body').fadeIn(); 
		      $.get(file,function(data) { 
		          $(targetwidget).slideUp('slow',function(){ 
		              $(this).html(data).slideDown('slow',function(){ 
		                  $('.loading').fadeOut('slow',function(){$(this).remove();}); 
		              }); 
		          }); 
		      }); 
}


function code_mirror(){
	
    htmleditor = CodeMirror.fromTextArea('widget', {
        height: "350px",
        parserfile: ["parsexml.js", "parsecss.js", "tokenizejavascript.js", "parsejavascript.js",
                     "tokenizephp.js", "parsephp.js", "parsephphtmlmixed.js"],
        stylesheet: ["assets/plugins/codemirror/css/xmlcolors.css", "assets/plugins/codemirror/css/jscolors.css", "assets/plugins/codemirror/css/csscolors.css", "assets/plugins/codemirror/css/phpcolors.css"],
        path: "assets/plugins/codemirror/js/",
        continuousScanning: 500,
        lineNumbers: true,
      }); 
    
    sqleditor = CodeMirror.fromTextArea('normal_select', {
        height: "250px",
        parserfile: "parsesparql.js",
        stylesheet: ["assets/plugins/codemirror/css/sparqlcolors.css"],
        path: "assets/plugins/codemirror/js/",
      });  
	
}


function refreshthetree(){
	$.tree_reference('widgettree').refresh();
}

function createtree(){

    var formoptions = { 
        target:        '#widget-form',
        success:       refreshthetree  
    }; 
	
	 
   $("#create_new_url").click(function(event){
		$("#widget-form").load("admin_tools/new/3",  {limit: 25}, function(){
			
			var current_form = $("#new_record_form").html();
			
			$('#new_record_form').ajaxForm(function() { 
				refreshthetree();
				$("#new_record_form").html(current_form + "Added new url successfully");
            }); 
		});	
    });	
   
   $("#create_new_widget").click(function(event){
		$("#widget-form").load("admin_tools/new/2",  {limit: 25}, function(){
			
			var current_form = $("#new_record_form").html();
			
			$('#new_record_form').ajaxForm(function() { 
				refreshthetree();
				$("#new_record_form").html(current_form + "Added new widget successfully");
				code_mirror();
           }); 
			code_mirror();
		});	
   });	   
    
    
   $("#widgettree").tree( {
      
      data  : {
        type  : "xml_flat",
        url   : "assets/apps/admin/jstree.php",
      },
      
      rules : {
        deletable : "all",
        draggable : "all"      	
      },
      
      callback : {
        onselect : function(NODE,TREE_OBJ) {
    	  if (isNaN(NODE.id)){
    	  
    	  }else{

    	      $.get('admin_tools/edit/2/'+NODE.id,function(data) { 
                  $('#widget-form').html(data);
                  $('#edit_form').ajaxForm(formoptions);
                  code_mirror();
    	      });
    		  
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
					//delete url	
					}else{
						$("#widget-form").load("admin_tools/delete/2/"+NODE.id+":aiki_widgets:yes",  {limit: 25}, function(){
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

function system_accordion(){
	$("#system_accordion").accordion({
		fillSpace: true
	});
}

function structur_accordion(){
		$("#structur_accordion").accordion({
			fillSpace: true
});
createtree(); 
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

	$(document).ready( function() {

		// PAGE LAYOUT
		outerLayout = $('body').layout({
			applyDefaultStyles:	true
			// AUTO-RESIZE Accordion widget when west pane resizes
		,	west__onresize:		function () { $("#structur_accordion").accordion("resize"); }
		,	west__onopen:		function () { $("#structur_accordion").accordion("resize"); }
		,	center__onresize:	function () { $("#accordion-center").accordion("resize"); }
		,	center__onopen:		function () { $("#accordion-center").accordion("resize"); }
		,	west__size:			400
		});

		outerLayout.addToggleBtn('.help-toggler', 'east');


		// ACCORDION - inside the West pane
		structur_accordion();

		widget_accordion();

	});	
	
	//global ajaxify
	 $("a").click(function(event){

		  if($(this).attr('rel') && $(this).attr('href') && $(this).attr('ajax')) {
		      globalajaxify($(this).attr("rel"), $(this).attr("href")); 

		  return false; 
		  }
			 
	 });	
	 	 
	
});