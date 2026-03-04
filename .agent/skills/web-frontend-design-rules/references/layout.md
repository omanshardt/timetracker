# Responsive Layout & Typography Rules

## 1. Responsive Design
- **Mobile First:** Start with mobile styles and add media queries for larger screens.
- **Breakpoints:** Use logical breakpoints (e.g., `640px` (SM), `768px` (MD), `1024px` (LG), `1280px` (XL)). Avoid device-specific breakpoints.
- **Fluid Layouts:** Use `max-width` (e.g., `max-w-7xl`) and auto-margins for centering containers.
- **Grid & Flexbox:** Use `gap` for spacing instead of `margin` on children to ensure consistent gutters.

## 2. Typography
- **Scalability:** Use `rem` or `em` instead of `px` for font sizes.
- **Hierarchy:**
  - `H1`: 2.25rem (36px) - Single use per page.
  - `H2`: 1.875rem (30px) - Main sections.
  - `H3`: 1.5rem (24px) - Sub-sections.
  - `Body`: 1rem (16px) - Base text.
- **Line Height:** Aim for `1.5` for body text and `1.2` for headings.
- **Max Line Length:** Keep text between 45–75 characters per line (approx. `max-w-prose` or `65ch`).

## 3. Spacing (The 8px Rule)
- Use multiples of 8 (e.g., 8px, 16px, 24px, 32px) for all margins, paddings, and gaps.
