# Nexus — Fashion Marketplace

A multi-vendor e-commerce marketplace built from scratch in vanilla PHP — no framework, no ORM, custom MVC-style architecture. Built as a hands-on exercise to prove I can design and ship a real, end-to-end web application: authentication, a multi-role marketplace (customer / vendor / admin), an admin-approval pipeline, and the full shopping loop from browsing to checkout.

**Read this before you read the code:** this is a portfolio project, not a production system. See [What this is not](#what-this-is-not) below — it's not an afterthought, it's the point.

## What it does

**Storefront**
- Browse products, search, add to cart, apply coupons, checkout
- Register with email verification, log in, reset a forgotten password, edit profile (info + password)
- In-app notifications (order updates, vendor application decisions)
- Apply to become a vendor

**Vendor**
- Full product CRUD with a multi-step wizard (pricing, stock, discounts, image gallery)
- Manage your own product catalog and orders

**Admin**
- Review and approve/reject vendor applications
- Manage users, categories, products (cross-vendor), coupons, reviews, and orders
- Role-based access control throughout — vendors and admins can only edit what they own; admins get an explicit override on moderation actions (e.g. deleting any product), never on editing someone else's listing

## What this is *not*

I'd rather you find out from me than from the code. This was built solo, as a learning project, over a bounded amount of time — it is:

- **Not load-tested.** No idea how it behaves under concurrent traffic. No caching layer, no query optimization pass, no benchmarking of any kind.
- **Not test-covered.** There is no automated test suite — no unit tests, no integration tests. Everything was verified by hand, manually, click by click, across every role.
- **Not feature-complete.** Review management has no search/filter UI yet. A few other admin conveniences are thinner than a real product would need.
- **Not security-audited by anyone but me.** CSRF protection, input validation, and ownership checks are in place and I believe they're correct, but this hasn't been reviewed by anyone else or run through a real security tool. Treat it as "reasonably careful," not "audited."
- **Not using a framework, ORM, or dependency manager.** Every piece — routing, sessions, validation, the query layer — is hand-rolled. That was the point (to prove I understand what frameworks usually do for you), but it also means none of the battle-testing a framework gives you for free.
- **Missing production infrastructure.** No environment-based config (`.env`), no queue for emails (they send synchronously, inline, on the request), no rate limiting, no logging/monitoring beyond basic error logs.
- **A few rough edges left on purpose.** Some dead code paths and leftover template scaffolding from the starting theme still exist in places; not everything has been polished to the same level.

None of this is a confession — it's scope. I know what production-grade would require, and building all of that wasn't the goal here. The goal was to prove I can architect and ship a genuinely full-featured system by hand, correctly, and know exactly where its edges are.

## What this *is* proof of

- Designing a multi-role permission model where "role" and "ownership" are two different, correctly-enforced axes
- Building auth from scratch: sessions, CSRF, bcrypt hashing, email verification and password reset via selector/token pairs (the same pattern behind Laravel's "remember token" design), not copy-pasted from a tutorial
- A consistent layered architecture (Repository → Service → Validator) applied the same way across ~15 independent domains, not just the one or two that got the most attention
- Debugging and fixing real, self-found bugs along the way — not just "it works," but catching and correcting actual mistakes (a couple of which are documented in commit history rather than hidden)

## Stack

- **Backend:** PHP 8, vanilla — no framework. Custom autoloader, a small `Repository` base class over PDO, service-layer classes per domain, server-side validation with a rule-based `Validator`
- **Frontend:** Server-rendered PHP views + vanilla JS (ES modules) for interactive pieces — the admin panel uses a small hand-built class architecture (`Template` / `Renderer` / `Ajax` helpers) instead of a JS framework
- **Database:** MySQL/MariaDB
- **Auth:** Session-based, CSRF protection on all state-changing requests, bcrypt password hashing, selector/token email verification & password reset
- **Email:** SMTP via PHP's `mail()` — configurable for any SMTP provider

## Architecture notes

Each domain (`Users`, `Products`, `Orders`, `VendorRequests`, etc.) follows the same layering: a `Repository` for data access, a `Service` for business logic, and a `Validator` for input rules.

A few specific decisions worth calling out:
- **Ownership vs. role** — most permission checks are ownership-based (`vendor_id === session user`), not just role-based, so an admin can't casually edit another vendor's listing. Deletion is the one place admins get a moderation override, and that split is enforced in code, not just convention.
- **CSRF** verified server-side on every mutating endpoint.
- **Vendor onboarding is admin-approved**, not instant self-serve — closer to how real marketplaces (Etsy, Amazon Seller Central) gate seller accounts than a toy "flip a role" implementation would be.

## Local setup

1. Clone the repo into your local server root (developed against XAMPP on Windows)
2. Create a MySQL database named `nexus`
3. Import the schema: `mysql -u root -p nexus < database/schema.sql`
4. Set your DB credentials in `config/database.php`
5. Configure SMTP in `config/app.php` for email verification / password reset to work
6. Point your web server at `public/`