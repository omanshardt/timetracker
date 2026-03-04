# Web Accessibility (A11y) Rules

## 1. Perceivable
- **Contrast:** Ensure text has at least 4.5:1 contrast (3:1 for large text).
- **Text Alternatives:** Every `<img>` must have an `alt` attribute. Use `alt=""` for decorative images.
- **Form Labels:** Every `<input>` must have a programmatic label (using `<label for="...">` or `aria-label`).

## 2. Operable
- **Keyboard Navigation:** All interactive elements must be reachable via `Tab` and show a clear `:focus` state.
- **Interactive Targets:** Buttons and links must be at least 44x44px for touch accessibility.

## 3. Understandable
- **Consistent Layout:** Keep navigation and interface elements in consistent locations.
- **Error Feedback:** Provide clear, text-based error messages for form validation errors.

## 4. Robust
- **Semantic HTML:** Use `<button>` for actions and `<a>` for navigation. Avoid `div` or `span` for interactive elements without proper ARIA roles.
