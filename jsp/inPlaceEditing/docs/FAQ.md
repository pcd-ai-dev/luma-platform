# FAQ

**Does it require jQuery?**  
No. It works vanilla. If jQuery is present, the same `.editable()` API is enabled.

**How do I make selects searchable?**  
It’s automatic for `data-type="select"`; a search box appears on open.

**The select shows the numeric value (e.g., 2) instead of text.**  
Fixed in this fork. Local/remote `source` is cached and used to map `value -> text` for display.

**How do I format dates?**  
Add `data-format="DD/MM/YYYY"` (or any combo of `YYYY, MM, DD, HH, mm`). Default is `YYYY-MM-DD`. Server submission stays ISO.

**Can I add custom input types?**  
Yes—register another builder into the internal `TypeBuilders` map (see code comments).
