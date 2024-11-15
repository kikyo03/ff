<?php 

if(isset($_POST['fname']) && 
   isset($_POST['uname']) &&  
   isset($_POST['pass']) && 
   isset($_POST['role'])) {

    include "../db_conn.php";

    $fname = $_POST['fname'];
    $uname = $_POST['uname'];
    $pass = $_POST['pass'];
    $role = $_POST['role'];  // Capture the selected role

    $data = "fname=".$fname."&uname=".$uname."&role=".$role;
    
    if (empty($fname)) {
        $em = "Full name is required";
        header("Location: ../index.php?error=$em&$data");
        exit;
    }else if(empty($uname)){
        $em = "User name is required";
        header("Location: ../index.php?error=$em&$data");
        exit;
    }else if(empty($pass)){
        $em = "Password is required";
        header("Location: ../index.php?error=$em&$data");
        exit;
    }else if(empty($role)){
        $em = "Role is required";
        header("Location: ../index.php?error=$em&$data");
        exit;
    }else {
      // hashing the password
      $pass = password_hash($pass, PASSWORD_DEFAULT);

      if (isset($_FILES['pp']['name']) AND !empty($_FILES['pp']['name'])) {
         
         $img_name = $_FILES['pp']['name'];
         $tmp_name = $_FILES['pp']['tmp_name'];
         $error = $_FILES['pp']['error'];
         
         if($error === 0){
            $img_ex = pathinfo($img_name, PATHINFO_EXTENSION);
            $img_ex_to_lc = strtolower($img_ex);

            $allowed_exs = array('jpg', 'jpeg', 'png');
            if(in_array($img_ex_to_lc, $allowed_exs)){
               $new_img_name = uniqid($uname, true).'.'.$img_ex_to_lc;
               $img_upload_path = '../upload/'.$new_img_name;
               move_uploaded_file($tmp_name, $img_upload_path);

               // Insert into Database with role
               $sql = "INSERT INTO users(fname, username, password, pp, role) 
                       VALUES(?,?,?,?,?)";
               $stmt = $conn->prepare($sql);
               $stmt->execute([$fname, $uname, $pass, $new_img_name, $role]);

               header("Location: ../index.php?success=Your account has been created successfully");
               exit;
            }else {
               $em = "You can't upload files of this type";
               header("Location: ../index.php?error=$em&$data");
               exit;
            }
         }else {
            $em = "Unknown error occurred!";
            header("Location: ../index.php?error=$em&$data");
            exit;
         }
        
      }else {
        // Insert without profile picture
        $sql = "INSERT INTO users(fname, username, password, role) 
                VALUES(?,?,?,?)";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$fname, $uname, $pass, $role]);

        header("Location: ../index.php?success=Your account has been created successfully");
        exit;
      }
    }

}else {
    header("Location: ../index.php?error=error");
    exit;
}
