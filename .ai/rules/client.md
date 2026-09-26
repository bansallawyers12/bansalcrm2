---
paths:
  - 'app/Http/Controllers/Admin/{Client/ClientController.php,LeadController.php,ReportController.php}'
---

# Client

## Phase 3 list pages keep UI contract
List pages must keep paginate() (not simplePaginate), keep the Applications withCount/loadCount, and keep type ILIKE matching. Drop only the extra $query->count() and use $lists->total(). Visa expiry calendar must refetch events per visible month so prev/next still works; do not dump every visa row into the HTML.
