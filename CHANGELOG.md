# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.1.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

## [2.0.0] – 2026-01-01

### Added

- Introduced **PHP 8 Attributes** for declaring required OAuth scopes on controllers and controller actions.
- Added `#[RequiresScope]` and `#[RequiresAnyScope]` attributes to define mandatory Passport scopes declaratively.
- Automatic resolution of **class-level and method-level scope attributes** at runtime.
- Method-level scope attributes are **additive** to class-level requirements.
- New middleware `ResolvePassportScopeAttributes` to enforce scope validation consistently.
- Optional **automatic middleware bootstrapping** via configuration (`passport-modern-scopes.auto_boot`).

> Initial stable release of the package in its current architecture.