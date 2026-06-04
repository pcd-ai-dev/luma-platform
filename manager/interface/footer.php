<?php
/*
 *  @author Luma Prod - Pierre Cosmao Dumanoir
 *  @copyright  2003-2026 | Luma Prod - Pierre Cosmao Dumanoir
 *  @version  Release: 3
 */
?>

<div style="width:100%;">
  <table style="border:0px;" cellspacing="0" cellpadding="0" width="100%" style="text-align:center;">
    <tr>
      <td height="10"></td>
    </tr>
    <tr>
      <td style="text-align:center">
        <div>
          <?php 
          $formatter = new IntlDateFormatter('fr_FR', IntlDateFormatter::LONG, IntlDateFormatter::NONE);
		      $formatter ->setPattern("cccc dd MMMM YYYY"); $dateTime = new DateTime();
          echo ucfirst($formatter->format($dateTime));  ?> | &copy; <a href="https://www.lumaprod.com/" target="_blank">LUMA PROD</a> - Version 6.0 - <a href="/manager<?= $varLink;?>/admin_cgu.html" target="_self">Conditions d'utilisation</a>
          </div>
        </td>
      </tr>
    </table>
</div>

<!-- JS -->
<script type="text/javascript" src="/js/admin/agent/process-agent-manager.js"></script>
<!-- /JS -->