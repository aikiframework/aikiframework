/** Remove the content */
function updateRemoveContent() {
	$("#update-script").remove();
	$("#update-content").remove();
	$("#update-check").remove();
	$("#update-decompress").remove();
	$("#update-validate").remove();
}

/** this gets called by backup.php */
function updateOverwrite(isSuccess) {
	$("#update-script").remove();
	if (isSuccess) {
		// backup is done
		$("#update-content").append('Done');
		//updateRemoveContent();
		//$("#update-dialog").append('<pre style="line-height:normal;" id="update-content">Creating Backup... </pre>');
		$.ajax({
			  url: 'assets/apps/admin/update/backup.php?archive=' + archive,
			  success: function(data) {
			    $('#update-content').append(data);
			  }
		});
	}
	else {
		// backup failed
		$("#update-content").append('Failed');
	}
}

/** this gets called by decompress.php */
function updateBackup(archive, isSuccess) {
	$("#update-script").remove();
	if (isSuccess) {
		// decompress is done
		$("#update-decompress").append('Done');
		updateRemoveContent();
		$("#update-dialog").append('<pre style="line-height:normal;" id="update-content">Creating Backup... </pre>');
		$.ajax({
			  url: 'assets/apps/admin/update/backup.php?archive=' + archive,
			  success: function(data) {
			    $('#update-content').append(data);
			  }
		});
	}
	else {
		// validate failed
		$("#update-decompress").append('Failed');
	}
}

/** this gets called by validate.php */
function updateDecompress(archive, isSuccess) {
	$("#update-script").remove();
	if (isSuccess) {
		// validate is done
		$("#update-validate").append('Done');
		$("#update-dialog").append('<pre style="line-height:normal;" id="update-decompress">Decompressing... </pre>');
		$.ajax({
			  url: 'assets/apps/admin/update/decompress.php?archive=' + archive,
			  success: function(data) {
			    $('#update-content').append(data);
			  }
		});
	}
	else {
		// validate failed
		$("#update-validate").append('Failed');
	}
}

/** this gets called by download.php */
function updateValidate(sum, archive, isSuccess) {
	$("#update-script").remove();
	if (isSuccess) {
		// download is done
		$("#update-content").append('Done');
		$("#update-dialog").append('<pre style="line-height:normal;" id="update-validate">Validating... </pre>');
		$.ajax({
			  url: 'assets/apps/admin/update/validate.php?sum=' + sum + '&archive=' + archive,
			  success: function(data) {
			    $('#update-content').append(data);
			  }
		});
	}
	else {
		// download failed
		$("#update-content").append('Failed');
	}
}

/** This gets called when the download update button is clicked.
 * Attempts to download the update.
 * @return void */
function updateDownload(version) {
	updateRemoveContent();
	$("#update-dialog").append('<pre style="line-height:normal;" id="update-content">Downloading... </pre>');
	$.ajax({
		  url: 'assets/apps/admin/update/download.php?aiki=' + version,
		  success: function(data) {
		    $('#update-content').append(data);
		  }
	});
}

/** This gets called when Update button is clicked. */
function updateCheck() {
	updateRemoveContent();
	$("#update-dialog").append('<pre style="line-height:normal;" id="update-content">Checking For Update... </pre>');
	$("#update-dialog").dialog('open');
	$.ajax({
		  url: 'assets/apps/admin/update/check.php',
		  success: function(data) {
		    $('#update-dialog').append(data);
		  }
	});
}

// append the update button to the main navigation
$("<li><a href='#' id='update-button'>Update</a></li>").appendTo("#main-navigation");
$("<div id='update-dialog' title='Update'></div>").appendTo("#header");
$("#update-dialog").dialog({width: 'auto', height: 'auto', autoOpen: false});
// when the update button is clicked,
// display the update interface.
$("#update-button").click(function(event) {
	updateCheck();
});