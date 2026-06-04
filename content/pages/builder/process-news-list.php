<?php

/*
 *  @author Luma Prod - Pierre Cosmao Dumanoir
 *  @copyright  2003-2026 | Luma Prod - Pierre Cosmao Dumanoir
 *  @version  Release: 3
 */
 
	//---------------------------------------------------------  
	// NEWS PROCESS
	//--------------------------------------------------------- 

	//---------------------------------------------------------  
	// INIT VARS
	//--------------------------------------------------------- 

		unset($_SESSION['offsetPost']);
		$_SESSION['offsetPost']=10;

		unset($_SESSION['idCatPost']);
		$_SESSION['idCatPost']=$idCategory;

	//---------------------------------------------------------  
	// CONSTRUCTION
	//--------------------------------------------------------- 


	//---------------------------------------------------------  
	// NEWS PROCESS
	//--------------------------------------------------------- 
			
		$textBuilder= $htmlText;
		$textBuilder= str_replace("#newsContent#", $newsContent, $textBuilder);
