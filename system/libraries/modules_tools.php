<?php

/*
 * Aikiframework
 *
 * @author		Bassel Khartabil
 * @copyright	Copyright (C) 2009-2010 Aikilab inc
 * @license		http://www.gnu.org/licenses/gpl.html
 * @link		http://www.aikiframework.org
 */

if(!defined('IN_AIKICMS')){
	die('Welcome to Aiki CMS');
}

class Modules_Tools extends records_libs
{

	function EditMultiWidgetsInPlace(){
		global $db, $layout;

		$layout->widget_html .= '
<script type="text/javascript">
$(function() {
        
   $(".widget_name").editable("save.php?do=widget_name", { 
      indicator : "Loading...",
      tooltip   : "Click to edit...",
      id   : "widget_name_id",
      name : "widget_name_name",
      style   : "inherit"
      
  });
  
   $(".widget_group").editable("save.php?do=widget_group", { 
      indicator : "Loading...",
      id   : "widget_group_id",
      name : "widget_group_name",
      indicator : "Loading...",
      tooltip   : "Click to edit...",
      style   : "inherit"

  });  
  
   $(".widget_folder").editable("save.php?do=widget_folder", { 
      indicator : "Loading...",
      id   : "widget_folder_id",
      name : "widget_folder_name",
      indicator : "Loading...",
      tooltip   : "Click to edit...",
      style   : "inherit"

  });    
  
   $(".display_order").editable("save.php?do=display_order", { 
      indicator : "Loading...",
      id   : "display_order_id",
      name : "display_order_name",
      indicator : "Loading...",
      tooltip   : "Click to edit...",
      style   : "inherit"

  });      
  
     $(".style_id").editable("save.php?do=style_id", { 
      indicator : "Loading...",
      id   : "style_id_id",
      name : "style_id_name",
      indicator : "Loading...",
      tooltip   : "Click to edit...",
      style   : "inherit"

  });   
  
     $(".is_father").editable("save.php?do=is_father", { 
      indicator : "Loading...",
      id   : "is_father_id",
      name : "is_father_name",
      indicator : "Loading...",
      tooltip   : "Click to edit...",
      style   : "inherit"

  });   
  
  
     $(".father_widget").editable("save.php?do=father_widget", { 
      indicator : "Loading...",
      id   : "father_widget_id",
      name : "father_widget_name",
      indicator : "Loading...",
      tooltip   : "Click to edit...",
      style   : "inherit"

  });    

       $(".father_module").editable("save.php?do=father_module", { 
      indicator : "Loading...",
      id   : "father_module_id",
      name : "father_module_name",
      indicator : "Loading...",
      tooltip   : "Click to edit...",
      style   : "inherit"

  });    
  

       $(".dis_operators").editable("save.php?do=dis_operators", { 
      indicator : "Loading...",
      id   : "dis_operators_id",
      name : "dis_operators_name",
      indicator : "Loading...",
      tooltip   : "Click to edit...",
      style   : "inherit"

  });      
  

    $(".aiki_select").editable("save.php?do=aiki_select", { 
      indicator : "Loading...",
        type      : "autogrow",
        submit    : "OK",
        cancel : "cancel",
        id   : "aiki_select_id",
        name : "aiki_select_name",
        tooltip   : "Click to edit...",
        onblur    : "ignore",
        
    });  
  


    $(".widget").editable("save.php?do=widget", { 
      indicator : "Loading...",
        type      : "autogrow",
        submit    : "OK",
        cancel : "cancel",
        id   : "widget_id",
        name : "widget_name",
        tooltip   : "Click to edit...",
        onblur    : "ignore",
                
    });

    
    $(".nogui_widget").editable("save.php?do=nogui_widget", { 
      indicator : "Loading...",
        type      : "autogrow",
        submit    : "OK",
        cancel : "cancel",
        id   : "nogui_widget_id",
        name : "nogui_widget_name",
        tooltip   : "Click to edit...",
    	onblur    : "ignore",        
        
    });  

    $(".if_authorized").editable("save.php?do=if_authorized", { 
      indicator : "Loading...",
        type      : "autogrow",
        submit    : "OK",
        cancel : "cancel",
        id   : "if_authorized_id",
        name : "if_authorized_name",
        tooltip   : "Click to edit...",
        onblur    : "ignore",
        
    });    

       $(".display_in_row_of").editable("save.php?do=display_in_row_of", { 
      indicator : "Loading...",
      id   : "display_in_row_of_id",
      name : "display_in_row_of_name",
      indicator : "Loading...",
      tooltip   : "Click to edit...",
      style   : "inherit"

  });   

      $(".records_in_page").editable("save.php?do=records_in_page", { 
      indicator : "Loading...",
      id   : "records_in_page_id",
      name : "records_in_page_name",
      indicator : "Loading...",
      tooltip   : "Click to edit...",
      style   : "inherit"

  }); 

  
      $(".dynamic_pagetitle").editable("save.php?do=dynamic_pagetitle", { 
      indicator : "Loading...",
      id   : "dynamic_pagetitle_id",
      name : "dynamic_pagetitle_name",
      indicator : "Loading...",
      tooltip   : "Click to edit...",
      style   : "inherit"

  });   

  
      $(".pagetitle").editable("save.php?do=pagetitle", { 
      type      : "autogrow",   
      submit    : "OK",
      cancel : "cancel",   
      indicator : "Loading...",
      id   : "pagetitle_id",
      name : "pagetitle_name",
      tooltip   : "Click to edit...",
      onblur    : "ignore",      


  });  

  
      $(".is_admin").editable("save.php?do=is_admin", { 
      indicator : "Loading...",
      id   : "is_admin_id",
      name : "is_admin_name",
      indicator : "Loading...",
      tooltip   : "Click to edit...",
      style   : "inherit"

  });   

  
      $(".permissions").editable("save.php?do=permissions", { 
      indicator : "Loading...",
      id   : "permissions_id",
      name : "permissions_name",
      indicator : "Loading...",
      tooltip   : "Click to edit...",
      style   : "inherit"

  });     
  
  
      $(".javascript").editable("save.php?do=javascript", { 
      indicator : "Loading...",
      id   : "javascript_id",
      name : "javascript_name",
      indicator : "Loading...",
      tooltip   : "Click to edit...",
      style   : "inherit"

  });       

});

</script>
		';

		$records_in_page = "0";
		$recordscount = $db->get_var("SELECT count(*) FROM aiki_widgets");
		if ($records_in_page > 0){
			$pages_number = ($recordscount / $records_in_page) + 1;
		}

		$layout->widget_html .= '
<table class="toolboxsortable table-autosort:0 table-autopage:'.$records_in_page.' table-stripeclass:alternate" dir="ltr" border="0" cellpadding="2"
	cellspacing="2" id="page">
<thead>
	<tr>
		<th class="table-sortable:numeric">id</th>
		<th class="table-sortable:default">Widget name</th>
		<th class="table-sortable:default">Widget group</th>
		<th class="table-sortable:default">Widget folder</th>
		<th class="table-sortable:numeric">Display_order</th>
		<th class="table-sortable:default">Style id</th>
		<th class="table-sortable:default">is father</th>
		<th class="table-sortable:numeric">Father widget</th>
		<th class="table-sortable:numeric">Father module</th>
		<th class="table-sortable:default">Display operators</th>
		<th class="table-sortable:default">SQL select</th>
		<th>widget</th>
		<th>nogui widget</th>
		<th>if authorized widget</th>
		<th class="table-sortable:numeric">Display in row of</th>
		<th class="table-sortable:numeric">Records in page</th>
		<th class="table-sortable:default">Dynamic pagetitle</th>
		<th class="table-sortable:default">Pagetitle</th>
		<th class="table-sortable:numeric">Require Admin</th>
		<th class="table-sortable:default">Permissions</th>
		<th class="table-sortable:default">Javascript</th>

</tr>
	<tr>
		<th>Filter:</th>
		<th><input name="filter" size="8" onkeyup="Table.filter(this,this)"></th>
		<th>
		<select onchange="Table.filter(this,this)">
		<option value="function(){return true;}">All</option>
		<option value="aiki_shared">aiki_shared</option>
		
		'; 
		$site_groups = $db->get_results("SELECT site_shortcut from aiki_sites order by BINARY site_shortcut");
		foreach ($site_groups as $site_group){
			$layout->widget_html .= '<option value="'.$site_group->site_shortcut.'">'.$site_group->site_shortcut.'</option>';
		}
		$layout->widget_html .= '
		</select>
		</th>
		
		<th><input name="filter" size="8" onkeyup="Table.filter(this,this)"></th>
		<th><input name="filter" size="8" onkeyup="Table.filter(this,this)"></th>
		<th><input name="filter" size="8" onkeyup="Table.filter(this,this)"></th>
		<th><input name="filter" size="8" onkeyup="Table.filter(this,this)"></th>
		<th><input name="filter" size="8" onkeyup="Table.filter(this,this)"></th>
		<th><input name="filter" size="8" onkeyup="Table.filter(this,this)"></th>
		<th><input name="filter" size="8" onkeyup="Table.filter(this,this)"></th>
		<th><input name="filter" size="8" onkeyup="Table.filter(this,this)"></th>
		<th></th>
		<th></th>
		<th></th>
		<th><input name="filter" size="8" onkeyup="Table.filter(this,this)"></th>
		<th><input name="filter" size="8" onkeyup="Table.filter(this,this)"></th>
		<th><input name="filter" size="8" onkeyup="Table.filter(this,this)"></th>
		<th><input name="filter" size="8" onkeyup="Table.filter(this,this)"></th>
		<th><input name="filter" size="8" onkeyup="Table.filter(this,this)"></th>
		<th><input name="filter" size="8" onkeyup="Table.filter(this,this)"></th>		
		<th><input name="filter" size="8" onkeyup="Table.filter(this,this)"></th>		
	</tr>
</thead>
<tbody>
';

		$get_results = $db->get_results("SELECT * from aiki_widgets");
		foreach ($get_results as $result){
			//$result->widget = html_entity_decode($result->widget, ENT_QUOTES, 'UTF-8');
			//$result->widget = htmlspecialchars_decode($result->widget);
			$result->widget = str_replace('%3E', '>', $result->widget);
			$result->widget = str_replace('%5B', '[', $result->widget);
			$result->widget = str_replace('%5D', ']', $result->widget);
			$result->widget = str_replace('%29', ')', $result->widget);
			$result->widget = str_replace('%28', '(', $result->widget);

			$layout->widget_html .= "<tr>
			<td>".$result->id."</td>
			<td><p class=\"widget_name\"  style=\"height: 50px\" id=\"".$result->id."\">".$result->widget_name."</p></td>
			<td><p class=\"widget_group\"  style=\"height: 50px\" id=\"".$result->id."\">".$result->widget_group."</p></td>
			<td><p class=\"widget_folder\"  style=\"height: 50px\" id=\"".$result->id."\">".$result->widget_folder."</p></td>
			<td><p class=\"display_order\"  style=\"height: 50px\" id=\"".$result->id."\">".$result->display_order."</p></td>
			<td><p class=\"style_id\"  style=\"height: 50px\" id=\"".$result->id."\">".$result->style_id."</p></td>
			<td><p class=\"is_father\"  style=\"height: 50px\" id=\"".$result->id."\">".$result->is_father."</p></td>
			<td><p class=\"father_widget\"  style=\"height: 50px\" id=\"".$result->id."\">".$result->father_widget."</p></td>
			<td><p class=\"father_module\"  style=\"height: 50px\" id=\"".$result->id."\">".$result->father_module."</p></td>
			<td><p class=\"dis_operators\"  style=\"height: 50px\" id=\"".$result->id."\">".$result->dis_operators."</p></td>
			<td><p class=\"aiki_select\"  style=\"height: 100%\" id=\"".$result->id."\">".$result->aiki_select."</p></td>
			<td><div class=\"widget\" style=\"height: 100%\" id=\"".$result->id."\">".$result->widget."</div></td>
			<td><div class=\"nogui_widget\"  style=\"height: 100%\" id=\"".$result->id."\">".$result->nogui_widget."</div></td>
			<td><div class=\"if_authorized\"  style=\"height: 100%\" id=\"".$result->id."\">".$result->if_authorized."</div></td>
			<td><p class=\"display_in_row_of\"  style=\"height: 50px\" id=\"".$result->id."\">".$result->display_in_row_of."</p></td>
			<td><p class=\"records_in_page\"  style=\"height: 50px\" id=\"".$result->id."\">".$result->records_in_page."</p></td>
			<td><p class=\"dynamic_pagetitle\"  style=\"height: 50px\" id=\"".$result->id."\">".$result->dynamic_pagetitle."</p></td>
			<td><div class=\"pagetitle\"  style=\"height: 100%\" id=\"".$result->id."\">".$result->pagetitle."</div></td>
			<td><p class=\"is_admin\"  style=\"height: 50px\" id=\"".$result->id."\">".$result->is_admin."</p></td>
			<td><p class=\"permissions\"  style=\"height: 50px\" id=\"".$result->id."\">".$result->permissions."</p></td>
			<td><p class=\"javascript\"  style=\"height: 50px\" id=\"".$result->id."\">".$result->javascript."</p></td>
			
			</tr>";
		}
		$layout->widget_html .= "</tbody>";
		if ($records_in_page > 0){
			$layout->widget_html .= '
<tfoot>
	<td colspan="21">

		<a href="#" onclick="pageexample(\'previous\'); return false;">&lt;&lt;&nbsp;Previous</a>';

			for ($i=1; $i<$pages_number; $i++){
				$j = $i -1 ;
				$layout->widget_html .= '<a href="#" id="page'.$i.'" class="pagelink" onclick="pageexample('.$j.'); return false;">'.$i.'</a>';
			}
			$layout->widget_html .= '<a href="#" onclick="pageexample(\'next\'); return false;">Next&nbsp;&gt;&gt;
	</td>
</tfoot>';
		}
		$layout->widget_html .="</table>";
	}

