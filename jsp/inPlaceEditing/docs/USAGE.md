# Usage Guide

XEditable Lite provides both **popup** and **inline** editing modes with support for text, select, date, and other input types.
It’s a full drop-in replacement for Bootstrap x-editable — but modernized, lightweight, and framework-free.

---

## Getting Started

### Include the Library

```html
<script src="xeditable-lite.js"></script>
```

If you’re using jQuery, the plugin will attach automatically (`.editable()` works as before).
Otherwise, use the built-in vanilla API (`XEditable.init()` or `XEditable.initAll()`).

### Basic Example

```html
<span class="editable"
      data-type="text"
      data-name="username"
      data-title="Username">superuser</span>

<script>
  $('.editable').editable({
    url: '/api/save'
  });
</script>
```

---

## Supported Input Types

| Type | Description | Example |
|------|--------------|----------|
| `text`, `number`, `email`, `url`, `password`, `textarea` | Standard text inputs | `<span data-type="text">Edit me</span>` |
| `date`, `datetime-local` | Native date/time pickers with format display support | `<span data-type="date" data-format="DD/MM/YYYY">2025-01-10</span>` |
| `select` | Dropdown (searchable) | `<a data-type="select" data-title="Choose">Option</a>` |
| `checkbox`, `radio` | Boolean or multiple-choice | `<span data-type="checkbox">true</span>` |

---

## Popup Editing

By default, elements open in a floating popup positioned automatically within the viewport.

```html
<a href="#" class="editable"
   data-type="textarea"
   data-name="bio"
   data-title="Short bio">Type here...</a>

<script>
  $('.editable').editable({
    url: '/api/user/bio'
  });
</script>
```

---

## Inline Editing

Use `data-mode="inline"` (or `mode: 'inline'` in JS) to enable inline editing with compact icon-only Save/Cancel buttons.

```html
<span class="editable-inline"
      data-mode="inline"
      data-type="textarea"
      data-name="bio"
      data-title="Bio">Inline bio text…</span>

<script>
  $('.editable-inline').editable({ url: '/api/save' });
</script>
```

Inline mode has a hidden error row that appears only when a validation or AJAX error occurs.

---

## Select / Dropdown Fields

### Local Source

```html
<a href="#" id="gender"
   data-type="select"
   data-name="gender"
   data-title="Gender">Female</a>

<script>
  $('#gender').editable({
    url: '/api/save',
    source: [
      { value: 1, text: 'Male' },
      { value: 2, text: 'Female' },
      { value: 3, text: 'Transgender' }
    ]
  });
</script>
```

✅ Displays **“Male/Female/Transgender”** (the `text`) after save.  
Submits `1/2/3` (the `value`) to the server.

### Searchable Dropdown

All selects are searchable by default — type to filter options dynamically.

### Remote Source

```html
<a href="#" id="country"
   data-type="select"
   data-title="Select Country"></a>

<script>
  $('#country').editable({
    url: '/api/save',
    source: '/api/countries' // Must return [{value:'IN',text:'India'}, ...]
  });
</script>
```

---

## Default Values and Placeholders

### Default Value

If an editable element has no value, you can provide a fallback with `data-default`:

```html
<span class="editable"
      data-type="text"
      data-name="city"
      data-title="City"
      data-default="Bengaluru"></span>
```

### Placeholder for Selects

When no default value exists, the `data-title` acts as a **non-selectable placeholder**:

```html
<a href="#" data-type="select" data-title="Lead Status"></a>
```

---

## Date and Datetime Formatting

Format display via `data-format` (default: `YYYY-MM-DD`).

```html
<span id="dob"
      data-type="date"
      data-format="DD/MM/YYYY"
      data-name="dob"
      data-title="Date of Birth">1990-07-15</span>

<script>
  $('#dob').editable({ url: '/api/save' });
</script>
```

- Displayed as `15/07/1990`
- Submitted as `1990-07-15`

---

## AJAX Behavior

When `url` is provided:
- Save button disables and shows a spinner during AJAX.
- On success, the popup closes and value updates.
- On error, the button re-enables and the error message appears.

```html
<span id="email"
      data-type="email"
      data-title="Email">user@example.com</span>

<script>
  $('#email').editable({
    url: '/api/profile/email',
    ajaxOptions: { type: 'POST', dataType: 'json' },
    validate: v => (!v ? 'Required field' : null)
  });
</script>
```

---

## Methods

```js
$('#username').editable('show');
$('#username').editable('hide');
$('#username').editable('enable');
$('#username').editable('disable');
$('#username').editable('getValue');
$('#username').editable('setValue', 'newname');
```

---

## Events

| Event | Description |
|--------|-------------|
| `shown` | Fired when editor opens |
| `hidden` | Fired when editor closes |
| `save` | Fired after successful save (`e.detail.newValue`, `e.detail.response`) |

```js
document.querySelector('#username').addEventListener('save', e => {
  console.log('Saved:', e.detail.newValue);
});
```

---

## Full Example

```html
<a href="#" id="role"
   data-type="select"
   data-name="role"
   data-title="User Role"
   data-default="Editor"></a>

<script>
  $('#role').editable({
    url: '/api/save',
    source: [
      { value: 'Admin', text: 'Administrator' },
      { value: 'Editor', text: 'Editor' },
      { value: 'Viewer', text: 'Viewer' }
    ]
  });
</script>
```

---

## Summary
- Drop-in replacement for Bootstrap x-editable
- Works standalone or with jQuery
- Searchable dropdowns, formatted dates, inline editing
- AJAX-ready with spinner & disabled save state
- Modern UI, no external dependencies
