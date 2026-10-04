---
name: vibefarsi
description: >-
  Initialize and manage Persian RTL UI components, typography (Vazirmatn / IRANSans),
  Graphite design tokens, and Jalali date utilities using the VibeFarsi CLI. Use when
  setting up or adding Persian/Farsi RTL components, calendar, or Persian typography in
  React and Tailwind CSS projects.
---

# VibeFarsi Workflow Guide

VibeFarsi (`vibefarsi`) is a CLI and component registry for Persian RTL components, fonts, and Graphite design tokens built for React and Tailwind CSS.

## 1. Project Initialization

To initialize RTL direction, Persian font configuration, Graphite tokens, and essential utility libraries (`lib/utils.ts`, `lib/jalali.ts`):

```bash
npx vibefarsi@latest init
```

### CLI Options & Flags:
- `--font <vazirmatn|iransans>`: Select font family (e.g., `--font vazirmatn` or `--font iransans`).
- `--theme <slug>`: Design tokens to apply on init (default: `graphite`).
- `--cwd <dir>`: Target project directory (default: `.`).
- `--registry <url>`: Custom registry root (default: `https://vibefarsi.ir/r`).
- `--overwrite`: Replace existing files.
- `--no-install`: Skip automatic dependency installation (`npm install`).
- `-y`, `--yes`: Non-interactive mode (default).
- `--dry-run`: Preview file modifications without writing to disk.

### Examples:
```bash
# Non-interactive init with Vazirmatn font
npx vibefarsi@latest init --font vazirmatn -y

# Dry run to inspect changes
npx vibefarsi@latest init --dry-run
```

---

## 2. Adding Components & Registry Modules

Pull components directly from the VibeFarsi registry into your project:

```bash
# Add a single component
npx vibefarsi add button

# Add multiple components (e.g. calendar pulls lib/jalali.ts automatically)
npx vibefarsi add button calendar price
```

---

## 3. Discovering Available Registry Items

Query and inspect available items in the registry:

```bash
# List all available registry slugs
npx vibefarsi list

# List specific categories
npx vibefarsi list components
npx vibefarsi list animations
npx vibefarsi list backgrounds
npx vibefarsi list templates
npx vibefarsi list themes
npx vibefarsi list lib
```

---

## 4. Key Core Features & Integration Checklist

1. **RTL Direction Standard**: Ensures `dir="rtl"` is configured on the root HTML element or layout wrapper.
2. **Persian Typography**: Injects and configures Persian web fonts (such as `Vazirmatn` or `IRANSans`) with appropriate Tailwind font family utilities.
3. **Graphite Design Tokens**: Standardizes design tokens for Persian UI aesthetics and dark/light modes.
4. **Jalali Date Handling**: Generates `lib/jalali.ts` for Persian solar date parsing, conversions, and calendar formatting.
5. **Utility Helpers**: Generates `lib/utils.ts` (e.g. `clsx` and `tailwind-merge` class concatenators).
