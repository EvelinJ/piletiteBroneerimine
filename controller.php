<?php

    function controller_add($nimetus, $aeg, $kohad) {
		
		$kuupaev = date('Y-m-d H:i:s');
		
		if ( !controller_user() ) {
			message_add('Kasutaja peab olema sisselogitud!');
			return false;
		}
		
		// kontrollime, kas sisendväärtused on oodatud kujul või mitte
		if ($nimetus == '' || $aeg == '' || $aeg < $kuupaev || $kohad <= 0) {
			message_add('Sisestatud väärtused on vigased. Kõik andmed peavad olema sisestatud ja etenduse kuupäev ei tohi olla möödunud!');
			return false;
		}
		
		if ( model_add($nimetus, $aeg, $kohad) ) {
			message_add('Mängukavva lisati uus etendus');
			// saadame info modelisse, mis reaalse salvestamise teeb
			return true;
		}
		
		message_add('Andmete lisamine ebaõnnestus!');
		return false;
	}
	
	function controller_gobooking($etenduse_id, $aeg) {
		
		$kuupaev = date('Y-m-d H:i:s');
		
		if ( !controller_user() ) {
			message_add('Kasutaja peab olema sisselogitud!');
			return false;
		}
		
		if ($etenduse_id <= 0) {
			message_add('Vigased sisendandmed');
			return false;
		}
		
		if ($aeg < $kuupaev) {
			message_add('Juba toimunud etendusele ei saa pileteid broneerida');
			return false;
		}
		
		if ( model_gobooking($etenduse_id, $aeg) ) {
			message_add('Valisid etenduse '.$etenduse_id);
			// saadame info modelisse, mis reaalse salvestamise teeb
			return true;
		}
		
		message_add('Etenduse valimine ebaõnnestus');
		return false;
		
	}
	
	function controller_booking($etenduse_id, $piletid) {
		
		if ( !controller_user() ) {
			message_add('Kasutaja peab olema sisselogitud!');
			return false;
		}
		
		$vabad_kohad = model_gobooking($etenduse_id);
		
		// kui id või kogus on nullist väiksem või 0 või pileteid broneeritakse rohkem kui vabu kohti, siis see meile ei sobi
		if ($etenduse_id <= 0 || $piletid <= 0 || $piletid > $vabad_kohad['kohad']) {
			message_add('Broneeritavate piletite arv peab olema väiksem, kui vabade kohtade arv ja piletite arv peab olema nullist suurem');
			return false;
		}
		
		if ( model_booking($etenduse_id, $piletid) ) {
			message_add('Etendusele broneeritud piletite arv: '.$piletid);
			// saadame info modelisse, mis reaalse salvestamise teeb
			return true;
		}
		
		message_add('Andmete uuendamine ebaõnnestus!');
		return false;
	}
	
	function controller_delete($etenduse_id) {
		
		if ( !controller_user() ) {
			message_add('Kasutaja peab olema sisselogitud!');
			return false;
		}
		
		if ($etenduse_id <= 0) {
			message_add('Vigased sisendandmed');
			return false;
		}
		
		if ( model_delete($etenduse_id) ) {
			message_add('Kustutati rida '.$etenduse_id);
			// saadame info modelisse, mis reaalse salvestamise teeb
			return true;
		}
		
		message_add('Rea kustutamine ebaõnnestus!');
		return false;
	}
	
	function controller_user() {
		if( empty($_SESSION['user']) ) {
			return false;
		}
		return $_SESSION['user'];
	}
	
	//lisame uue kasutajakonto
	function controller_register($kasutajanimi, $parool) {
		
		if ($kasutajanimi == '' || $parool == '') {
			message_add('Kasutajanimi ja parool peavad olema sisestatud!');
			return false;
		}
		
		if ( model_user_add($kasutajanimi, $parool) ) {
			message_add('Teie konto on registreeritud');
			return true;
		}
		
		message_add('Konto registreerimine ebaõnnestus, kasutajanimi võib olla juba võetud!');
		return false;
	}
	
	function controller_login ($kasutajanimi, $parool) {
		
		if ($kasutajanimi == '' || $parool == '') {
			message_add('Kasutajanimi ja parool peavad olema sisestatud!');
			return false;
		}
		
		$kasutaja_id = model_user_get($kasutajanimi, $parool);
		
		if (!$kasutaja_id) {
			message_add('Vigane kasutajanimi või parool');
			return false;
		}
		
		session_regenerate_id();
		
		$_SESSION['user'] = $kasutaja_id;
		
		message_add('Tere tulemast, '.$kasutajanimi);
		
		return $kasutaja_id;
	}
	
	function controller_logout () {
		
		if ( isset( $_COOKIE[session_name()] ) ) {
			setcookie( session_name(), '', time() - 42000, '/' );
		}
		
		$_SESSION = array();
		
		session_destroy();
		
		message_add('Oled nüüd välja logitud');
		
		return true;
		
	}
	
	function message_add($message) {
		
		if ( empty($_SESSION['messages']) ) {
			$_SESSION['messages'] = array();
		}
		$_SESSION['messages'][] = $message;
	}
	
	function message_list() {
		
		if ( empty($_SESSION['messages']) ) {
			return array();
		}
		$messages = $_SESSION['messages'];
		$_SESSION['messages'] = array();
		
		return $messages;
	}
	