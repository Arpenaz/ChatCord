<?php
    session_start(); //starting session
    include_once "config.php";
    $fname = mysqli_real_escape_string($conn, $_POST['fname']);
    $lname = mysqli_real_escape_string($conn, $_POST['lname']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);

    if(!empty($fname) && !empty($lname) && !empty($email) && !empty($password)){
        // check whether user email is valid or not
        if(filter_var($email, FILTER_VALIDATE_EMAIL)) //if email is valid
        {
            // lets check whether email already exists in the database or not
            $sql = mysqli_query($conn, "SELECT * FROM users WHERE email = '{$email}'");
            if(mysqli_num_rows($sql) > 0){
                echo "$email - This email already exist!";
            }else{
                // let's check user upload file or not
                if(isset($_FILES['image'])){
                    $img_name = $_FILES['image']['name']; //getting user uploaded img name
                    $img_type = $_FILES['image']['type']; //getting user uploaded user type
                    $tmp_name = $_FILES['image']['tmp_name']; //this is temporaray name that is used to save/move file in our folder
                    
                    //let's explode image and get the last extendion like jpg png
                    $img_explode = explode('.',$img_name);
                    $img_ext = end($img_explode); //here we get the extension of an user uploaded img file
    
                    $extensions = ["jpeg", "png", "jpg"]; //these are some valid img ext and we'ev store them in array
                    if(in_array($img_ext, $extensions) === true){ //if user uploaded img ext is matched with any array extensions
                        $types = ["image/jpeg", "image/jpg", "image/png"];
                        if(in_array($img_type, $types) === true){
                            $time = time(); //this will return us currnt time , we need this time becoz when you are uploading user img to in our folder we rename user file with current time , so all the images file will have a unique name
                            $new_img_name = $time.$img_name;
                            //if user uploaded to our folder successfully
                            if(move_uploaded_file($tmp_name,"images/".$new_img_name) //lets move the user uploaded img to our particular folder
                            {
                                $ran_id = rand(time(), 100000000);//creating random id for user 
                                $status = "Active now"; // once user signed up ,then his/her status will be active now
                                $encrypt_pass = md5($password);
                                //Inserting all user data inside table
                                $insert_query = mysqli_query($conn, "INSERT INTO users (unique_id, fname, lname, email, password, img, status)
                                VALUES ({$ran_id}, '{$fname}','{$lname}', '{$email}', '{$encrypt_pass}', '{$new_img_name}', '{$status}')");
                                if($insert_query) //if the data is inserted
                                {
                                    $select_sql2 = mysqli_query($conn, "SELECT * FROM users WHERE email = '{$email}'");
                                    if(mysqli_num_rows($select_sql2) > 0){
                                        $result = mysqli_fetch_assoc($select_sql2);
                                        //using this session we used unique_id in other php file
                                        $_SESSION['unique_id'] = $result['unique_id'];
                                        echo "success";
                                    }else{
                                        echo "This email address not Exist!";
                                    }
                                }else{
                                    echo "Something went wrong. Please try again!";
                                }
                            }
                        }else{
                            echo "Please upload an image file - jpeg, png, jpg";
                        }
                    }else{
                        echo "Please upload an image file - jpeg, png, jpg";
                    }
                }
            }
        }else{
            echo "$email is not a valid email!";
        }
    }else{
        echo "All input fields are required!";
    }
?>