<?php

    function controller_add($nimetus, $aeg, $kohad) {
		
		if ( !controller_user() ) {
			message_add('Kasutaja peab olema sisselogitud!');
			return false;
		}
		
		// kontrollime kas sisendväärtused on oodatud kujul või mitte
		if ($nimetus == '' || $aeg == '' || $kohad <= 0) {
			message_add('Sisestatud väärtused on vigased. Kõik andmed peavad olema sisestatud ja kuupäev ei tohi olla möödunud!');
			return false;
		}
		
		if ( model_add($nimetus, $aeg, $kohad) ) {
			message_add('Lisati uus etendus');
			// saadame info modelisse, mis reaalse salvestamise teeb
			return true;
		}
		
		message_add('Andmete lisamine ebaõnnestus!');
		
		return false;
	}
	
	function controller_gobooking($etenduse_id) {
		
		if ( !controller_user() ) {
			message_add('Kasutaja peab olema sisselogitud!');
			return false;
		}
		
		if ($etenduse_id <= 0) {
			message_add('Vigased sisendandmed');
			return false;
		}
		
		if ( model_gobooking($etenduse_id) ) {
			message_add('Valisid etenduse '.$etenduse_id);
			// saadame info modelisse, mis reaalse salvestamise teeb
			return true;
		}
		
		message_add('Etenduse valimine ebaõnnestus');
		return false;
	}
	
	function controller_booking($broneeringu_id, $etenduse_id, $kasutaja_id, $piletid) {
		
		if ( !controller_user() ) {
			message_add('Kasutaja peab olema sisselogitud!');
			return false;
		}
		
		// kui id või kogus on nullist väiksem või 0, siis see meile ei sobi
		if ($broneeringu_id <= 0 || $etenduse_id <= 0 || $kasutaja_id <= 0 || $piletid <= 0) {
			message_add('Sisestatud andmed on vigased!');
			return false;
		}
		
		if ( model_booking($broneeringu_id, $etenduse_id, $kasutaja_id, $piletid) ) {
			message_add('Broneering '.$broneeringu_id);
			// saadame info modelisse, mis reaalse salvestamise teeb
			return true;
		}
		
		message_add('Andmete uuendamine ebaõnnestus');
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
		
		message_add('Rea kustutamine ebaõnnestus');
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
			message_add('Sisestatud andmed ei sobi!');
			return false;
		}
		
		if ( model_user_add($kasutajanimi, $parool) ) {
			message_add('Konto on registreeritud');
			return true;
		}
		
		message_add('Konto registreerimine ebaõnnestus, kasutajanimi võib olla juba võetud');
		return false;
	}
	
	function controller_login ($kasutajanimi, $parool) {
		
		if ($kasutajanimi == '' || $parool == '') {
			message_add('Kasutajanimi ja parool peavad olema sisestatud!');
			return false;
		}
		
		$id = model_user_get($kasutajanimi, $parool);
		
		if (!$id) {
			message_add('Vigane kasutajanimi või parool');
			return false;
		}
		
		session_regenerate_id();
		
		$_SESSION['user'] = $id;
		
		message_add('Tere tulemast, '.$kasutajanimi);
		
		return $id;
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
	