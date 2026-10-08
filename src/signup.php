<?php
//http://localhost:8080/combox/src/signup.php
//recibir los datos del formulario de registro
require('../config/database.php');


//recibir los datos del formulario de registro
$f_name = $_POST['fname'];
$l_name = $_POST['lname'];
$m_phone = $_POST['mphone'];
$e_mail = $_POST['email'];
$p_assword = $_POST['pswd'];
//insertar los datos en la base de datos variable 
$sql = "insert into users(  firstname, lastname, mobile_phone, email, password) 
    VALUES ('$f_name', '$l_name', '$m_phone', '$e_mail', '$p_assword')";


  //ejecutar la consulta en la base de datos
    $local_res= pg_query($localconn, $sql);
    $supa_res= pg_query($supa_conn, $sql);
//verificar si la consulta se ejecuto correctamente
  if($local_res){
    echo "<br>User has been created successfully into local database!!!";}
    else{
        echo "<br>User hasn't been created into local database!!!";}
//verificar si la consulta se ejecuto correctamente
    if($supa_res){
        echo "<br>User has been created successfully into supabase database !!!";}
        else{
            echo "<br>User hasn't been created into supabase database !!!";}


    /*
    echo "<br>first name is: " . $f_name ;
    echo "<br>last name is: " . $l_name ;
    echo "<br>mobile number is: " . $m_phone ;
    echo "<br>email is: " . $e_mail ;
    echo "<br>password is: " . $p_assword ;
    */
?>
