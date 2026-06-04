/*!
 * XEditable Lite (Drop-in for Bootstrap x-editable)
 * v1.5.1 — zero-dependency core; optional jQuery bridge.
 * Adds: searchable selects, date display formatting (data-format), spinner+disable on save,
 * inline icon-only buttons, hidden error row, smart popups, default/placeholder logic.
 * FIX: when source is local array/object, display the option's "text" (not "value") after selection.
 * License: MIT
 */
(function (global, factory) {
  if (typeof module === 'object' && typeof module.exports === 'object') {
    module.exports = factory(global);
  } else {
    global.XEditable = factory(global);
  }
})(typeof window !== 'undefined' ? window : this, function (window) {
  'use strict';

  const doc = window.document;

  // ---------------------------
  // Utilities
  // ---------------------------
  const isFn = v => typeof v === 'function';
  const isStr = v => typeof v === 'string';
  const isObj = v => v && typeof v === 'object' && !Array.isArray(v);
  const isPromise = v => !!v && (typeof v === 'object' || isFn(v)) && isFn(v.then);
  const toArray = arrLike => Array.prototype.slice.call(arrLike || []);
  const clamp = (n, min, max) => Math.min(Math.max(n, min), max);

  function deepMerge(target, ...sources) {
    target = target || {};
    for (const src of sources) {
      if (!src) continue;
      for (const k of Object.keys(src)) {
        const v = src[k];
        if (Array.isArray(v)) target[k] = v.slice();
        else if (isObj(v)) target[k] = deepMerge(isObj(target[k]) ? target[k] : {}, v);
        else target[k] = v;
      }
    }
    return target;
  }

  function parseMaybeJSON(val) {
    if (!isStr(val)) return val;
    const s = val.trim();
    if (!s) return s;
    try { return JSON.parse(s); } catch (_) { return val; }
  }

  function uid(prefix='xe') {
    return prefix + '_' + Math.random().toString(36).slice(2, 9);
  }

  function on(el, type, handler, opts) {
    el.addEventListener(type, handler, opts || false);
    return () => el.removeEventListener(type, handler, opts || false);
  }

  function triggerDOM(el, name, detail) {
    const ev = new CustomEvent(name, { bubbles: true, cancelable: true, detail });
    el.dispatchEvent(ev);
  }

  function triggerJQ(el, name, payload) {
    if (window.jQuery) {
      try { window.jQuery(el).trigger(name, payload); } catch (_) {}
    }
  }

  // Date helpers
  const DEFAULT_DATE_FMT = 'YYYY-MM-DD';
  const DEFAULT_DT_FMT = 'YYYY-MM-DDTHH:mm';
  function pad2(n){ n=String(n); return n.length<2?'0'+n:n; }
  function formatByTokens(date, fmt) {
    if (!date || isNaN(date.getTime())) return '';
    const Y = date.getFullYear();
    const M = pad2(date.getMonth()+1);
    const D = pad2(date.getDate());
    const H = pad2(date.getHours());
    const m = pad2(date.getMinutes());
    return fmt
      .replace(/YYYY/g, String(Y))
      .replace(/MM/g, M)
      .replace(/DD/g, D)
      .replace(/HH/g, H)
      .replace(/mm/g, m);
  }
  function parseByTokens(str, fmt) {
    if (!str) return null;
    const rx = fmt
      .replace(/YYYY/g,'(?<Y>\\d{4})')
      .replace(/MM/g,'(?<M>\\d{2})')
      .replace(/DD/g,'(?<D>\\d{2})')
      .replace(/HH/g,'(?<H>\\d{2})')
      .replace(/mm/g,'(?<m>\\d{2})');
    const m = new RegExp('^'+rx+'$').exec(str);
    if (!m || !m.groups) return null;
    const Y = +(m.groups.Y ?? '0');
    const Mo = +(m.groups.M ?? '1')-1;
    const D = +(m.groups.D ?? '1');
    const H = +(m.groups.H ?? '0');
    const Mi = +(m.groups.m ?? '0');
    const dt = new Date(Y, Mo, D, H, Mi);
    return isNaN(dt.getTime()) ? null : dt;
  }

  // ---------------------------
  // Styles (auto-injected)
  // ---------------------------
  const BASE_STYLES = `
:root {
  --xe-bg: #fff; --xe-fg: #111827; --xe-border: #e5e7eb; --xe-muted:#6b7280;
  --xe-accent: #3b82f6; --xe-danger:#ef4444;
  --xe-radius: 12px; --xe-shadow: 0 12px 28px rgba(0,0,0,.12), 0 8px 8px rgba(0,0,0,.08);
  --xe-z: 2147483000;
}
.xe-editable { text-decoration: underline dotted; cursor: pointer; text-underline-offset: 2px; }
.xe-editable.xe-disabled { cursor: not-allowed; text-decoration: underline dotted rgba(107,114,128,.5); }
.xe-popup { position: fixed; z-index: var(--xe-z); background: var(--xe-bg); color: var(--xe-fg);
  border: 1px solid var(--xe-border); border-radius: var(--xe-radius);
  box-shadow: var(--xe-shadow); min-width: 260px; max-width: min(92vw, 480px); }
.xe-header { padding: 12px 14px; font-weight: 600; border-bottom: 1px solid var(--xe-border); }
.xe-body { padding: 12px 14px; }
.xe-field, .xe-field * { font: inherit; }
.xe-field { width: 100%; box-sizing: border-box; padding: 8px 10px; border-radius: 10px;
  border: 1px solid var(--xe-border); outline: none; background: #fff; }
.xe-field:focus { border-color: var(--xe-accent); box-shadow: 0 0 0 3px rgba(59,130,246,.25); }
.xe-group { display: grid; gap: 8px; }
.xe-radio-group, .xe-check-group { display: grid; gap: 6px; padding-top: 4px; }
.xe-actions { display:flex; gap:8px; padding: 10px 14px 14px; justify-content:flex-end; }
.xe-btn { appearance: none; border:1px solid var(--xe-border); background:#f9fafb; color:#111827;
  padding: 6px 10px; border-radius: 10px; cursor:pointer; font-size: 13px; line-height: 1;
  display:inline-flex; align-items:center; gap:6px; }
.xe-btn:hover { filter: brightness(.98); }
.xe-btn:disabled { opacity:.65; cursor:not-allowed; }
.xe-btn svg { width:14px; height:14px; display:inline-block; }
.xe-btn-primary { background: var(--xe-accent); color:#fff; border-color: var(--xe-accent); }
.xe-error { color: var(--xe-danger); font-size: 12px; padding: 6px 14px 0; min-height: 18px; }
.xe-error:empty { display:none; }
.xe-muted { color: var(--xe-muted); }
.xe-arrow { position:absolute; width:12px; height:12px; transform: rotate(45deg); background: var(--xe-bg); border:1px solid var(--xe-border); }
.xe-arrow[data-side="top"] { bottom:-7px; border-top:none; border-left:none; }
.xe-arrow[data-side="bottom"] { top:-7px; border-bottom:none; border-right:none; }
.xe-arrow[data-side="left"] { right:-7px; border-left:none; border-bottom:none; }
.xe-arrow[data-side="right"] { left:-7px; border-right:none; border-top:none; }
.xe-hidden { display:none !important; }
.xe-highlight { animation: xe-hl 1.6s ease-in-out; }
@keyframes xe-hl { 0% { background: #fff3; } 25% { background: #dbeafe; } 100% { background: none; } }

/* Inline mode (with hidden error row until used) */
.xe-inline {
  display: inline-grid;
  grid-template-columns: auto auto;
  grid-template-rows: auto auto;
  gap: 8px 8px;
  align-items: center;
  background: var(--xe-bg);
  border: 1px solid var(--xe-border);
  border-radius: 10px;
  padding: 6px 8px;
}
.xe-inline .xe-input   { grid-column: 1 / span 1; }
.xe-inline .xe-actions { grid-column: 2 / span 1; padding: 0; }
.xe-inline .xe-error-row { grid-column: 1 / span 2; padding: 0 2px 2px; display:none; }
.xe-inline .xe-error-row.xe-visible { display:block; }
.xe-inline .xe-error { padding: 0; }
.xe-inline .xe-btn { padding: 6px; width: 28px; height: 28px; justify-content: center; }
.xe-inline .xe-btn span { display: none; }

/* Spinner */
.xe-spinner { width:14px; height:14px; display:inline-block;
  border:2px solid currentColor; border-right-color: transparent; border-radius:50%;
  animation: xe-spin .8s linear infinite; }
@keyframes xe-spin { to { transform: rotate(360deg);} }

/* Searchable select */
.xe-select-wrap { display:grid; gap:6px; }
.xe-select-search { width:100%; box-sizing:border-box; padding:6px 10px; border-radius: 10px;
  border:1px solid var(--xe-border); }
.xe-select-search:focus { border-color: var(--xe-accent); box-shadow: 0 0 0 3px rgba(59,130,246,.25); }
`;

  function ensureStyles() {
    if (doc.getElementById('xe-styles')) return;
    const st = doc.createElement('style');
    st.id = 'xe-styles';
    st.textContent = BASE_STYLES;
    doc.head.appendChild(st);
  }

  // ---------------------------
  // DOM helpers
  // ---------------------------
  function el(tag, attrs) {
    const n = doc.createElement(tag);
    attrs = attrs || {};
    for (const k in attrs) {
      if (k === 'text') n.textContent = attrs[k];
      else n.setAttribute(k, attrs[k]);
    }
    return n;
  }
  function cssField(inst) {
    return 'xe-field' + (inst.options.inputclass ? (' ' + inst.options.inputclass) : '');
  }
  function setCommonAttrs(input, inst) {
    const ph = inst.options.placeholder || inst.options.title || inst.options.name || '';
    if (input.setAttribute) input.setAttribute('placeholder', ph);
  }
  function normalizeSource(source) {
    if (!source) return [];
    if (isStr(source)) return []; // URL handled elsewhere
    if (Array.isArray(source)) {
      return source.map(it => {
        if (isObj(it)) {
          const value = 'value' in it ? it.value : it.id;
          const text = 'text' in it ? it.text : it.name || String(value);
          return { value, text };
        }
        return { value: it, text: String(it) };
      });
    }
    if (isObj(source)) return Object.keys(source).map(k => ({ value: k, text: source[k] }));
    return [];
  }
  function svgIcon(name) {
    const NS = 'http://www.w3.org/2000/svg';
    const svg = document.createElementNS(NS, 'svg');
    svg.setAttribute('viewBox','0 0 24 24'); svg.setAttribute('aria-hidden','true');
    const path = document.createElementNS(NS, 'path');
    path.setAttribute('fill','currentColor'); path.setAttribute('stroke','currentColor'); path.setAttribute('stroke-width','0');
    if (name === 'check')      path.setAttribute('d','M9 16.2l-3.5-3.5-1.4 1.4L9 19 20.3 7.7l-1.4-1.4z');
    else if (name === 'x')     path.setAttribute('d','M18.3 5.7L12 12l6.3 6.3-1.4 1.4L10.6 13.4 4.3 19.7 2.9 18.3 9.2 12 2.9 5.7 4.3 4.3 10.6 10.6 16.9 4.3z');
    else                       path.setAttribute('d','');
    svg.appendChild(path);
    return svg;
  }

  // ---------------------------
  // Type registry (inputs)
  // ---------------------------
  const TypeBuilders = {
    text(inst) { const input = el('input', { type: 'text', class: cssField(inst) }); setCommonAttrs(input, inst); return input; },
    textarea(inst, opt) { const ta = el('textarea', { rows: opt.rows || 4, class: cssField(inst) }); setCommonAttrs(ta, inst); return ta; },
    number(inst) { const input = el('input', { type: 'number', class: cssField(inst) }); setCommonAttrs(input, inst); return input; },
    email(inst) { const input = el('input', { type: 'email', class: cssField(inst) }); setCommonAttrs(input, inst); return input; },
    url(inst) { const input = el('input', { type: 'url', class: cssField(inst) }); setCommonAttrs(input, inst); return input; },
    password(inst) { const input = el('input', { type: 'password', class: cssField(inst) }); setCommonAttrs(input, inst); return input; },
    date(inst) { const input = el('input', { type: 'date', class: cssField(inst) }); setCommonAttrs(input, inst); return input; },
    'datetime'(inst) { const input = el('input', { type: 'datetime-local', class: cssField(inst) }); setCommonAttrs(input, inst); return input; },
    'datetime-local'(inst) { const input = el('input', { type: 'datetime-local', class: cssField(inst) }); setCommonAttrs(input, inst); return input; },
    checkbox(inst, opt) {
      const wrap = el('label', { class: 'xe-check-group' });
      const id = uid('xe_chk');
      const input = el('input', { type: 'checkbox', id });
      if (opt && (opt.value === true || opt.value === 'true' || opt.value === 1 || opt.value === '1')) input.checked = true;
      const lab = el('span', { text: opt && opt.label ? String(opt.label) : inst.options.title || inst.options.name || '' });
      const l = el('label', { for: id, style: 'display:flex; gap:8px; align-items:center;' });
      l.appendChild(input); l.appendChild(lab); wrap.appendChild(l);
      wrap.getValue = () => input.checked ? (opt.trueValue ?? true) : (opt.falseValue ?? false);
      wrap.setValue = (v) => { input.checked = !!(v === true || v === 'true' || v === 1 || v === '1'); };
      return wrap;
    },
    radio(inst, opt) {
      const cont = el('div', { class: 'xe-radio-group' });
      const name = uid('xe_radio');
      const src = normalizeSource(opt.source || []);
      for (const item of src) {
        const id = uid('xe_r');
        const lab = el('label', { for: id, style: 'display:flex; gap:8px; align-items:center;' });
        const input = el('input', { type: 'radio', name, id, value: String(item.value) });
        lab.appendChild(input); lab.appendChild(el('span', { text: String(item.text) }));
        cont.appendChild(lab);
      }
      cont.getValue = () => { const sel = cont.querySelector('input[type="radio"]:checked'); return sel ? sel.value : ''; };
      cont.setValue = (v) => { const a = cont.querySelectorAll('input[type="radio"]'); for (const i of a) i.checked = String(i.value) === String(v); };
      return cont;
    },

    // SELECT with search box + "default-first; else title placeholder" logic (value/text match)
    select(inst, opt) {
      const isMultiple = !!opt.multiple;
      const wrap = el('div', { class: 'xe-select-wrap' });
      const search = el('input', { class: 'xe-select-search', type: 'text', placeholder: 'Search...' });
      const sel = el('select', { class: cssField(inst), ...(isMultiple ? { multiple: true } : {}) });
      wrap.appendChild(search);
      wrap.appendChild(sel);

      const src = normalizeSource(opt.source || []);
      for (const item of src) {
        const o = el('option', { value: String(item.value), text: String(item.text) });
        sel.appendChild(o);
      }

      const findIndexByValueOrText = (s) => {
        if (s == null) return -1;
        const needle = String(s).toLowerCase();
        for (let i = 0; i < sel.options.length; i++) {
          const o = sel.options[i];
          if (o.disabled) continue;
          const ov = String(o.value).toLowerCase();
          const ot = String(o.text).toLowerCase();
          if (ov === needle || ot === needle) return i;
        }
        return -1;
      };

      // Intended selection from current value or defaultValue
      let intended = inst._state && inst._state.value;
      const isEmpty = intended === '' || intended === null || intended === undefined || (Array.isArray(intended) && intended.length === 0);
      if (isEmpty && inst.options.defaultValue !== undefined) intended = inst.options.defaultValue;

      if (!isMultiple) {
        const hasTitle = !!inst.options.title;
        let matchIdx = -1;
        if (!(intended === '' || intended == null)) matchIdx = findIndexByValueOrText(intended);
        if (matchIdx >= 0) sel.selectedIndex = matchIdx;
        else {
          if (hasTitle) {
            const ph = el('option', { value: '', text: String(inst.options.title) });
            ph.disabled = true; ph.hidden = true; ph.className = 'xe-placeholder';
            sel.insertBefore(ph, sel.firstChild);
            sel.selectedIndex = 0;
          } else {
            for (let i = 0; i < sel.options.length; i++) { const o = sel.options[i]; if (!o.disabled) { sel.selectedIndex = i; break; } }
          }
        }
      }

      // Search/filter behavior
      const original = toArray(sel.options).map(o => ({ value:o.value, text:o.text, disabled:o.disabled, isPh:o.className==='xe-placeholder' }));
      function applyFilter(q) {
        const query = (q||'').trim().toLowerCase();
        while (sel.options.length) sel.remove(0);
        let list = original.filter(o => !o.isPh);
        if (query) {
          const starts = list.filter(o => o.text.toLowerCase().startsWith(query) || o.value.toLowerCase().startsWith(query));
          const contains = list.filter(o =>
            !(o.text.toLowerCase().startsWith(query) || o.value.toLowerCase().startsWith(query)) &&
            (o.text.toLowerCase().includes(query) || o.value.toLowerCase().includes(query))
          );
          list = [...starts, ...contains];
        }
        const hadPh = original.length && original[0].isPh;
        if (hadPh) {
          const ph = el('option', { value:'', text: String(inst.options.title || '') }); ph.disabled = true; ph.hidden = true; ph.className='xe-placeholder';
          sel.appendChild(ph);
        }
        for (const item of list) {
          const o = el('option', { value: item.value, text: item.text }); if (item.disabled) o.disabled = true;
          sel.appendChild(o);
        }
        const current = wrap.getValue();
        if (current && findIndexByValueOrText(current) >= 0) wrap.setValue(current);
        else {
          if (sel.options[0] && sel.options[0].className === 'xe-placeholder') sel.selectedIndex = 0;
          else if (sel.options.length) sel.selectedIndex = 0;
        }
      }
      search.addEventListener('input', () => applyFilter(search.value));

      // Expose value handlers on wrapper
      wrap.getValue = () => {
        if (isMultiple) return Array.prototype.slice.call(sel.selectedOptions).map(o => o.value).filter(v => v !== '');
        const v = sel.value;
        const isPlaceholder = sel.selectedIndex === 0 && sel.options[0] && sel.options[0].className === 'xe-placeholder';
        return (isPlaceholder ? '' : v);
      };
      wrap.setValue = (v) => {
        if (isMultiple) {
          const want = Array.isArray(v) ? v.map(String) : [];
          for (const o of sel.options) o.selected = want.includes(String(o.value));
          return;
        }
        const s = String(v ?? '');
        let idx = -1;
        for (let i = 0; i < sel.options.length; i++) {
          const o = sel.options[i];
          if (o.disabled) continue;
          if (String(o.value) === s || String(o.text) === s) { idx = i; break; }
        }
        if (idx >= 0) sel.selectedIndex = idx;
        else {
          if (sel.options[0] && sel.options[0].className === 'xe-placeholder') sel.selectedIndex = 0;
          else if (sel.options.length) sel.selectedIndex = 0;
        }
      };

      wrap.classList.add('xe-input-group');
      return wrap;
    }
  };

  // ---------------------------
  // Read options from data-attrs
  // ---------------------------
  function getDataAttrOptions(el) {
    const map = {};
    const ds = el.dataset || {};
    if (ds.type) map.type = ds.type;
    if (ds.name) map.name = ds.name;
    if (ds.url) map.url = ds.url;
    if (ds.pk) map.pk = ds.pk;
    if (ds.title) map.title = ds.title;
    if (ds.value != null) map.value = parseMaybeJSON(ds.value);
    if (ds.source != null) map.source = parseMaybeJSON(ds.source);
    if (ds.placement) map.placement = ds.placement;
    if (ds.mode) map.mode = ds.mode;
    if (ds.disabled) map.disabled = ds.disabled === 'true' || ds.disabled === '1';
    if (ds.emptytext) map.emptytext = ds.emptytext;
    if (ds.inputclass) map.inputclass = ds.inputclass;
    if (ds.default != null) map.defaultValue = parseMaybeJSON(ds.default);
    if (ds.defaultValue != null) map.defaultValue = parseMaybeJSON(ds.defaultValue);
    if (ds.format) map.format = ds.format; // date display format
    return map;
  }

  // ---------------------------
  // Core class
  // ---------------------------
  const Registry = new WeakMap();

  class EditableInstance {
    constructor(el, options) {
      ensureStyles();
      this.el = el;
      this.id = uid('xe_inst');
      this.options = this._initOptions(options);
      this._state = {
        shown: false,
        disabled: !!this.options.disabled,
        value: this._inferInitialValue(),
        sourceCache: null,   // will be set for select/radio (AJAX or local)
        popup: null,
        inlineWrap: null,
        inputEl: null,
        btnSave: null,
        btnCancel: null,
        saving: false
      };
      this._unbinders = [];
      this._bind();
    }

    _initOptions(options) {
      const defaults = {
        type: 'text',
        name: this.el.getAttribute('name') || this.el.getAttribute('data-name'),
        url: this.el.getAttribute('data-url') || null,
        pk: this.el.getAttribute('data-pk') || null,
        title: this.el.getAttribute('data-title') || '',
        mode: 'popup',  // 'popup' | 'inline'
        placement: 'auto',
        emptytext: 'Empty',
        display: null,
        validate: null,
        params: null,
        ajaxOptions: { type: 'POST', dataType: 'json', contentType: 'application/x-www-form-urlencoded; charset=UTF-8', headers: {} },
        toggle: 'click',
        onblur: 'ignore',   // 'cancel' | 'submit' | 'ignore'
        inputclass: '',
        placeholder: '',
        value: undefined,
        defaultValue: undefined,
        source: undefined,  // array | object | url | function
        multiple: false,
        disabled: false,
        format: undefined
      };
      return deepMerge({}, defaults, getDataAttrOptions(this.el), options);
    }

    _inferInitialValue() {
      if (this.options.value !== undefined) return this.options.value;
      const dataValue = this.el.getAttribute('data-value');
      if (dataValue != null) return parseMaybeJSON(dataValue);
      const text = (this.el.textContent || '').trim();
      return text || '';
    }

    _bind() {
      this.el.classList.add('xe-editable');
      if (this.options.toggle === 'manual') return;
      const handler = (e) => { if (this._state.disabled) return; e.preventDefault(); this.show(); };
      this._unbinders.push(on(this.el, 'click', handler));
      this.el.setAttribute('tabindex', this.el.getAttribute('tabindex') || '0');
      this._unbinders.push(on(this.el, 'keydown', (e) => {
        if (this._state.disabled) return;
        if (e.key === 'Enter' || e.key === ' ') { e.preventDefault(); this.show(); }
      }));
    }

    destroy() {
      this.hide(true);
      for (const un of this._unbinders) un();
      this._unbinders = [];
      delete this.el.__xe_instance;
    }

    enable() { this._state.disabled = false; this.el.classList.remove('xe-disabled'); }
    disable() { this._state.disabled = true; this.el.classList.add('xe-disabled'); }
    toggleDisabled() { this._state.disabled ? this.enable() : this.disable(); }

    option(key, value) {
      if (value === undefined) return this.options[key];
      this.options[key] = value; return this;
    }

    // Public API
    show() { return this._showInternal(); }
    hide(cancel) { return this._hideInternal(cancel); }
    toggle() { return this._state.shown ? this.hide() : this.show(); }

    getValue(includeEmpty) {
      const v = this._state.value;
      if (includeEmpty) return { [this.options.name || 'value']: v };
      if (v === '' || v == null) return {};
      return { [this.options.name || 'value']: v };
    }

    setValue(v/*, convert*/) { this._state.value = v; this._renderDisplay(v); return this; }

    _applyInitialToInput(inputEl) {
      let v = this._state.value;
      const empty = v === '' || v === null || v === undefined || (Array.isArray(v) && v.length === 0);
      if (empty && this.options.defaultValue !== undefined) v = this.options.defaultValue;

      const isWrapper = inputEl && inputEl.classList && inputEl.classList.contains('xe-select-wrap');
      const target = isWrapper ? inputEl.querySelector('select') : inputEl;

      const isSelect = target && target.tagName === 'SELECT';
      if (isSelect) {
        const opts = target.options;
        const hasPlaceholderTop = opts[0] && opts[0].className === 'xe-placeholder';
        if (v === '' || v === null || v === undefined) {
          if (hasPlaceholderTop) target.selectedIndex = 0;
          else { for (let i = 0; i < opts.length; i++) { const o = opts[i]; if (!o.disabled) { target.selectedIndex = i; break; } } }
          return;
        }
        const s = String(v).toLowerCase();
        let idx = -1;
        for (let i = 0; i < opts.length; i++) {
          const o = opts[i]; if (o.disabled) continue;
          if (String(o.value).toLowerCase() === s || String(o.text).toLowerCase() === s) { idx = i; break; }
        }
        if (idx >= 0) target.selectedIndex = idx;
        else {
          if (hasPlaceholderTop) target.selectedIndex = 0;
          else { for (let i = 0; i < opts.length; i++) { const o = opts[i]; if (!o.disabled) { target.selectedIndex = i; break; } } }
        }
        return;
      }

      // Date inputs: normalize to control's expected format
      const t = (this.options.type || '').toLowerCase();
      if ((t === 'date' || t === 'datetime' || t === 'datetime-local') && v) {
        const fmt = this.options.format || (t === 'date' ? DEFAULT_DATE_FMT : DEFAULT_DT_FMT);
        let dt = null;
        if (/^\d{4}-\d{2}-\d{2}/.test(String(v))) dt = new Date(v);
        if (!dt || isNaN(dt)) dt = parseByTokens(String(v), fmt);
        if (dt && !isNaN(dt)) v = (t === 'date') ? formatByTokens(dt, DEFAULT_DATE_FMT) : formatByTokens(dt, DEFAULT_DT_FMT);
      }

      const set = inputEl.setValue ? (x => inputEl.setValue(x)) : (x => { if ('value' in inputEl) inputEl.value = x ?? ''; });
      set(v);
    }

    // Toggle saving state: disable Save & show spinner
    _setSaving(yes) {
      this._state.saving = !!yes;
      const btn = this._state.btnSave;
      if (!btn) return;
      btn.disabled = !!yes;
      const existingSpinner = btn.querySelector('.xe-spinner');
      if (existingSpinner) existingSpinner.remove();
      if (yes) {
        const spin = doc.createElement('span'); spin.className = 'xe-spinner';
        const inInline = !!this._state.inlineWrap;
        if (inInline) { btn.textContent = ''; btn.appendChild(spin); }
        else { btn.insertBefore(spin, btn.firstChild); }
      } else {
        const inInline = !!this._state.inlineWrap;
        if (inInline) { btn.textContent = ''; btn.appendChild(svgIcon('check')); }
      }
    }

    async _showInternal() {
      if (this._state.shown) return;
      this._state.shown = true;

      const t = (this.options.type || 'text').toLowerCase();
      const isChoice = (t === 'select' || t === 'radio');
      let effectiveOptions = { ...this.options };

      // ---- Source resolution & caching for display mapping ----
      if (isChoice) {
        if (isStr(this.options.source)) {
          const srcData = await this._loadSource(this.options.source);
          this._state.sourceCache = srcData;
          effectiveOptions = { ...effectiveOptions, source: srcData };
        } else if (isFn(this.options.source)) {
          const res = this.options.source.call(this.el);
          const srcData = isPromise(res) ? await res : res;
          this._state.sourceCache = srcData;
          effectiveOptions = { ...effectiveOptions, source: srcData };
        } else if (Array.isArray(this.options.source) || isObj(this.options.source)) {
          // **FIX**: cache local array/object sources so display uses text, not value
          this._state.sourceCache = this.options.source;
        }
      }
      // --------------------------------------------------------

      // INLINE MODE
      if ((this.options.mode || '').toLowerCase() === 'inline') {
        const inline = el('span', { class: 'xe-inline', role: 'group' });
        const inputEl = (TypeBuilders[t] || TypeBuilders.text)(this, effectiveOptions);
        inputEl.classList.add('xe-input');

        this._applyInitialToInput(inputEl);

        const btnSave = el('button', { class: 'xe-btn xe-btn-primary', type: 'button', 'aria-label': 'Save' });
        btnSave.appendChild(svgIcon('check'));
        const btnCancel = el('button', { class: 'xe-btn', type: 'button', 'aria-label': 'Cancel' });
        btnCancel.appendChild(svgIcon('x'));

        const actions = el('div', { class: 'xe-actions' });
        actions.appendChild(btnSave); actions.appendChild(btnCancel);

        const errRow = el('div', { class: 'xe-error-row' });
        const err = el('div', { class: 'xe-error' });
        errRow.appendChild(err);

        inline.appendChild(inputEl);
        inline.appendChild(actions);
        inline.appendChild(errRow);

        this._inlinePrev = { parent: this.el.parentNode, next: this.el.nextSibling };
        this.el.classList.add('xe-hidden');
        this.el.insertAdjacentElement('afterend', inline);

        this._state.inputEl = inputEl;
        this._state.inlineWrap = inline;
        this._state.btnSave = btnSave;
        this._state.btnCancel = btnCancel;

        const submit = () => this._submit();
        const cancel = () => this.hide(true);
        btnSave.addEventListener('click', submit);
        btnCancel.addEventListener('click', cancel);

        setTimeout(() => {
          const focusEl = inline.querySelector('.xe-select-search') || inputEl;
          (focusEl && focusEl.focus && focusEl.focus());
        }, 0);

        inline.addEventListener('keydown', (e) => {
          const tag = (e.target && e.target.tagName) || '';
          if (e.key === 'Escape') { e.preventDefault(); cancel(); }
          else if (e.key === 'Enter') {
            if (tag === 'TEXTAREA' && !e.ctrlKey && !e.metaKey) return;
            e.preventDefault(); submit();
          }
        });

        triggerDOM(this.el, 'shown', { instance: this });
        triggerJQ(this.el, 'shown', [this]);
        return;
      }

      // POPUP MODE
      const pop = el('div', { class: 'xe-popup', role: 'dialog', 'aria-modal': 'true' });
      pop.dataset.instance = this.id;

      if (this.options.title) {
        const head = el('div', { class: 'xe-header' });
        head.textContent = this.options.title;
        pop.appendChild(head);
      }

      const body = el('div', { class: 'xe-body' });
      const group = el('div', { class: 'xe-group' });

      const builder = TypeBuilders[t] || TypeBuilders.text;
      const inputEl = builder(this, effectiveOptions);
      inputEl.classList.add('xe-input');

      this._applyInitialToInput(inputEl);

      group.appendChild(inputEl);
      body.appendChild(group);
      pop.appendChild(body);

      const err = el('div', { class: 'xe-error' });
      pop.appendChild(err);

      const actions = el('div', { class: 'xe-actions' });
      const btnSave = el('button', { class: 'xe-btn xe-btn-primary', type: 'button' });
      btnSave.appendChild(svgIcon('check')); btnSave.append('Save');
      const btnCancel = el('button', { class: 'xe-btn', type: 'button' });
      btnCancel.appendChild(svgIcon('x')); btnCancel.append('Cancel');
      actions.appendChild(btnSave); actions.appendChild(btnCancel);
      pop.appendChild(actions);

      const arrow = el('div', { class: 'xe-arrow' });
      pop.appendChild(arrow);

      doc.body.appendChild(pop);
      this._state.popup = pop;
      this._state.inputEl = inputEl;
      this._state.btnSave = btnSave;
      this._state.btnCancel = btnCancel;

      this._reposition('auto');

      this._previousFocus = doc.activeElement;
      setTimeout(() => {
        const focusEl = pop.querySelector('.xe-select-search') || pop.querySelector('.xe-input, input, textarea, select, button');
        if (focusEl && focusEl.focus) focusEl.focus();
      }, 0);

      const closeOnOutside = (e) => {
        if (!this._state.shown) return;
        if (!pop.contains(e.target) && e.target !== this.el) {
          if (this.options.onblur === 'cancel') this.hide(true);
          else if (this.options.onblur === 'submit') this._submit();
          else this.hide(true);
        }
      };
      const onEsc = (e) => {
        if (e.key === 'Escape') { e.preventDefault(); this.hide(true); }
        else if ((e.metaKey || e.ctrlKey) && e.key.toLowerCase() === 'enter') { e.preventDefault(); this._submit(); }
      };
      const onResize = () => this._reposition(this.options.placement || 'auto');
      const onScroll = () => this._reposition(this.options.placement || 'auto');

      this._unbindPopup = [
        on(window, 'mousedown', closeOnOutside, true),
        on(window, 'keydown', onEsc, true),
        on(window, 'resize', onResize),
        on(window, 'scroll', onScroll)
      ];

      btnCancel.addEventListener('click', () => this.hide(true));
      btnSave.addEventListener('click', () => this._submit());
      pop.addEventListener('keydown', (e) => {
        const tag = (e.target && e.target.tagName) || '';
        if (e.key === 'Enter') {
          if (tag === 'TEXTAREA' && !e.ctrlKey && !e.metaKey) return;
          e.preventDefault(); this._submit();
        }
      });

      triggerDOM(this.el, 'shown', { instance: this });
      triggerJQ(this.el, 'shown', [this]);
    }

    _hideInternal(cancel) {
      if (!this._state.shown) return;
      this._state.shown = false;

      if (this._state.inlineWrap) {
        const wrap = this._state.inlineWrap;
        if (wrap.parentNode) wrap.parentNode.removeChild(wrap);
        this._state.inlineWrap = null;
        this._state.inputEl = null;
        this._state.btnSave = null;
        this._state.btnCancel = null;
        this.el.classList.remove('xe-hidden');
        if (this._previousFocus && this._previousFocus.focus) { try { this._previousFocus.focus(); } catch (_) {} }
        triggerDOM(this.el, 'hidden', { instance: this, cancelled: !!cancel });
        triggerJQ(this.el, 'hidden', [this, { cancelled: !!cancel }]);
        return;
      }

      if (this._unbindPopup) { for (const un of this._unbindPopup) try { un(); } catch (_) {} this._unbindPopup = null; }
      const pop = this._state.popup;
      if (pop && pop.parentNode) pop.parentNode.removeChild(pop);
      this._state.popup = null;
      this._state.inputEl = null;
      this._state.btnSave = null;
      this._state.btnCancel = null;

      if (this._previousFocus && this._previousFocus.focus) { try { this._previousFocus.focus(); } catch (_) {} }
      triggerDOM(this.el, 'hidden', { instance: this, cancelled: !!cancel });
      triggerJQ(this.el, 'hidden', [this, { cancelled: !!cancel }]);
    }

    async _submit() {
      const input = this._state.inputEl;
      if (!input || this._state.saving) return;

      const getVal = () => (input.getValue ? input.getValue() : ('value' in input ? input.value : ''));
      let newValue = getVal();

      if (isFn(this.options.validate)) {
        const msg = this.options.validate.call(this.el, newValue);
        if (msg) { this._showError(String(msg)); return; }
      }
      this._showError('');

      const submitData = { name: this.options.name || 'value', value: newValue, pk: this.options.pk };

      let payload = submitData;
      if (this.options.params) {
        if (isFn(this.options.params)) payload = this.options.params.call(this.el, submitData);
        else if (isObj(this.options.params)) payload = deepMerge({}, submitData, this.options.params);
      }

      const doDisplayUpdate = (serverResp) => {
        if (isFn(this.options.display)) {
          try { this.options.display.call(this.el, newValue, this._state.sourceCache); } catch (_) {}
        } else {
          this._renderDisplay(newValue);
        }
        this._state.value = newValue;
        this.el.classList.add('xe-highlight'); setTimeout(() => this.el.classList.remove('xe-highlight'), 800);
        const params = { newValue, submitted: payload, response: serverResp };
        triggerDOM(this.el, 'save', params); triggerJQ(this.el, 'save', [params]);
      };

      if (!this.options.url) { doDisplayUpdate(null); this.hide(false); return; }

      this._setSaving(true);

      const ao = this.options.ajaxOptions || {};
      const method = (ao.type || 'POST').toUpperCase();
      const dataType = (ao.dataType || 'json').toLowerCase();
      const contentType = ao.contentType || 'application/x-www-form-urlencoded; charset=UTF-8';
      const headers = ao.headers || {};

      let body;
      if (contentType.indexOf('application/json') >= 0) body = JSON.stringify(payload);
      else {
        const usp = new URLSearchParams();
        for (const [k,v] of Object.entries(payload)) {
          if (Array.isArray(v)) v.forEach(it => usp.append(k, it)); else usp.append(k, v == null ? '' : String(v));
        }
        body = usp.toString();
      }

      try {
        const url = isFn(this.options.url) ? this.options.url.call(this.el) : this.options.url;
        const res = await fetch(url, { method, headers: { 'Content-Type': contentType, ...headers }, body });
        let data = null;
        if (dataType === 'json') data = await res.json().catch(() => null); else data = await res.text();

        if (isFn(this.options.success)) {
          const maybeMsg = this.options.success.call(this.el, data, newValue);
          if (isStr(maybeMsg) && maybeMsg) { this._showError(maybeMsg); this._setSaving(false); return; }
        }
        if (!res.ok) {
          const message = (data && (data.message || data.error)) || ('HTTP ' + res.status);
          this._showError(message); if (isFn(this.options.error)) this.options.error.call(this.el, res, message);
          this._setSaving(false); return;
        }

        doDisplayUpdate(data);
        this._setSaving(false);
        this.hide(false);
      } catch (err) {
        const msg = (err && err.message) ? err.message : 'Network error';
        this._showError(msg); if (isFn(this.options.error)) this.options.error.call(this.el, null, msg);
        this._setSaving(false);
      }
    }

    _showError(text) {
      if (this._state.inlineWrap) {
        const row = this._state.inlineWrap.querySelector('.xe-error-row');
        const e = row && row.querySelector('.xe-error');
        if (e) e.textContent = text || '';
        if (row) { if (text && String(text).trim()) row.classList.add('xe-visible'); else row.classList.remove('xe-visible'); }
        return;
      }
      const pop = this._state.popup; const e = pop && pop.querySelector('.xe-error'); if (e) e.textContent = text || '';
    }

    _formatDisplay(value) {
      const t = (this.options.type || 'text').toLowerCase();
      if (t === 'date' || t === 'datetime' || t === 'datetime-local') {
        const fmt = this.options.format || (t === 'date' ? DEFAULT_DATE_FMT : DEFAULT_DT_FMT);
        if (!value) return this.options.emptytext;
        let dt = null;
        if (/^\d{4}-\d{2}-\d{2}/.test(String(value))) dt = new Date(value);
        if (!dt || isNaN(dt)) dt = parseByTokens(String(value), fmt) || parseByTokens(String(value), DEFAULT_DATE_FMT);
        if (!dt || isNaN(dt)) return String(value);
        return formatByTokens(dt, fmt);
      }
      if (t === 'select' && this._state.sourceCache) {
        const src = normalizeSource(this._state.sourceCache);
        const map = new Map(src.map(i => [String(i.value), i.text]));
        if (Array.isArray(value)) return value.map(v => map.get(String(v)) ?? v).join(', ');
        return map.get(String(value)) ?? value;
      }
      if (t === 'checkbox') return value ? 'Yes' : 'No';
      return (value == null || value === '') ? this.options.emptytext : String(value);
    }

    _renderDisplay(value) {
      if (value == null || value === '') {
        this.el.classList.add('xe-muted');
        this.el.textContent = this.options.emptytext;
      } else {
        this.el.classList.remove('xe-muted');
        this.el.textContent = this._formatDisplay(value);
      }
    }

    async _loadSource(source) {
      const url = String(source);
      const res = await fetch(url, { method: 'GET', headers: { 'Accept': 'application/json,text/plain,*/*' }});
      const text = await res.text();
      return parseMaybeJSON(text);
    }

    _reposition(preferred) {
      const pop = this._state.popup;
      if (!pop) return;

      pop.style.visibility = 'hidden';
      pop.style.left = '-9999px';
      pop.style.top = '-9999px';
      pop.style.maxWidth = 'min(92vw, 480px)';

      const rect = this.el.getBoundingClientRect();
      const pRect = pop.getBoundingClientRect();
      const vw = window.innerWidth;
      const vh = window.innerHeight;

      const spaces = { top: rect.top, bottom: vh - rect.bottom, left: rect.left, right: vw - rect.right };
      let side = 'bottom';
      const pref = (this.options.placement || preferred || 'auto').toLowerCase();

      function chooseAuto(bias) {
        const order = bias ? [bias, 'bottom', 'top', 'right', 'left'] : ['bottom', 'top', 'right', 'left'];
        for (const s of order) {
          if ((s === 'bottom' || s === 'top') && spaces[s] >= pRect.height + 12) return s;
          if ((s === 'left' || s === 'right') && spaces[s] >= pRect.width + 12) return s;
        }
        return Object.entries(spaces).sort((a,b)=>b[1]-a[1])[0][0];
      }

      if (pref.startsWith('auto')) {
        const bias = pref.split(' ')[1];
        side = chooseAuto(bias);
      } else side = pref;

      let top = 0, left = 0, gap = 8;
      if (side === 'bottom') { top = rect.bottom + gap; left = rect.left + (rect.width/2) - (pRect.width/2); }
      else if (side === 'top') { top = rect.top - pRect.height - gap; left = rect.left + (rect.width/2) - (pRect.width/2); }
      else if (side === 'left') { top = rect.top + (rect.height/2) - (pRect.height/2); left = rect.left - pRect.width - gap; }
      else if (side === 'right') { top = rect.top + (rect.height/2) - (pRect.height/2); left = rect.right + gap; }

      const minPad = 8;
      left = clamp(left, minPad, vw - pRect.width - minPad);
      top  = clamp(top,  minPad, vh - pRect.height - minPad);

      pop.style.visibility = 'visible';
      pop.style.left = `${Math.round(left)}px`;
      pop.style.top = `${Math.round(top)}px`;

      const arrow = pop.querySelector('.xe-arrow');
      if (arrow) {
        arrow.dataset.side = side;
        const anchorCx = rect.left + rect.width / 2;
        const anchorCy = rect.top + rect.height / 2;
        const localX = clamp(anchorCx - left, 12, pRect.width - 12);
        const localY = clamp(anchorCy - top, 12, pRect.height - 12);

        if (side === 'bottom' || side === 'top') { arrow.style.left = `${Math.round(localX - 6)}px`; arrow.style.top = ''; arrow.style.right = ''; }
        else { arrow.style.top = `${Math.round(localY - 6)}px`; arrow.style.left = ''; arrow.style.right = ''; }
      }
    }
  }

  // ---------------------------
  // Public API (vanilla)
  // ---------------------------
  function getInstance(el, options) {
    let inst = Registry.get(el);
    if (!inst) {
      el.classList.add('xe-editable');
      inst = new EditableInstance(el, options || {});
      Registry.set(el, inst);
      el.__xe_instance = inst;
      inst._renderDisplay(inst._state.value);
    }
    return inst;
  }

  function init(el, options) { return getInstance(el, options); }
  function initAll(selector = '[data-type],[data-toggle="editable"]', options) {
    const nodes = typeof selector === 'string' ? doc.querySelectorAll(selector) : (selector || []);
    toArray(nodes).forEach(n => getInstance(n, options));
  }

  // ---------------------------
  // jQuery bridge
  // ---------------------------
  if (window.jQuery) {
    const $ = window.jQuery;
    $.fn.editable = function (arg, ...args) {
      if (typeof arg !== 'string') {
        this.each(function () { getInstance(this, arg || {}); });
        return this;
      }
      const method = arg;
      if (method === 'getValue') {
        const includeEmpty = args[0];
        const result = {};
        this.each(function () {
          const inst = getInstance(this);
          Object.assign(result, inst.getValue(includeEmpty));
        });
        return result;
      }
      if (method === 'option') {
        if (args.length === 1) {
          const inst = getInstance(this.get(0));
          return inst.option(args[0]);
        } else {
          const [k, v] = args;
          this.each(function () { getInstance(this).option(k, v); });
          return this;
        }
      }
      this.each(function () {
        const inst = getInstance(this);
        if (typeof inst[method] === 'function') inst[method](...args);
      });
      return this;
    };
  }

  // Expose vanilla API
  return { init, initAll, getInstance };
});
