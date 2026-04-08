## Summary

- Describe what this PR changes and why.
- Link the related Linear/GitHub issue.

## Validation

- [ ] I ran local checks relevant to this change (tests, lint, smoke test).
- [ ] I verified there are no obvious regressions in wp-admin and frontend behavior.

## WordPress Release Checklist (only for release/version PRs)

- [ ] `readme.txt` updated:
  - [ ] `Stable tag` matches release version.
  - [ ] `== Changelog ==` includes the new version section.
  - [ ] `== Upgrade Notice ==` includes the new version note (if needed).
- [ ] Plugin version bumped in `clevers-product-carousel.php`.
- [ ] Release artifacts/notes reviewed (tag, changelog, package expectations).
- [ ] PHP compatibility matrix workflow passed for supported versions (7.4 → 8.4).
- [ ] WordPress compatibility headers reviewed (`Requires at least`, `Tested up to`, `Requires PHP`) where applicable.

## Deployment Notes

- Anything reviewers or release managers must know before merging/releasing.
