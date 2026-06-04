
---

# docs/API.md

```markdown
# API Reference

## Initialization
- **jQuery**: `$(selector).editable(options?)`
- **Vanilla**:
  - Per element: `XEditable.init(el, options?)`
  - Batch: `XEditable.initAll(selector, options?)`

## Options (subset, x-editable parity)
- `type`: `'text'|'textarea'|'number'|'email'|'url'|'password'|'date'|'datetime-local'|'select'|'checkbox'|'radio'`
- `name`: field name (sent to server)
- `url`: string or `() => string` (AJAX endpoint)
- `pk`: primary key (sent with submit)
- `title`: label/placeholder; for selects it becomes a **non-selectable** placeholder when no default exists
- `placement`: `'top'|'right'|'bottom'|'left'|'auto'`
- `mode`: `'popup'|'inline'` (default `'popup'`)
- `source`: for selects/radios
  - Array: `[{value, text}, ...]`
  - Object map: `{value: 'Text', ...}`
  - URL (string) or function returning the above
- `multiple`: (select) boolean
- `defaultValue`: initial fallback if current value is empty (or use `data-default`)
- `ajaxOptions`: `{ type, dataType, contentType, headers }` (defaults mimic x-editable)
- `params`: object or `(params) => newParams`
- `display(value, sourceData)`: custom renderer (optional)
- `validate(value)`: return string to show an error; prevents submit
- `success(response, newValue)`: return string to show error, like x-editable
- `error(xhrOrNull, message)`

## Methods
- Instance or jQuery: `show()`, `hide()`, `toggle()`, `enable()`, `disable()`, `destroy()`
- `getValue(includeEmpty)`, `setValue(value)`, `option(key[, value])`

## Events
Dispatched as DOM CustomEvents **and** jQuery events:
- `shown`, `hidden`, `save`
Payload example for `save`:
```js
el.addEventListener('save', (e) => {
  console.log(e.detail.newValue, e.detail.submitted, e.detail.response);
});
