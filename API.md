# API v1

Base URL: `/api/v1`

All endpoints return JSON and are limited by Laravel's API rate limiter.

## Read endpoints

- `GET /feed?limit=10&page=1` — paginated posts with public author fields and reactions. `limit` accepts 1–50.
- `GET /posts/{id}` — one visible post with public author fields, up to 100 latest comments, and reactions.
- `GET /users?q=search` — search active public user profiles by first or last name.
- `GET /users/{id}` — one active public profile. Only the profile name, bio, avatar/cover URLs, ID, and join date are returned; account credentials, status, admin flags, and private preferences are not exposed.

Unknown resources return standard HTTP `404` JSON responses. Validation errors return `422`.

Write endpoints require the Bearer token described below.

## Authentication

`POST /login` accepts `email`, `password`, and optional `device_name`. It is limited to five attempts per minute per email and IP. Suspended or unverified accounts cannot receive API tokens. The plain token is returned once and only its SHA-256 hash is stored. Tokens expire after 30 days; protected requests re-check account status and revoke tokens for suspended accounts.

Send protected requests with `Authorization: Bearer <token>`.

- `GET /me` — current authenticated user.
- `POST /posts` — create a text post using `post_text`.
- `POST /logout` — revoke the current token.

Authentication failures return `401`; suspended/unverified accounts return `403`; invalid credentials or input return `422`; rate limits return `429`.
