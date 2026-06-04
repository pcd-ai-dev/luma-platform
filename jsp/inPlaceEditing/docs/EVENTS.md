
---

# docs/EVENTS.md

```markdown
# Events

| Name   | When                         | Detail payload                                  |
|--------|------------------------------|--------------------------------------------------|
| shown  | Editor opened                | `{ instance }`                                  |
| hidden | Editor closed                | `{ instance, cancelled: boolean }`              |
| save   | After successful submission  | `{ newValue, submitted, response }`             |

jQuery:
```js
$('.editable').on('save', (e, detail) => console.log(detail));
