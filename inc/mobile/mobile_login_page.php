
<style>
    h1{
        font-size: 75px;
    }   
    
    a{
        text-decoration: none;
    }

    input{
         font-size: 50px;
         width: 100%;
         height: 100px;
    }

    .login_form {
        padding: 50px;
        margin: 20px;
    }

    #login_button{
        border:none;
        margin-top:20px;
        background: rgb(119, 193, 88);
        color:white;
        border: 1px solid rgb(76, 174, 76);
        border-radius: 7px;
    }

</style>  

<div class="login_form">

<form method="post">
    <p style="font-size: 80px;">Username</p>
    <input name="myemail" type="text" id="myusername" value="djsalter93@hotmail.com">

    <p style="font-size: 80px;">Password</p>
    <input name="mypassword" type="password" id="mypassword" value="thegreatiam">

    <input type="submit" id="login_button" value="Login">
</form>

</div>


</body>
</html>