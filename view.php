<!doctype html>
<html>
    <head>
        <meta charset="utf-8"/>
        <title>Piletite broneerimine</title>
		
        <style>
            #lisa-vorm-vaade {
                display: none;
                border: 1px solid red;
                padding: 10px;
            }
            
			
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
		
        <h1>Piletite broneerimine</h1>
		
        <p>
		    <button type="button" id="kuva-lisa-vorm">Ava etenduste sisestamise vorm</button>
		</p>

        <div id="lisa-vorm-vaade">
			
            <form id="lisa-vorm" method="post" action="<?= $_SERVER['PHP_SELF']; ?>"> <!-- php jaoks on vajalik method ja action viitab sellele failile, mille URLis avame 9Rakendus.php-->
                
				<input type="hidden" name="action" value="add">
				<input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token']; ?>">
				
				<p>
				    <button type="button" id="peida-lisa-vorm">Peida etenduste sisestamise vorm</button>
				</p>
				
				<h2>Lisa lavastus mängukavasse</h2>
				
				<!-- Etenduse andmete sisestamise lahtrid -->
				<table>
                    <tr>
                        <td>Etenduse nimetus</td>
                        <td>
                            <input type="text" id="nimetus" name="nimetus"> <!-- php jaoks on vajalik name, kui seda ei ole, siis neid elemente ei saadeta serverisse -->
                        </td>
                    </tr>
					<tr>
                        <td>Etenduse toimumise aeg</td>
                        <td>
                            <input type="datetime-local" id="aeg" name="aeg">
                        </td>
                    </tr>
                    <tr>
                        <td>Kohtade arv</td>
                        <td>
                            <input type="number" id="kohad" name="kohad">
                        </td>
                    </tr>
                </table>
				
				<p>
                    <button type="submit" id="lisa-nupp">Lisa etendus</button>
				</p>
				
            </form>
			
        </div>
		
		<!-- Sisestatud andmete tabel -->
        <table id="kirjed" border="1">
            <thead> <!-- tabeli päis -->
                <tr> <!-- üks rida, kaks veergu -->
                    <th>Etenduse nimetus</th>
					<th>Etenduse toimumise aeg</th>
					<th>Vabad kohad</th>
                    <th>Broneerimine</th>
                    <th>Kustuta etendus</th>
                </tr>
            </thead>
			
            <tbody>
			
            <!-- salvestab massiivist väärtused tabelisse, indeksit on vaja, et teaksime millist rida kustutada  -->
            <?php 
			// koolon tsükli lõpus tähendab, et tsükkel koosneb HTML osast
                foreach ( model_load($page) as $rida ): ?>
                    <tr>
                        <td>
                            <!-- vältimaks pahatahtlikku XSS sisu, kus kasutaja sisestab õige info asemel <script> tag'i, peame tekstiväljundis asendama kõik HTML erisümbolid  --> 
                            <?= htmlspecialchars($rida['nimetus']); ?>
                        </td>
						<td>
                            <?= $rida['aeg']; ?>
                        </td>
						<td>
                            <?= $rida['kohad']; ?>
                        </td>
						<td>
						    <form method="post" action="<?= $_SERVER['PHP_SELF']; ?>">
							
							    <input type="hidden" name="action" value="gobooking">
								<input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token']; ?>">
                                <input type="hidden" name="etenduse_id" value="<?= $rida['etenduse_id']; ?>">
								
                                
								<button type="submit">Pileti broneerimine</button>
                                
							</form>
                        </td>
                        <td>
					
                            <form method="post" action="<?= $_SERVER['PHP_SELF']; ?>">
							    <input type="hidden" name="action" value="delete">
								<input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token']; ?>">
                                <input type="hidden" name="etenduse_id" value="<?= $rida['etenduse_id']; ?>">
								
                                <button type="submit">Kustuta etendus</button>
                            </form>
                    
                        </td>
                    </tr>
            <?php endforeach; ?>
            
			</tbody>
        </table>
		
		<p>
		    <a href="<?= $_SERVER['PHP_SELF']; ?>?page=<?= $page - 1; ?>">
			    Eelmine lehekülg
			</a>
			|
			<a href="<?= $_SERVER['PHP_SELF']; ?>?page=<?= $page + 1; ?>">
			    Järgmine lehekülg
			</a>
			
		</p>

        <script src="rakendus.js"></script>
    </body>

</html>