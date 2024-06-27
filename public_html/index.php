<?php
    require_once __DIR__."/backend/auth/login.php";
?>

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Gestor Equipamentos</title><link rel="stylesheet" href="/frontend/css/index.css"><script src=""></script>
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin="">
        <link href="https://fonts.googleapis.com/css2?family=Roboto+Slab:wght@100..900&amp;display=swap" rel="stylesheet">  
        <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200">
    </head>
    <body>
        <div class="form-positioner">
            <form class="sign-in-form" action="<?=htmlspecialchars($_SERVER["PHP_SELF"])?>" method="post">
                <h1 class="form-title">
                    Please sign in
                </h1>
                <div class="form-input-container">
                    <label for="inputEmail" class="">
                    </label>
                        <input type="text" name="email" id="inputEmail" class="form-input" placeholder="Email address" required="true" maxlength="50 " autofocus="true ">
                        <label for="inputPassword" class="">
                    </label>
                        <input type="password" name="password" id="inputPassword" class="form-input" placeholder="Password" required="true" maxlength="" autofocus="">
                </div>
                <div class="button-input-container"> 
                    <button href="" class="login-button" type="submit" name="submit" value="submit">
                        Sign in
                    </button>
                </div> 
                <?php
                    if(!empty($error_message))
                    echo("
                        <div class=\"error-message\">
                            <p>
                                $error_message
                            </p>
                        </div>
                    ");
                ?>
           </form>
        </div>
    </body>
</html>