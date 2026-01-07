# Layout Change Plan

## Goal:
Make Avatar and Third Section same height (side by side in top row)
Personal Details below Avatar (bottom row)

## Current Working Structure:
```
client-detail-container (column)
  ├── left_section (Avatar) - 280px
  └── bottom-row-container (row)
      ├── personal-details-container (Personal Details) - 280px
      └── right_section (Third/Tabs) - flex: 1
```

## Desired Structure:
```
client-detail-container (column)
  ├── top-row-container (row)
  │   ├── left_section (Avatar) - 280px
  │   └── right_section (Third/Tabs) - flex: 1
  └── personal-details-container (Personal Details) - 280px
```

## Changes Required:

### CSS:
1. Rename `.bottom-row-container` → `.top-row-container`
2. Change `align-items: flex-start` → `align-items: stretch`

### HTML:
1. Wrap `.left_section` (Avatar) in `.top-row-container`
2. Move `.right_section` (Tabs) inside `.top-row-container` (after Avatar)
3. Move `.personal-details-container` outside, below `.top-row-container`


