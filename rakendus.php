<?php
//rakenduse põhifail, mida avame url-is

//algatame sessiooni
session_start();

if ( empty( $_SESSION['csrf_token'] ) ) {
	$_SESSION['csrf_token'] = bin2hex( openssl_random_pseudo_bytes(20) );
}

//tagame, et andmed oleks saadval ja neid on võimalik salvestada
require('model.php');

//andmete lisamise ja kustutamise funktsioonid
require('controller.php');

//loogika, mis kontrollib, mis actioniga on tegu ja kutsub vastava tegevuse ehk vahendab kasutaja poolt tulnud käske õigesse kohta, see võiks ka eraldi failis olla
if ( $_SERVER['REQUEST_METHOD'] == 'POST' ) {
    //tekitame muutuja, mida muudame, juhul kui tegevused saavad läbi ja result on ikka false, siis tekkis viga, kui on true, siis tegime kas toimingu add või delete
	$result = false;
	
	if ( !empty( $_POST['csrf_token'] ) && $_POST['csrf_token'] == $_SESSION['csrf_token'] ) {
	
        switch ($_POST['action']) {
            case 'add': 
		        $nimetus = $_POST['nimetus'];
				$aeg = date ('Y-m-d H:i:s', strtotime($_POST['aeg']));
				$kohad = intval($_POST['kohad']);
		        $result = controller_add($nimetus, $aeg, $kohad);
		        break;
			case 'gobooking': 
		        $etenduse_id = intval($_POST['etenduse_id']);
		        $result = controller_gobooking($etenduse_id);
				header('Location: ' . $_SERVER['PHP_SELF'] . '?view=etendus');
				exit;
			    break;
			case 'booking': 
		        $broneeringu_id = intval($_POST['broneeringu_id']);
				$etenduse_id = intval($_POST['etenduse_id']);
				$kasutaja_id = intval($_POST['kasutaja_id']);
				$piletid = intval($_POST['piletid']);
		        $result = controller_booking($broneeringu_id, $etenduse_id, $kasutaja_id, $piletid);
			    break;
            case 'delete': 
		        $etenduse_id = intval($_POST['etenduse_id']);
		        $result = controller_delete($etenduse_id);
			    break;
		    case 'register':
		        $kasutajanimi = $_POST['kasutajanimi'];
			    $parool = $_POST['parool'];
			    $result = controller_register($kasutajanimi, $parool);
			    break;
		    case 'login':
		        $kasutajanimi = $_POST['kasutajanimi'];
			    $parool = $_POST['parool'];
			    $result = controller_login($kasutajanimi, $parool);
			    break;
		    case 'logout':
		        $result = controller_logout();
			    break;
	    }
    } else {
		message_add('Vigane päring, CSRF token ei vasta oodatule');
	}
	
	//Juhul, kui result on false ehk ei toimunud ühtegi toimingut, siis annab veateate.
	if (!$result) {
		message_add('Päring ebaõnnestus!');
	}
	
	
	//kui result muutus true'ks suuname kasutaja ümber iseenda pihta.
	header('Location: rakendus.php');
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
