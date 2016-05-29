<?php 
    $host = 'localhost';
    $user = 'test';
    $pass = 't3st3r123';
    $db = 'test';

    $l = mysqli_connect($host, $user, $pass, $db);
    mysqli_query($l, 'SET CHARACTER SET UTF8') or
            die('Error, ei saa andmebaasi charsetti seatud');
    
	//laeme kõik read korraga alla, sorteerime nimetuse järgi kasvavas järjekorras
    function model_load($page) {
	    global $l;
		$max = 5;
		$start = ($page - 1) * $max;
		
		$query = 'SELECT Etenduse_id, Nimetus, Aeg, Kohti_kokku FROM ejogi__etendused ORDER BY Aeg ASC LIMIT ?,?';
		//$query = 'SELECT Etenduse_id, Nimetus, Aeg, Kohti_kokku-SUM(Broneeritud_piletid) FROM ejogi__etendused, ejogi__broneeringud WHERE Etenduse_id=Etenduse_kood ORDER BY Aeg ASC LIMIT ?,?';
		$stmt = mysqli_prepare($l, $query);
		//juhul kui SQL lause on vale, siis saame teate
		if ( mysqli_error($l) ) {
			echo mysqli_error($l);
			exit;
		}
		
		mysqli_stmt_bind_param($stmt, 'ii', $start, $max);
		
		mysqli_stmt_execute($stmt);
		
		//muutujad peavad olema samas järjekorras, mis select lauses
		mysqli_stmt_bind_result($stmt, $etenduse_id, $nimetus, $aeg, $kohad);
		
		$rows = array();
		//fetch täidab ära need muutujad, mis on bindi juures määratud $query lauses olevate väärtustega
		while (mysqli_stmt_fetch($stmt)) {
			$rows[] = array(
			    'etenduse_id' => $etenduse_id, 
				'nimetus' => $nimetus, 
				'aeg' => $aeg,
				'kohad' => $kohad
			);
		}
		
		mysqli_stmt_close($stmt);
		
		return $rows;
	}
	
	function model_add($nimetus, $aeg, $kohad) {
		global $l;
		
		$query = 'INSERT INTO ejogi__etendused (Nimetus, Aeg, Kohti_kokku) VALUES (?, ?, ?)';
		
		$stmt = mysqli_prepare($l, $query);
		
		//juhul kui SQL lause on vale, siis saame teate
		if ( mysqli_error($l) ) {
			echo mysqli_error($l);
			exit;
		}
		
		//si tähendab string ehk nimetus ja int ehk kogus
		mysqli_stmt_bind_param($stmt, 'ssi', $nimetus, $aeg, $kohad);
		
		//küsimärgid asendatakse nimetuse ja koguse väärtusega
		mysqli_stmt_execute($stmt);
		
		$etenduse_id = mysqli_stmt_insert_id($stmt);
		
		mysqli_stmt_close($stmt);
		
		return $etenduse_id;
	}
	
	function model_gobooking($etenduse_id) {
		global $l;
		
		$query = 'SELECT Etenduse_id, Nimetus, Aeg, Kohti_kokku FROM ejogi__etendused WHERE Etenduse_id = ? LIMIT 1';
		
		$stmt = mysqli_prepare($l, $query);
		
		//juhul kui SQL lause on vale, siis saame teate
		if ( mysqli_error($l) ) {
			echo mysqli_error($l);
			exit;
		}
		
		//i tähendab küsimärgi muutuja tüüpi
		mysqli_stmt_bind_param($stmt, 'i', $etenduse_id);
		
		mysqli_stmt_execute($stmt);
		
		
		//muutujad peavad olema samas järjekorras, mis select lauses
		mysqli_stmt_bind_result($stmt, $etenduse_id, $nimetus, $aeg, $kohad);
		
		$etendus = array();
		//fetch täidab ära need muutujad, mis on bindi juures määratud $query lauses olevate väärtustega
		
		if (mysqli_stmt_fetch($stmt)) {
			$etendus[] = array(
			    'etenduse_id' => $etenduse_id, 
			    'nimetus' => $nimetus, 
			    'aeg' => $aeg,
			    'kohad' => $kohad
			);
		}
		
		mysqli_stmt_close($stmt);
		
		return $etendus;
		
	}
	
	function model_booking($broneeringu_id, $etenduse_id, $kasutaja_id, $piletid) {
		// modelis toimub ainult salvestamine
		global $l;
		
		//kui mitut välja uuendame, siis väljad käivad komadega (...SET Kogus=?, Nimetus=?)
		$query = 'INSERT INTO ejogi__broneeringud (Broneeringu_id, Etenduse_kood, Kasutaja_kood, Broneeritud_piletid) VALUES (?, ?, ?, ?)';
		
		$stmt = mysqli_prepare($l, $query);
		//juhul kui SQL lause on vale, siis saame teate
		if ( mysqli_error($l) ) {
			echo mysqli_error($l);
			exit;
		}
		
		//i tähendab küsimärgi muutuja tüüpi
		mysqli_stmt_bind_param($stmt, 'iiii', $broneeringu_id, $etenduse_id, $kasutaja_id, $piletid);
		
		mysqli_stmt_execute($stmt);
		
		if ( mysqli_stmt_error($stmt) ) {
			return false;
		}
		
		mysqli_stmt_close($stmt);
		
		return true;
	}
	
	function model_delete($etenduse_id) {
		global $l;
		
		$query = 'DELETE FROM ejogi__etendused WHERE Etenduse_id = ? LIMIT 1';
		
		$stmt = mysqli_prepare($l, $query);
		
		//juhul kui SQL lause on vale, siis saame teate
		if ( mysqli_error($l) ) {
			echo mysqli_error($l);
			exit;
		}
		
		//i tähendab küsimärgi muutuja tüüpi
		mysqli_stmt_bind_param($stmt, 'i', $etenduse_id);
		
		mysqli_stmt_execute($stmt);
		
		//mitut rida mõjutati ehk kustutati
		$deleted = mysqli_stmt_affected_rows($stmt);
		
		mysqli_stmt_close($stmt);
		
		return $deleted;
	}
	
	function model_user_add($kasutajanimi, $parool) {
		global $l;
		
		$hash = password_hash($parool, PASSWORD_DEFAULT);
		
		$query = 'INSERT INTO ejogi__kasutajad (Kasutajanimi, Parool) VALUES (?,?)';
		
		$stmt = mysqli_prepare($l, $query);
		
		//juhul kui SQL lause on vale, siis saame teate
		if ( mysqli_error($l) ) {
			echo mysqli_error($l);
			exit;
		}
		
		//seome query'ga need kaks muutujat ss tähendab muutujate tüüpi ehk string string
		mysqli_stmt_bind_param($stmt, 'ss', $kasutajanimi, $hash);
		
		mysqli_stmt_execute($stmt);
		
		$kasutaja_id = mysqli_stmt_insert_id($stmt);
		
		mysqli_stmt_close($stmt);
		
		return $kasutaja_id;
	}
	
	function model_user_get($kasutajanimi, $parool) {
		global $l;
		
		$query = 'SELECT Id, Parool FROM ejogi__kasutajad WHERE Kasutajanimi=? LIMIT 1';
		
		$stmt = mysqli_prepare($l, $query);
		
		//juhul kui SQL lause on vale, siis saame teate
		if ( mysqli_error($l) ) {
			echo mysqli_error($l);
			exit;
		}
		
		mysqli_stmt_bind_param($stmt, 's', $kasutajanimi);
		
		mysqli_stmt_execute($stmt);
		
		mysqli_stmt_bind_result($stmt, $kasutaja_id, $hash);
		
		mysqli_stmt_fetch($stmt);
		
		mysqli_stmt_close($stmt);
		
		
		if ( password_verify($parool, $hash) ) {
			return $kasutaja_id;
		} else {
			return false;
		}
	}

