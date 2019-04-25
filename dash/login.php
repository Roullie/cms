<?php

	// include common files
	include '../lib/common.php';
	// redirect user to an appropriate page
	if( !empty( $_SESSION[ABBR.'LOGGED'] ) ){
		redirect("index.php");
	}
	// variable used for validation and output
	$err 		= "";
	$username 		= "";
	$password 	= "";
	// checks if form is submitted
	if( !empty( $_POST ) ){
		
		// db class
		$db = new DB();
		// encryption class
		$enc=new Encryption();
		
		// gets a user with the same username inputted by the user
		$user 		= $db->SelectUsers( $_POST , true);
		$username 	= $_POST['username'];
		$password 	= $_POST['password'];
		
		// credentials exists
		if( $user ){
			unset( $user['password'] );
			$_SESSION[ABBR.'LOGGED'] = $user;
			// set cookie for them not to logged in again until cookie expires 1 year
			setcookie(
				ABBR.'LOGGED',
				$enc->encode($user['id']),
				(time() + (365*24*60*60)),
				"/"
			);
			if( $_SESSION[ABBR.'LOGGED'] == 1 ){
				redirect("/admin/index.php");
			}
			redirect("index.php");
		}
		// login form submitted but failed to find the variables in the database
		$err = "We cannot find your username.";
	}
	
?>
<?php include 'parts/header.php';?>

    	<div class="container">
			
            <div class="panel panel-info" style="margin:5% auto 0 auto;width:300px;">
            	
            	<div class="panel-heading">
                	Login
                </div>
                <div class="panel-body">
                  <?php if($err){?>
                    <div class="alert alert-warning"><?=$err?></div>
                  <?php } ?>
                  <form method="post">
                	<input type="text" name="username" class="form-control" placeholder="Username" value="<?=$username?>"/><br />
                    <input type="password" name="password" class="form-control" placeholder="Password" value="<?=$password?>" /><br />
                    <button class="btn btn-primary" style="width:100%">Login</button>
                  </form>
                </div>
            </div>
            
        </div>
        
<?php include 'parts/footer.php';?>