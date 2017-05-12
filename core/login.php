<?php

$username = isset($_POST['username'])?$_POST['username']:null;
$password = isset($_POST['password'])?$_POST['password']:null;

if (!empty($username) && !empty($password)){
	switch ($config['session']['auth']['type']) {
		case "ad":
			rrcphpbase_login_ad($username,$password);
			break;
		case "ldap":
			rrcphpbase_login_ldap($username,$password);
			break;
	}
}
if (!rrcphpbase_login_islogged()){
?>
<!DOCTYPE html><html class=''>
<head>
<meta charset='UTF-8'>
<meta name="robots" content="noindex">
<meta name="description" content="https://colorlib.com/wp/html5-and-css3-login-forms/">
<style class="cp-pen-styles">@import url(https://fonts.googleapis.com/css?family=Roboto:300);

.login-page {
  width: 360px;
  padding: 8% 0 0;
  margin: auto;
}

.form {
  position: relative;
  z-index: 1;
  background: #FFFFFF;
  max-width: 360px;
  margin: 0 auto;/* 100px;*/
  padding: 45px;
  text-align: center;
  box-shadow: 0 0 20px 0 rgba(0, 0, 0, 0.2), 0 5px 5px 0 rgba(0, 0, 0, 0.24);
}
.form input {
  font-family: "Roboto", sans-serif;
  outline: 0;
  background: #f2f2f2;
  width: 100%;
  border: 0;
  margin: 0 0 15px;
  padding: 15px;
  box-sizing: border-box;
  font-size: 14px;
}
.form button {
  font-family: "Roboto", sans-serif;
  text-transform: uppercase;
  outline: 0;
  background: #4CAF50;
  width: 100%;
  border: 0;
  padding: 15px;
  color: #FFFFFF;
  font-size: 14px;
  -webkit-transition: all 0.3 ease;
  transition: all 0.3 ease;
  cursor: pointer;
}
.form button:hover,.form button:active,.form button:focus {
  background: #43A047;
}
.form .message {
  margin: 15px 0 0;
  color: #b3b3b3;
  font-size: 12px;
}
.form .message a {
  color: #4CAF50;
  text-decoration: none;
}
.container {
  position: relative;
  z-index: 1;
  max-width: 300px;
  margin: 0 auto;
}
.container:before, .container:after {
  content: "";
  display: block;
  clear: both;
}
.container .info {
  margin: 50px auto;
  text-align: center;
}
.container .info h1 {
  margin: 0 0 15px;
  padding: 0;
  font-size: 36px;
  font-weight: 300;
  color: #1a1a1a;
}
.container .info span {
  color: #4d4d4d;
  font-size: 12px;
}
.container .info span a {
  color: #000000;
  text-decoration: none;
}
.container .info span .fa {
  color: #EF3B3A;
}
body {
	 background-image: url("<?php echo ((is_file('site/images/login.jpg'))?'site/images/login.jpg':RRCPHPBASE_LOGIN_IMAGE); ?>");
    background-repeat: no-repeat;
    background-attachment: fixed;
    background-position: center; 
  background: #fffff; /* fallback for old browsers */
  // background: -webkit-linear-gradient(right, #76b852, #8DC26F);
  // background: -moz-linear-gradient(right, #76b852, #8DC26F);
  // background: -o-linear-gradient(right, #76b852, #8DC26F);
  // background: linear-gradient(to left, #76b852, #8DC26F);
  font-family: "Roboto", sans-serif;
  -webkit-font-smoothing: antialiased;
  -moz-osx-font-smoothing: grayscale;      
}</style>
</head>
<body>
<div class="login-page">
  <div class="form">
    <form class="login-form" method="POST" action="index.php">
	  <h1><?php echo $config['login']['title']; ?></h1>
	  <p><?php echo $config['login']['subtitle']; ?></p>
      <input type="text" name="username" placeholder="usuario"/>
      <input type="password" name="password" placeholder="contraseña"/>
      <button>login</button>
      <p class="message">Inicio de sesión integrado con <strong><?php echo rrcphpbase_login_authText(); ?></strong></p>
	  <p class="message"><?php echo RRCPHPBASE_COPYRIGHT; ?></p>
    </form>
  </div>
</div>
</body>
</html>
<?php
}
?>