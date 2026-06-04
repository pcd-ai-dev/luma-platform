<?php
/*
 *  @author Luma Prod - Pierre Cosmao Dumanoir
 *  @copyright  2003-2026 | Luma Prod - Pierre Cosmao Dumanoir
 *  @version  Release: 3
 */
?>

<!-- Bouton flottant -->
<div id="ai-btn">
  <img src="/img/interface/icons/assistant.svg" width="50" height="50" />
</div>

<!-- Fenêtre chat -->
<div id="ai-chat">
  <div id="ai-header">
    Assistant IA
    <span id="ai-close">×</span>
  </div>

  <div id="ai-messages"></div>
<div class="ia-select-group">
  <button class="ia-select-btn active" data-provider="anthropic">Anthropic</button>
  <button class="ia-select-btn" data-provider="openai">Open AI</button>
</div>
  <div id="ai-input-area">
    <input id="ai-input" type="text" placeholder="Écris ton message..." />
    <button id="ai-send">Envoyer</button>
  </div>
</div>