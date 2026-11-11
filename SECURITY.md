# Security Policy

## Supported versions

Only the latest released version receives security fixes.

## Reporting a vulnerability

If you discover a security vulnerability, please **do not open a public issue**.

Email **sultanbayezidai@gmail.com** with:

1. A description of the vulnerability
2. Steps to reproduce or a proof of concept
3. Affected endpoints or components
4. Any suggested remediation

You will receive an acknowledgement within 72 hours. Please allow reasonable
time for a fix before public disclosure. Coordinated disclosure is appreciated.

## Security measures in this project

- Session-based web auth with CSRF protection (Laravel Breeze)
- Token-based API auth (Laravel Sanctum); tokens are revocable
- Role-based access control on every route group + model policies
- Form request validation on all writes; mass-assignment protection via `$fillable`
- Authorization checks scope students/parents to their own records
- No raw user input in shell commands or SQL
- Rate limiting on authentication routes
- Secrets only via environment configuration — never committed

## Scope notes

Document processing features treat all uploads as untrusted input. This
project currently handles no file uploads beyond configuration.
