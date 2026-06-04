
---

# docs/MIGRATION.md

```markdown
# Migration from Bootstrap x-editable

Most pages work by simply swapping the script:
- Remove old x-editable script
- Add `xeditable-lite.js` (after jQuery if you use jQuery)

### Confirmed parity
- `.editable()` plugin name & data attributes (`data-type`, `data-url`, `data-pk`, `data-name`, `data-title`, `data-source`, etc.)
- Callbacks: `validate`, `success`, `error`, `display`, `params`, `ajaxOptions`
- Methods: `show`, `hide`, `toggle`, `getValue`, `setValue`, `enable`, `disable`, `destroy`, `option`

### Differences (improvements)
- Works without Bootstrap; modern, accessible popup
- Smart `auto` placement
- **Searchable** selects
- Inline mode has icon-only actions, hidden error row by default
- Save shows **spinner** and is disabled during AJAX
