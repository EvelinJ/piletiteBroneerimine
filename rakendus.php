<?php
//rakenduse põhifail, mida avame url-is

//algatame sessiooni
session_start();

if ( empty( $_SESSION['csrf_token'] ) ) {
	$_SESSION['csrf_token'] = bin2hex( openssl_random_pseudo_bytes(20) );
}

//tagame, et andmed oleks saadval ja neid on võimalik salvestada
require('model.php');

require('controller.php');

//loogika, mis kontrollib, mis actioniga on tegu ja kutsub välja vastava tegevuse ehk vahendab kasutaja poolt tulnud käske õigesse kohta
if ( $_SERVER['REQUEST_METHOD'] == 'POST' ) {
    //tekitame muutuja, mida muudame, juhul kui tegevused saavad läbi ja result on ikka false, siis tekkis viga, kui on true, siis tegime vastava case toimingu
	$result = false;
	
	if ( !empty( $_POST['csrf_token'] ) && $_POST['csrf_token'] == $_SESSION['csrf_token'] ) {
	
        switch ($_POST['action']) {
            case 'add': 
		        $nimetus = $_POST['nimetus'];
				$aeg = date ('Y-m-d H:i:s', strtotime($_POST['aeg']));
				$kohad = intval($_POST['kohad']);
		        $result = controller_add($nimetus, $aeg, $kohad);
		        break;
			case 'booking': 
				$etenduse_id = intval($_POST['etenduse_id']);
				$piletid = intval($_POST['piletid']);
		        $result = controller_booking($etenduse_id, $piletid);
			    break;
            case 'delete': 
		        $etenduse_id = intval($_POST['etenduse_id']);
		        $result = controller_delete($etenduse_id);
			    break;
		    case 'register':
		        $kasutajanimi = htmlspecialchars($_POST['kasutajanimi']);
			    $parool = htmlspecialchars($_POST['parool']);
				$parool2 = htmlspecialchars($_POST['parool2']);
			    $result = controller_register($kasutajanimi, $parool, $parool2);
			    break;
		    case 'login':
		        $kasutajanimi = htmlspecialchars($_POST['kasutajanimi']);
			    $parool = htmlspecialchars($_POST['parool']);
			    $result = controller_login($kasutajanimi, $parool);
			    break;
		    case 'logout':
		        $result = controller_logout();
			    break;
	    }
    } else {
		message_add('Vigane päring, CSRF token ei vasta oodatule');
	}
	
	//kui result muutus true'ks suuname kasutaja ümber iseenda pihta.
	header('Location: rakendus.php');
	exit;
	
}

if ( !empty($_GET['action']) ) {
    //tekitame muutuja, mida muudame, juhul kui tegevused saavad läbi ja result on ikka false, siis tekkis viga, kui on true, siis tegime vastava toimingu
	$result = false;
	
    switch ($_GET['action']) {
		case 'gobooking': 
		    $etenduse_id = intval($_GET['etenduse_id']);
			$aeg = date ('Y-m-d H:i:s', strtotime($_GET['aeg']));
		    $result = controller_gobooking($etenduse_id, $aeg);
			break;
	    }
	
	//Juhul, kui result on false ehk ei õnnestunud broneerima minna, siis annab veateate ja jääb üldvaate lehele.
	if (!$result) {
		header('Location: rakendus.php');
	    exit;
	}
	
	// kui toiming õnnestus, siis kuvab etenduse detailvaate
	require('view_etendus.php');
	exit;
	
}

if( !empty($_GET['view']) ) {
	switch($_GET['view']) {
		case 'login':
		    require 'view_login.php';
			break;
		case 'register':
		    require 'view_register.php';
			break;
		case 'etendus':
		    require 'view_etendus.php';
			break;
		default:
		    header('Content-type: text/plain; charset=utf-8');
		    echo 'Tundmatu valik!';
			exit;
	}
} else {
	if( !controller_user() ) {
		header('Location: ' . $_SERVER['PHP_SELF'] . '?view=login');
		exit;
	}
	
	if ( empty($_GET['page']) ) {
		$page = 1;
	} else {
		$page = intval($_GET['page']);
	}
	
	if ($page < 1) {
		$page = 1;
	}
	
    require('view.php');
}

mysqli_close($l);

?>
