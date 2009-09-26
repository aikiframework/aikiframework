<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" dir='rtl'>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Uploadify Example Script</title>
<link href="default.css" rel="stylesheet" type="text/css" />
<link href="uploadify.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="jquery-1.3.2.min.js"></script>
<script type="text/javascript" src="swfobject.js"></script>
<script type="text/javascript" src="jquery.uploadify.v2.0.3.min.js"></script>
<script type="text/javascript">
$(document).ready(function() {

    	 
	$("#uploadify").uploadify({
		'uploader'       : 'uploadify.swf',
		'script'         : 'uploadify.php',
		'cancelImg'      : 'cancel.png',
		'folder'         : 'uploads',
		'queueID'        : 'fileQueue',
		'multi'          : true,
		onComplete: function () {
     	   $('#fileUploaded').html('تم رفع الملفات بنجاح');
     	 },
      onSelect: function () {
     		   	 keywords = $("#keywords").val() ;
   				 source_url = $("#source_url").val() ;
   				 article_id = $("#article_id").val() ;
   				 categorie = $("#categorie").val() ;
     		 $("#uploadify").uploadifySettings(
    		    'scriptData' , { 'keywords' : keywords , 'source_url' : source_url , 'article_id' : article_id, 'categorie' : categorie }
   		   );
		},
onError: function(a, b, c, d) {
            if (d.status == 404)
                alert('Could not find upload script. Use a path relative to: ' + '<?= getcwd() ?>');
            else if (d.type === "HTTP")
                alert("Error: "+d.type+" Info: "+d.info);
            else if (d.type === "File Size")
                alert(c.name + ' ' + d.type + ' Limit: ' + Math.round(d.sizeLimit / 1024) + 'KB');
            else
                alert('error ' + d.type + ": " + d.info);
        }		
	});
});
</script>
</head>

<body>
<table border="0" width="100%">
	<tbody>
		<tr>
			<td>نوع الصور</td>
			<td><select name='categorie' id='categorie'>
				<option value='1'>أرشيف الصور</option>
				<option value='4' selected="selected">صور ملحقة بالأخبار</option>
				<option value='5'>لوحات تشكيلية</option>
				<option value='6'>صور فوتوغرافية</option>
			</select></td>
		</tr>
		<tr>
			<td>الكلمات المفتاحية</td>
			<td><textarea name="keywords" id='keywords' cols="50" rows="7" ></textarea></td>
		</tr>
		<tr>
			<td>رابط المصدر</td>
			<td><input type="text" value="" name="source_url" id='source_url' /></td>
		</tr>
		<tr>
			<td>رقم المقال</td>
			<td><input type="text" value="" name="article_id" id='article_id' /></td>
		</tr>

		<tr>
			<td>الصور</td>
			<td>
			<div id="fileQueue"></div>
			<input type="file" name="uploadify" id="uploadify" /> <br />
			
			</td>
		</tr>

		<tr>
			<td colspan="2"><a
				href="javascript:$('#uploadify').uploadifyUpload();">رفع الصور</a></td>
		</tr>
	</tbody>
</table>
<div id='fileUploaded'></div>
</body>
</html>
