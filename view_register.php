<!doctype html>
<html>
    <head>
	
        <meta charset="utf-8"/>
        <title>Registreeri</title>
		<link rel="stylesheet" type="text/css" href="css.css">
		
    </head>
	
    <body>
	
        <?php foreach (message_list() as $message):?>
		    <p class="message">
			    <?= $message; ?>
			</p>
		<?php endforeach; ?>
		
		<h1 class="pealkiri">Registreeri kasutajaks</h1>
		<div class="form-user">
		    <form method="post" action="<?= $_SERVER['PHP_SELF']; ?>">
		    
		        <input type="hidden" name="action" value="register">
			    <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token']; ?>">
		    
			    <table class="table-user">
			        <tr>
				        <td>Kasutajanimi</td>
					    <td>
					        <input type="text" name="kasutajanimi" placeholder="Sisesta kasutajanimi" required>
					    </td>
				    </tr>
				
				    <tr>
				        <td>Parool</td>
					    <td>
					        <input type="password" name="parool" placeholder="Sisesta parool" required>
					    </td>
				    </tr>
					<tr>
				        <td>Korda parooli</td>
					    <td>
					        <input type="password" name="parool2" placeholder="Korda valitud parooli" required>
					    </td>
				    </tr>
			    </table>
			
			    <p>
			        <button type="submit" class="button">Registreeri konto</button>
				    või
			        <a href="<?= $_SERVER['PHP_SELF']; ?>?view=login">
			            logi sisse
			        </a>
			    </p>
		
		    </form>
        </div>
	</body>
</html>