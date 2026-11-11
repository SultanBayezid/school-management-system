# Changelog

All notable changes to this project are documented in this file.
The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.1.0/).

## [1.0.0] - 2026-11-11

### Added

- Student management: admissions, guardian details, search, profiles.
- Teacher management with optional login accounts and subject assignments.
- Classes, sections, subjects, class-subject-teacher mapping.
- Academic years with current-year tracking.
- Enrollments (one per student per year) with roll numbers and status.
- Attendance: daily class/section marking, upsert-safe saves, monthly report.
- Exams: per-class exams, grid marks entry, publish/draft workflow.
- Report cards: totals, percentage, GPA, letter grades, class position, print view.
- Configurable grade bands (Bangladeshi A+ to F scale seeded by default).
- Fees: fee types, single + bulk monthly invoice generation, discounts, fines.
- Payments with receipts and automatic unpaid/partial/paid status transitions.
- Library: catalogue, copy tracking, issue/return, overdue detection, fines.
- Timetable with class-slot and teacher-conflict detection.
- Notices with audience targeting and scheduled publishing.
- Role-based access control (admin, teacher, student, parent) with policies.
- REST API v1 (Sanctum token auth) for all read models + attendance writes.
- Dashboard with live statistics per role.
- Docker Compose stack (Nginx, PHP-FPM, PostgreSQL, Redis, queue worker).
- Demo seeder: 5 classes, 120 students, attendance, exams, marks, fees, library.
- 115 automated tests (257 assertions).
- Documentation: README, API reference, installation, security, contributing.

[1.0.0]: https://github.com/SultanBayezid/school-management-system/releases/tag/v1.0.0
