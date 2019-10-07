<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta http-equiv="x-ua-compatible" content="ie=edge">
    <title>Upload images with AJAX</title>
    <link rel="stylesheet" type="text/css" href="css/bootstrap.min.css" />
   
    <script type="text/javascript" src="js/jquery-3.4.1.min.js"></script>
    <script type="text/javascript" src="js/bootstrap.min.js"></script>
    <script type="text/javascript" src="js/bootbox.min.js"></script> 
    <script type="text/javascript" src="js/jquery-ui.min.js"></script>
   
    <style>
		body{background-color:#C8BFE7}
        table.cvf_uploaded_files {list-style-type: none; margin: 20px 0 0 0; padding: 0;}
        table.cvf_uploaded_files td {background-color: #fff; border: 1px solid #ccc; border-radius: 5px; float: left; margin: 20px 20px 0 0; padding: 2px; width: 150px; height: 150px; line-height: 150px; position: relative;}
        table.cvf_uploaded_files td img.img-thumb {width: 150px; height: 150px;}
        table.cvf_uploaded_files .ui-selected {background: red;}
        table.cvf_uploaded_files .highlight {border: 1px dashed #000; width: 150px; background-color: #ccc; border-radius: 5px;}
        .cvf_delete_image{position: absolute; top:-30px!important; left: -10px;}
        .bg-success {padding: 7px;}
    </style>
</head>
<body>
    <div class ="container">
		<div class="text-center">
			<img src="img/handyperson-icon.png" width="120px">
			<h3>Advance photo upload with ajax and sorting</h3>
		</div><br/>
		<div class="row">
			<div class="col-md-6 offset-md-3">
				<form class="card card-primary"  method="post" action="upload.php" enctype="multipart/form-data">
					
					<div class="card-footer">
						<h3 class="text-center">Upload Photos <span id="cvf_uploading_files"></span></h3>
						<div class="table-responsive">
							<table class="table">
								<tr class = "cvf_uploaded_files">
									<td>
										<label for="ImagesUploader" style="cursor:pointer;">
											<img src='img/camera-icon2.png' height='70px'>
										</label>
										<input type = "file" id="ImagesUploader" name = "upload" multiple = "multiple" class = "form-control user_picked_files d-none" /> 
										<div class = "form-group cvf_order d-none">                           
											<input type ="hidden" class ="form-control cvf_hidden_field" value = "" /> 
										</div>
									</td>
								
								</tr>
							</table>
						</div><br>
						<input type = "submit" class = "cvf_upload_btn btn btn-success float-right" value = "Upload Image(s) to Server" />	
					</div>
				
				</form>
			</div>
		</div><br><br>
	</div>
	    	
	<script type="text/javascript">
        jQuery(document).ready(function() {        
           
            var storedFiles = [];      
            //$('.cvf_order').hide();
           
            // Apply sort function 
            function cvf_reload_order() {
                var order = $('.cvf_uploaded_files').sortable('toArray', {attribute: 'item'});
                $('.cvf_hidden_field').val(order);
            }
           
            function cvf_add_order() {
                $('.cvf_uploaded_files td').each(function(n) {
                    $(this).attr('item', n);
                });
                console.log('test');
            }
           
           
            $(function() {
                $('.cvf_uploaded_files').sortable({
                    cursor: 'move',
                    placeholder: 'highlight',
                    start: function (event, ui) {
                        ui.item.toggleClass('highlight');
                    },
                    stop: function (event, ui) {
                        ui.item.toggleClass('highlight');
                    },
                    update: function () {
                        //cvf_reload_order();
                    },
                    create:function(){
                        var list = this;
                        resize = function(){
                            $(list).css('height','auto');
                            $(list).height($(list).height());
                        };
                        $(list).height($(list).height());
                        $(list).find('img').load(resize).error(resize);
                    }
                });
                $('.cvf_uploaded_files').disableSelection();
            });
                   
            $('body').on('change', '.user_picked_files', function() {
               
                var files = this.files;
                var i = 0;
                           
                for (i = 0; i < files.length; i++) {
                    var readImg = new FileReader();
                    var file = files[i];
                   
                    if (file.type.match('image.*')){
                        storedFiles.push(file);
                        readImg.onload = (function(file) {
                            return function(e) {
                                $('.cvf_uploaded_files').append(
                                "<td file = '" + file.name + "'>" + 
                                    "<img class = 'img-thumb' style='border:1px solid black;' height='70px' src = '" + e.target.result + "' />" +
									"<a href = '#' class = 'cvf_delete_image badge badge-danger' style='position:relative' title = 'Cancel'>X</a>" +
                                    
                                "</td>"
                                );     
                            };
                        })(file);
                        readImg.readAsDataURL(file);
                       
                    } else {
                        alert('the file '+ file.name + ' is not an image<br/>');
                    }
                   
                    if(files.length === (i+1)){
                        setTimeout(function(){
                            cvf_add_order();
                        }, 1000);
                    }
                }
            });
           
            // Delete Image from Queue
            $('body').on('click','a.cvf_delete_image',function(e){
                e.preventDefault();
                $(this).parent().remove('');       
               
                var file = $(this).parent().attr('file');
                for(var i = 0; i < storedFiles.length; i++) {
                    if(storedFiles[i].name == file) {
                        storedFiles.splice(i, 1);
                        break;
                    }
                }
               
                //cvf_reload_order();
               
            });
                   
            // AJAX Upload
            $('body').on('click', '.cvf_upload_btn', function(e){
               
                e.preventDefault();
                cvf_reload_order();
               
                $("#cvf_uploading_files").append('<img src = "img/loading.gif" height="30px" class = "loader" />');
                var data = new FormData();
               
                var items_array = $('.cvf_hidden_field').val();
                var items = items_array.split(',');

                for (var i in items){
                    var item_number = items[i];
                    data.append('files' + i, storedFiles[item_number]);
                }
                   
                $.ajax({
                    url: 'upload.php',
                    type: 'POST',
                    contentType: false,
                    data: data,
                    processData: false,
                    cache: false,
                    success: function(response, textStatus, jqXHR) {
                        $(".cvf_uploaded_files td:gt(0)").remove()
						$("#cvf_uploading_files").html('');
						$("#ImagesUploader").val('');
						storedFiles=[];
						$('.cvf_hidden_field').val('');
                        bootbox.alert('<br /><p class = "text-success">File(s) uploaded successfully.</p>');
                    }
                });
               
            });        

        });
    </script>
</body>
</html>