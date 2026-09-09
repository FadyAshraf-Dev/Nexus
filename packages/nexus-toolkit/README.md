# @fadyashraf-dev/nexus-toolkit

Small, framework-agnostic front-end utilities extracted from the Nexus marketplace admin panel. Pure ES modules, no dependencies.

## Install

```bash
npm install @fadyashraf-dev/nexus-toolkit
```

## Usage

```js
import { Ajax, Dom, Money, Navigation, Renderer, Template } from "@fadyashraf-dev/nexus-toolkit";

// Ajax reads a CSRF token from a meta tag by default (meta[name="csrf-token"]).
// Point it elsewhere if your app uses a different convention:
Ajax.csrfSelector = 'meta[name="my-csrf-token"]';

const data = await Ajax.get("/api/products");
```

## Modules

- **Ajax** — fetch wrapper with timeout, CSRF header injection, and JSON/FormData handling.
- **Dom** — lightweight element lookup/caching helper.
- **Money** — number-to-currency formatting.
- **Navigation** — thin wrapper around `window.location`.
- **Renderer** — DOM content swap helpers (replace/append/prepend/loading/empty/error states).
- **Template** — abstract base class for building small HTML string templates (escape, badge, button, image, truncate).

## Releasing

Bump the version in `package.json`, then push a tag matching `toolkit-vX.Y.Z` (e.g. `toolkit-v1.0.1`). The `npm-publish.yml` GitHub Actions workflow verifies the tag matches the package version and publishes to npm automatically.
