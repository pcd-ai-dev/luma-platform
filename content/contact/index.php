<?php

/*
 *  @author Luma Prod - Pierre Cosmao Dumanoir
 *  @copyright  2003-2026 | Luma Prod - Pierre Cosmao Dumanoir
 *  @version  Release: 3
 */

    declare(strict_types=1);

    // ---------------------------------------------------------
    // Function
    // ---------------------------------------------------------

        function clean(string $v): string {
            return trim(htmlspecialchars($v, ENT_QUOTES, 'UTF-8'));
        }

    // ---------------------------------------------------------
    // MailSetUp
    // ---------------------------------------------------------

        $defaultRecipient = $is->contactRecipient;

        if ((isset($_POST['name'])) && (isset($_POST['code']))) {

            (isset($_SESSION['inputForm']) ? $_SESSION['inputForm'] : '');

            $name = (isset($_POST['name'])) ? clean($_POST['name']) : '';
            $_SESSION['inputForm']['name'] = $name;

            $phone = (isset($_POST['phone'])) ? clean($_POST['phone']) : '';
            $_SESSION['inputForm']['phone'] = $phone;

            $email = (isset($_POST['email'])) ? clean($_POST['email']) : '';
            $_SESSION['inputForm']['email'] = $email;

            $msg = (isset($_POST['msg'])) ? clean($_POST['msg']) : '';
            $_SESSION['inputForm']['msg'] = $msg;

            $recipient = (isset($_POST['recipient'])) ? clean($_POST['recipient']) :  $defaultRecipient;

            // Récupération du CAPTCHA sécurisé (SHA-256)
            (isset($_SESSION['cryptcode'])) ? $cryptcode = $_SESSION['cryptcode'] : '';

            $globalVar = $name . '-' . $phone . '-' . $email . '-' . $msg;

            $honeypot = (isset($_POST['website'])) ? clean($_POST['website']) : '';
            $time_start = (isset($_POST['form_time'])) ? clean($_POST['website']) : 0;



    // ---------------------------------------------------------
    // Vérification injection
    // ---------------------------------------------------------

        if (!preg_match("/\#file_links\b/i", $globalVar)) {

            // Vérification cryptogramme SHA-256

            if ($honeypot !== '') {exit("Spam détecté.");}

            if (time() - intval($time_start) < 3) {exit("Envoi trop rapide (bot ?)");}

            if (!isset($_SESSION['captcha'])) {exit("CAPTCHA absent.");}


            if (hash('sha256', $_POST['code']) === $_SESSION['captcha']) {

                unset($_SESSION['captcha']);
						
				$subject = $siteName." : Message";

				$Msg="";
				$Msg.= "Nom : ".$name."<br/>";
				$Msg.= "Courriel : ".$email."<br/>";
				$Msg.= "Tel : ".$phone."<br/>";
				$Msg.= "Message : ".$msg."<br/><br/>";
				$Msg.= "____________________________________<br/><br/>";
				$Msg.= $siteHostBase."<br/>";

                $Msg=mb_convert_encoding($Msg, 'ISO-8859-1', 'UTF-8');

				$mail->addAddress($recipient); 
				$mail->Subject = $subject;
				$mail->Body    = $Msg;

				if(!$mail->send()){
					$status=0;
					echo 'Erreur Mail : ' . $mail->ErrorInfo;
				}else{
					$mail->ClearAllRecipients();
					$status=1;
					unset($_SESSION['inputForm']);
					header("Location: ".$siteHost.((!empty($varLinkData))? "/". $varLinkData : "")."/".$is->contactSuccessPage."-contact-mail-send.html");
					exit;
				} 


        } else {
            $msgcrypto = '<span style="color:red;">'.$translations['ERRORCODETXT'].'</span>';
        }
    }

}