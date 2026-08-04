# Admissions frontend UAT checklist

| Test case | User | Expected | Actual | Status | Evidence | Remarks |
|---|---|---|---|---|---|---|
| Login | | Correct tenant-bound authentication | | Not run | | |
| Context | | Correct membership, institute, portal and year | | Not run | | |
| Profile | | Correct Person and profile | | Not run | | |
| Menus | | Only effective menu actions | | Not run | | |
| Direct URL | | Forbidden/hidden action denied | | Not run | | |
| Tenant attack | | Foreign UUID returns 403/404, no disclosure | | Not run | | |
| Logout | | Session invalidated | | Not run | | |

For every persona also record employment/responsibility, records visible, actions allowed/hidden, file protection, workflow/audit/notification evidence, timestamp, commit and retest result. Screenshots supplement but never replace automated security proof.
