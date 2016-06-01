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
		$max = 10;
		$start = ($page - 1) * $max;
		
		$query = 'SELECT Etenduse_id, Nimetus, Aeg, Kohti_kokku-IFNULL(SUM(Broneeritud_piletid),0) as vabu_kohti FROM ejogi__etendused LEFT JOIN ejogi__broneeringud ON Etenduse_id=Etenduse_kood GROUP BY Etenduse_id ORDER BY Aeg, Nimetus ASC LIMIT ?,?';
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
	
	function model_gobooking($etenduse_id, $aeg) {
		global $l;
		
		$query = 'SELECT Etenduse_id, Nimetus, Aeg, Kohti_kokku-IFNULL(SUM(Broneeritud_piletid),0) as vabu_kohti FROM ejogi__etendused LEFT JOIN ejogi__broneeringud ON Etenduse_id=Etenduse_kood WHERE Etenduse_id = ? GROUP BY Etenduse_id LIMIT 1';
		
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
		
		if (mysqli_stmt_fetch($stmt)) {
			$etendus = array(
			    'etenduse_id' => $etenduse_id, 
			    'nimetus' => $nimetus, 
			    'aeg' => $aeg,
			    'kohad' => $kohad
			);
		}
		
		mysqli_stmt_close($stmt);
		
		return $etendus;
		
	}
	
	function model_booking($etenduse_id, $piletid) {
		// modelis toimub ainult salvestamine
		global $l;
		
		$query = 'INSERT INTO ejogi__broneeringud (Etenduse_kood, Broneeritud_piletid) VALUES (?, ?)';
		
		$stmt = mysqli_prepare($l, $query);
		//juhul kui SQL lause on vale, siis saame teate
		if ( mysqli_error($l) ) {
			echo mysqli_error($l);
			exit;
		}
		
		mysqli_stmt_bind_param($stmt, 'ii', $etenduse_id, $piletid);
		
		mysqli_stmt_execute($stmt);
		
		if ( mysqli_stmt_error($stmt) ) {
			return false;
		}
		
		$broneeringu_id = mysqli_stmt_insert_id($stmt);
		
		mysqli_stmt_close($stmt);
		
		return $broneeringu_id;
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
	
	function model_user_add($kasutajanimi, $parool, $parool2) {
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

