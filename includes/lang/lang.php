<?php session_start();

/*
 *  @author Luma Prod - Pierre Cosmao Dumanoir
 *  @copyright  2003-2026 | Luma Prod - Pierre Cosmao Dumanoir
 *  @version  Release: 3
 */


    //---------------------------------------------------------
	// UNSET VARS SESSION
	//---------------------------------------------------------

		unset($_SESSION['langue']);
		unset($_SESSION['isolang']);

    //---------------------------------------------------------
	// VARS
	//---------------------------------------------------------
	
		if(preg_match("#^[0-9]+$#",$_GET['idLang'])){$idLang=intval($_GET['idLang']);}else{$idLang=1;}

		if($idLang==1){$_SESSION['isolang']="fr";}else{$_SESSION['isolang']="en";}
		$_SESSION['langue']=$idLang;

		$url=$_SERVER['HTTP_REFERER'];


    //---------------------------------------------------------
	// PROCESS
	//---------------------------------------------------------

		if(empty($url)){
			$url="/";
		}else{
			$url=$_SERVER['HTTP_REFERER'];
		}


    //---------------------------------------------------------
	// REDIRECT
	//---------------------------------------------------------

		header("Location: ".$url);