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

function treeajaxify(file, targetwidget){ 
      $.get('admin_tools/edit/widgets/'+file,function(data) { 
              $(targetwidget).html(data);
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
      }); 
} 


function refreshthetree(){
	$.tree_reference('widgettree').refresh();
}


function formselector(group, ref_node, type){
	if (group == "url_or_widget"){
		
		if(!isNaN(ref_node)) {
			return "widget";
		}else{
			return "url";
		}
		
	}
}

function createtree(){

    var formoptions = { 
        target:        '#widget-form',   // target element(s) to be updated with server response 
        // beforeSubmit: showRequest, // pre-submit callback
        success:       refreshthetree  // post-submit callback
        // other available options:
        // url: url // override for form's 'action' attribute
        // type: type // 'get' or 'post', override for form's 'method' attribute
        // dataType: null // 'xml', 'script', or 'json' (expected server
		// response type)
        // clearForm: true // clear all form fields after successful submit
        // resetForm: true // reset the form after successful submit
         // $.ajax options can be used here too, for example:
        // timeout: 3000
    }; 
	
	
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
        	 treeajaxify(NODE.id , '#widget-form'); 
        },

		oncreate : function (NODE,REF_NODE,TYPE,TREE_OBJ,RB) {

		selector = formselector("url_or_widget", REF_NODE.id, TYPE);

			if(selector == 'url') {
					//alert(TYPE + REF_NODE.id);
				$("#widget-form").load("admin_tools/new/urls",  {limit: 25}, function(){
					$('#new_record_form').ajaxForm(formoptions); 
  		   		});
			}

			if(selector == 'widget'){
					//create widget
					$("#widget-form").load("admin_tools/new/widgets",  {limit: 25}, function(){
						$('#new_record_form').ajaxForm(formoptions); 
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
						$("#widget-form").load("admin_tools/delete/widgets/"+NODE.id+":aiki_widgets:yes",  {limit: 25}, function(){
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

				

 
		},
		
        onrename : function(NODE,LANG,TREE_OBJ,RB) {
        	alert(NODE.id);
        	alert(LANG);
        	alert(TREE_OBJ);
        	alert(RB);
         },
        
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