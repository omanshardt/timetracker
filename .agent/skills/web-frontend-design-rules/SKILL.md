---
name: web-frontend-design-rules
description: Essential design principles for accessible, responsive, and visually consistent web frontends. Use when auditing UI code, creating new components, or establishing project-wide design systems for React, Angular, or Vanilla JS.
---

# Web Frontend Design Rules

This skill provides a comprehensive framework for creating high-quality web interfaces that prioritize accessibility (A11y), responsive layouts, and typographic clarity.

## Core Rules & Workflows

### 1. Accessibility (A11y) First
Never treat accessibility as an afterthought. Follow the guidelines in [accessibility.md](references/accessibility.md) for contrast, semantic HTML, and keyboard navigation.

### 2. Layout & Typography
Use a consistent spacing and typography scale to create visual harmony. 
- Refer to [layout.md](references/layout.md) for breakpoints and the 8px spacing rule.
- Apply the fluid typography scale and line-height rules to ensure readability.

### 3. Design Tokens (Baseline)
For new projects, use the [design-tokens.css](assets/design-tokens.css) file as a baseline for CSS variables. These tokens implement the 8px spacing and typography scales mentioned in the rules.

## How to Use
- **Audit UI Code:** Check if interactive elements have proper labels and `:focus` states.
- **Responsive Review:** Ensure mobile-first designs with logical breakpoints.
- **Hierarchy Check:** Verify that `H1`–`H4` tags are used logically and consistently.
