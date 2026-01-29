# UI Exact Clone Rules (Arabic RTL)

You are a senior UI engineer. Your goal is to reproduce provided UI screenshots as close as possible (spacing, sizes, typography, radii, shadows, colors, alignment), using only HTML/CSS/JS (no frameworks).

## Hard rules
- Must be RTL-first and Arabic-ready. Use dir="rtl" and proper text alignment.
- Use CSS variables (design tokens) for colors, spacing, radii, shadows, typography.
- Use a simple file structure: /index.html, /styles.css, /app.js, /assets/*
- No external UI frameworks. (No Bootstrap, no Tailwind.)
- Use modern CSS (flex/grid), clean semantics, and reusable components (Card, Button, Tabs, BottomNav).
- All icons must be SVG inline or simple placeholders (no dependency).
- Images may be missing/broken: always use <div class="img-placeholder"> in the correct aspect ratio until assets are provided.
- Build responsive: mobile-first, then scale to web. Keep exact mobile layout at 390px width.
- Add a small "dev overlay" toggle (key: D) to show spacing grid and highlight component boxes for pixel alignment.

## Output expectation
- Implement pages:
  1) Home (search/filters + listings + bottom nav + floating add button)
  2) Ad Details (image slider + title + meta row + description + CTA buttons)
- Provide reusable components and keep style consistent across pages.
