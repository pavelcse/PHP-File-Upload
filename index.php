<?php 
    include 'inc/header.php'; 
    include 'lib/config.php'; 
    include 'lib/Database.php'; 
?>
<?php 
    $db = new Database();
?>

    <div class="myform">
    	<?php
            if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            	$permission = array('jpg', 'jpeg', 'png', 'gif', );
            	$file_name = $_FILES['image']['name'];
            	$file_size = $_FILES['image']['size'];
            	$file_temp = $_FILES['image']['tmp_name'];


                $div = explode('.', $file_name);
                $file_extn = strtolower(end($div));
                $unique_img = substr(md5(time()), 0, 10).'.'.$file_extn;
                $upload_img = "uploads/".$unique_img;

                if (empty($file_name)) {
                    echo "<span class='error'> Please Select an Image...</span>";
                }elseif (in_array($file_extn, $permission) === false) {
                   echo "<span class='error'> Please select an Image which file type only:- ".implode(',', $permission)."</span>";
                }elseif ($file_size > 1048567) {
                    echo "<span class='error'> Please select an Image which is less than 1 MB...</span>";
                }else{
                    move_uploaded_file($file_temp, $upload_img);
                    $query = "INSERT INTO tbl_img(image) VALUES('$upload_img')";
                    $insert = $db->insert($query);
                    if ($insert) {
                        echo "<span class='success'>Image Upload successfully...</span>";
                    }else{
                        echo "<span class='error'>Image Upload Failed...</span>";
                    }
                }

            }
    	?>
    	<form action="" method="post" enctype="multipart/form-data">
    		<table>
    			<tr>
    				<td>Select Image</td>
    				<td><input type="file" name="image"></td>
    			</tr>
    			<tr>
    				<td></td>
    				<td><input type="submit" name="submit" value="Submit"></td>
    			</tr>
    		</table>
    	</form>                  
         <table width="100%" border="1px solid">
            <tr>
                <th width="30%">SL</th>
                <th width="40%">Image</th>
                <th width="30%">Action</th>
            </tr>

            <?php 
                if (isset($_GET['delete'])) {
                    $id = $_GET['delete'];
                //Delete Query from folder

                    $getQuery = "SELECT * FROM tbl_img WHERE id='$id'";
                    $getImagePath = $db->select($getQuery);
                    if ($getImagePath) {
                        while ($imgPath = $getImagePath->fetch_assoc()) {
                            $deleteFile = $imgPath['image'];
                            unlink($deleteFile);
                        }
                    }

                //Delete Query from Database

                    $sql = "DELETE FROM tbl_img WHERE id='$id'";
                    $deleteImg = $db->delete($sql);
                    if ($deleteImg) {
                        echo "<span class='success'>Image Deleted successfully...</span>";
                    }else{
                        echo "<span class='error'>Image Not Deleted..!!</span>";
                    }
                }
            ?>

            <?php 
            //View Query from Database

            $sql = "SELECT * FROM tbl_img ORDER BY id DESC";
            $getImage = $db->select($sql);
            if ($getImage) {
                $sl =0;
              while ($result = $getImage->fetch_assoc()) {
                $sl++;
        ?>
            <tr>
                <td><?php echo $sl;?></td>
                <td>
                    <img src="<?php echo $result['image']; ?>" alt="" height="50px" width="70px /">
                </td>
                <td> <a href="?delete=<?php echo $result['id']; ?>">Delete</a></td>
            </tr>
        
        <?php
              }
            }
        ?>
        </table>
    </div>

<?php include 'inc/footer.php' ?>