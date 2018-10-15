<?php
    if (isset($_FILES['attachments'])) {
        $msg = "";
        $dir = explode("_", $_FILES['attachments']['name'][0]);
        if (!is_dir("/var/www/sites/_expedientes/" . $dir[0])) {
          mkdir("/var/www/sites/_expedientes/" . $dir[0]);
        }
        $_FILES['attachments']['name'][0] = str_replace($dir[0] . "_", "", $_FILES['attachments']['name'][0]);
        $targetFile = "/var/www/sites/_expedientes/" . $dir[0] . "/" . basename($_FILES['attachments']['name'][0]);
        if (file_exists($targetFile)) {
          $msg = array("status" => 0, "msg" => "File already exists!");
        }
        else if (move_uploaded_file($_FILES['attachments']['tmp_name'][0], $targetFile)) {
          $msg = array("status" => 1, "msg" => "File Has Been Uploaded", "path" => "../_expedientes/" . $dir[0] . "/" . basename($_FILES['attachments']['name'][0]);
        }
        exit(json_encode($msg));
    }
?>
<html>
	<head>
		<title>Carga de fotografias</title>
		<style type="text/css">
			#dropZone {
				border: 3px dashed #0088cc;
				padding: 50px;
				width: 500px;
				margin-top: 20px;
			}

			#files {
				border: 1px dotted #0088cc;
				padding: 20px;
				width: 200px;
				display: none;
			}

            #error {
                color: red;
            }
		</style>
	</head>
	<body>
		<center>
			<img src="images/siapa.png"><br><br>
			<div id="dropZone">
				<h1>Arrastra y suelta tus carpetas....</h1>
				<input type="file" id="fileupload" name="attachments[]" multiple>
			</div>
			<h1 id="error"></h1><br><br>
			<h1 id="progress"></h1><br><br>
			<div id="files"></div>
		</center>

		<script src="https://code.jquery.com/jquery-3.2.1.min.js" integrity="sha256-hwg4gsxgFZhOsEEamdOYGBf13FyQuiTwlAQgxVSNgt4=" crossorigin="anonymous"></script>
		<script src="js/vendor/jquery.ui.widget.js" type="text/javascript"></script>
		<script src="js/jquery.iframe-transport.js" type="text/javascript"></script>
		<script src="js/jquery.fileupload.js" type="text/javascript"></script>
        <script type="text/javascript">
            $(function () {
               var files = $("#files");

               $("#fileupload").fileupload({
                   url: 'index.php',
                   dropZone: '#dropZone',
                   dataType: 'json',
                   autoUpload: false,
               }).on('fileuploadadd', function (e, data) {
                 // console.log(data);
                   var fileTypeAllowed = /.\.(gif|jpg|png|jpeg)$/i;
                   var fileName = data.originalFiles[0]['name'];
                   var fileSize = data.originalFiles[0]['size'];
                   //data.originalFiles[0]['name'] = data.originalFiles[0]['relativePath'] + fileName;

                   if (!fileTypeAllowed.test(fileName)) {
                        $("#error").html('Only images are allowed!');
                   }
                   else if (fileSize > 50000000) {
                       $("#error").html('Your file is too big! Max allowed size is: 50MB');
                   }
                   else {
                       $("#error").html("");
                       var count = data.files.length;
                       var i;
                       for (i = 0; i < count; i++) {
                           data.files[i].uploadName =
                               data.files[i].relativePath.replace("/","_") + data.files[i].name;
                       }
                        data.submit();
                   }
               }).on('fileuploaddone', function(e, data) {
                    var status = data.jqXHR.responseJSON.status;
                    var msg = data.jqXHR.responseJSON.msg;

                    if (status == 1) {
                        var path = data.jqXHR.responseJSON.path;
                        $("#files").fadeIn().append('<p><img style="width: 100px; height: 100px;" src="'+path+'" /></p>');
                    } else
                        $("#error").html(msg);
               }).on('fileuploadprogressall', function(e,data) {
                    var progress = parseInt(data.loaded / data.total * 100, 10);
                    $("#progress").html("Completed: " + progress + "%");
               });
            });
        </script>
	</body>
</html>