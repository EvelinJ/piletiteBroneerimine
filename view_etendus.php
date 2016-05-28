<!doctype html>
<html>
    <head>
        <meta charset="utf-8"/>
        <title>Lavastus</title>
		
        <style>
            
			
        </style>
		
        
    </head>
	
    <body>
	    
		<?php foreach (message_list() as $message):?>
		    <p style="border: 1px solid blue; background: #EEE;">
			    <?= $message; ?>
			</p>
		<?php endforeach; ?>
		
		<div style="float: right">
		    <form method="post" action="<?= $_SERVER['PHP_SELF']; ?>">
			    <input type="hidden" name="action" value="logout">
				<input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token']; ?>">
				<button type="submit">Logi välja</button>
			</form>
		</div>
		
        <h1>Lavastuse andmed</h1>

        <!-- Sisestatud andmete tabel -->
        <table id="kirjed" border="1">
            <thead> <!-- tabeli päis -->
                <tr> <!-- üks rida, kaks veergu -->
                    <th>Etenduse nimetus</th>
					<th>Etenduse toimumise aeg</th>
					<th>Vabad kohad</th>
                </tr>
            </thead>
			
            <tbody>
			
			<?php if ( !empty($etendus) ) :?>
			    <!--<h3><?php echo $_POST['etenduse_id']; ?></h3>-->
				<h3><?= $etendus['nimetus']; ?></h3>
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
			<?php else: ?>
			<h3><?php echo 'tyhi'; ?></h3>
			<?php endif; ?>
            <!-- salvestab massiivist väärtused tabelisse, indeksit on vaja, et teaksime millist rida kustutada  -->
            
            
			</tbody>
        </table>
		
		<div id="lisa-broneering-vaade">
			
            <form id="lisa-broneering" method="post" action="<?= $_SERVER['PHP_SELF']; ?>"> <!-- php jaoks on vajalik method ja action viitab sellele failile, mille URLis avame 9Rakendus.php-->
                
				<input type="hidden" name="action" value="booking">
				<input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token']; ?>">
				<input type="hidden" name="etenduse_id" value="<?= $etendus['etenduse_id']; ?>">
				<input type="hidden" name="kasutaja_id" value="<?= $_POST['kasutaja_id']; ?>">
				
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
                    <button type="submit">Broneeri piletid</button>
				</p>
				
            </form>
			
        </div>

        <script src="rakendus.js"></script>
    </body>

</html>