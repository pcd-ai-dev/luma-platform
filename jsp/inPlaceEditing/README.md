# XEditable Lite
_A drop-in replacement for Bootstrap x-editable — modern popups & inline editing, searchable selects, AJAX, and more._

[![License: MIT](https://img.shields.io/badge/License-MIT-green.svg)](#license)
[![Status](https://img.shields.io/badge/status-stable-blue)]()

XEditable Lite keeps the original `.editable()` API and data-attributes, so you can swap it in without touching existing page code. It also works **without Bootstrap** and **without jQuery** (a jQuery bridge is included for parity).

## ✨ Features
- **Drop-in x-editable API** (`$(el).editable(options)`, data attributes, callbacks, methods)
- **Popup & Inline** modes (viewport-aware popups, inline with icon-only actions)
- **Searchable select** (type to filter; starts-with ranked)
- **Date formatting** via `data-format="YYYY-MM-DD"` (default `YYYY-MM-DD`)
- **AJAX**: remote `source` + submit; spinner + disabled Save while processing
- **Default/placeholder** logic for selects (default value first; else non-selectable `data-title`)
- **Accessible & modern UI** (no Bootstrap dependency)

## Quick start

```html
<!-- 1) Include (with or without jQuery) -->
<script src="/path/to/xeditable.js"></script>

<!-- 2) Your editable elements -->
<span class="editable"
      data-type="text"
      data-name="username"
      data-title="Username">superuser</span>

<!-- 3) Initialize -->
<script>
  // jQuery parity
  $('.editable').editable({ url: '/api/save' });

  // or vanilla
  XEditable.initAll('.editable', { url: '/api/save' });
</script>
