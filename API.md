# API v1

Base URL: `/api/v1`

All endpoints return JSON and are limited by Laravel's API rate limiter.

## Read endpoints

- `GET /feed?limit=10&page=1` — paginated posts with author and reactions. `limit` accepts 1–50.
- `GET /posts/{id}` — one post with author, comments, and reactions.
- `GET /users?q=search` — search public user profiles by first or last name.
- `GET /users/{id}` — one public profile. Email, password, and remember token are excluded.

Unknown resources return standard HTTP `404` JSON responses. Validation errors return `422`.

Write endpoints require the Bearer token described below.

## Authentication

`POST /login` accepts `email`, `password`, and optional `device_name`. The plain token is returned once and only its SHA-256 hash is stored. Tokens expire after 30 days.

Send protected requests with `Authorization: Bearer <token>`.

- `GET /me` — current authenticated user.
- `POST /posts` — create a text post using `post_text`.
- `POST /logout` — revoke the current token.

Authentication failures return `401`; invalid credentials or input return `422`.