	/////////////////////////////////////////////////////
	function EditMultiRecordsInPlace(){
		global $db, $layout;

		$layout->widget_html .= '
<script type="text/javascript" charset="utf-8">
$(function() {
        
   $(".click").editable("save.php?do=updateCss", { 
      indicator : "Loading...",
      tooltip   : "Click to edit...",
      id   : "css_id",
      name : "css_name",
      style   : "inherit"
      
  });


    $(".autogrow").editable("save.php?do=updateCss", { 
      indicator : "Loading...",
        type      : "autogrow",
        submit    : "OK",
         cancel : "cancel",
        tooltip   : "Click to edit...",
        onblur    : "ignore",
        
    });


});

</script>
		';
		$records_in_page = "0";
		$recordscount = $db->get_var("SELECT count(*) FROM aiki_css");
		if ($records_in_page > 0){
			$pages_number = ($recordscount / $records_in_page) + 1;
		}

		$layout->widget_html .= '
<table class="toolboxsortable table-autosort:0 table-autopage:'.$records_in_page.' table-stripeclass:alternate" dir="ltr" border="0" cellpadding="2"
	cellspacing="2" id="page">
	<thead>
	<tr>
		<th class="table-sortable:numeric">id</th>
		<th class="table-sortable:default">css name</th>
		<th class="table-sortable:default">Css Group</th>
		<th class="table-sortable:default">css folder</th>
		<th>Style Sheet</th>
	</tr>
	<tr>
		<th>Filter:</th>
		<th><input name="filter" size="8" onkeyup="Table.filter(this,this)"></th>
		<th>
		<select onchange="Table.filter(this,this)">
		<option value="function(){return true;}">All</option>
		<option value="aiki_shared_style">aiki_shared_style</option>
		'; 
		$site_groups = $db->get_results("SELECT site_shortcut from aiki_sites order by BINARY site_shortcut");
		foreach ($site_groups as $site_group){
			$layout->widget_html .= '<option value="'.$site_group->site_shortcut.'">'.$site_group->site_shortcut.'</option>';
		}
		$layout->widget_html .= '
		</select>
		</th>
		<th><input name="filter" size="8" onkeyup="Table.filter(this,this)"></th>
		<th></th>
	</tr>	
</thead>
<tbody>	
';
		$get_results = $db->get_results("SELECT * from aiki_css ORDER BY id ASC");

		foreach ($get_results as $result){
			$layout->widget_html .= "<tr >
			<td>".$result->id."</td>
			<td><p class=\"click\"  style=\"height: 50px\" id=\"".$result->id."\">".$result->css_name."</p></td>
			<td>".$result->css_group."</td>
			<td>".$result->css_folder."</td>
			
			<td >
			<div class=\"autogrow\" id=\"".$result->id."\">".$result->style_sheet."</div>
			</td>
			
			</tr>";
		}
		$layout->widget_html .= "</tbody>";
		if ($records_in_page > 0){
			$layout->widget_html .= '
<tfoot>
	<td colspan="5">

		<a href="#" onclick="pageexample(\'previous\'); return false;">&lt;&lt;&nbsp;Previous</a>';

			for ($i=1; $i<$pages_number; $i++){
				$j = $i -1 ;
				$layout->widget_html .= '<a href="#" id="page'.$i.'" class="pagelink" onclick="pageexample('.$j.'); return false;">'.$i.'</a>';
			}
			$layout->widget_html .= '<a href="#" onclick="pageexample(\'next\'); return false;">Next&nbsp;&gt;&gt;
	</td>
</tfoot>';
		}
		$layout->widget_html .= "</table>";
	}


}
?>