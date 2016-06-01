<!doctype html>
<html>
    <head>
	
        <meta charset="utf-8"/>
        <title>Lavastus</title>
		<link rel="stylesheet" type="text/css" href="css.css">
        
    </head>
	
    <body>
	    
		<?php foreach (message_list() as $message):?>
		    <p class="message">
			    <?= $message; ?>
			</p>
		<?php endforeach; ?>
		
		<div style="float: right">
		    <form method="post" action="<?= $_SERVER['PHP_SELF']; ?>">
			    <input type="hidden" name="action" value="logout">
				<input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token']; ?>">
				
				<button type="submit" class="button">Logi välja</button>
			</form>
		</div>
		
        <h1>Lavastuse andmed</h1>

        <!-- Sisestatud andmete tabel -->
        <table class="table" id="kirjed">
            <thead> <!-- tabeli päis -->
                <tr> <!-- üks rida, kolm veergu -->
                    <th>Etenduse nimetus</th>
					<th>Etenduse toimumise aeg</th>
					<th>Vabad kohad</th>
                </tr>
            </thead>
			
            <tbody>
			
			<?php $etendus = model_gobooking($etenduse_id, $aeg); ?>
			
			<?php if ( !empty($etendus) ) :?>
				<h3><?php echo $etendus['nimetus']; ?></h3>
				<tr>
                    <td>
                        <!-- vältimaks pahatahtlikku XSS sisu, kus kasutaja sisestab õige info asemel <script> tag'i, peame tekstiväljundis asendama kõik HTML erisümbolid  --> 
                        <?= htmlspecialchars($etendus['nimetus']); ?>
                        </td>
						<td>
                            <?= $etendus['aeg']; ?>
                        </td>
						<td>
                            <?= $etendus['kohad']; ?>
                        </td>
                </tr>
			<?php endif; ?>
            
			</tbody>
        </table>
		
		<div id="lisa-broneering-vaade">
			
			<!-- php jaoks on vajalik method ja action viitab sellele failile, mille URLis avame rakendus.php-->
            <form id="lisa-broneering" method="post" action="<?= $_SERVER['PHP_SELF']; ?>">
                
				<input type="hidden" name="action" value="booking">
				<input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token']; ?>">
				<input type="hidden" name="etenduse_id" value="<?= $etendus['etenduse_id']; ?>">
				
				<h2>Broneeri pilet</h2>
				
				<!-- broneeritavate piletite siestamine -->
				<table>
                    <tr>
                        <td>Piletite arv</td>
                        <td>
                            <input type="number" id="piletid" name="piletid">
                        </td>
                    </tr>
                </table>
				
				<p>
                    <button type="submit" class="button">Broneeri piletid</button>
				</p>
				
            </form>
			
        </div>

        <script src="rakendus.js"></script>
    </body>

</html>