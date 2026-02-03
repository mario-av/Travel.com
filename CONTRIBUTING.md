# Contributing to Traveldotcom

Thank you for considering contributing to the Traveldotcom project. This document outlines the standards and procedures for contributing to this repository.

## Development Workflow

1.  **Clone the repository**:
    ```bash
    git clone https://github.com/company/app-vacacional.git
    ```
2.  **Create a feature branch**:
    ```bash
    git checkout -b feature/my-new-feature
    ```
3.  **Make your changes**.
4.  **Run tests**:
    ```bash
    php artisan test
    ```
5.  **Commit your changes**:
    ```bash
    git commit -m "feat: add some feature"
    ```
6.  **Push to the branch**:
    ```bash
    git push origin feature/my-new-feature
    ```
7.  **Create a Pull Request**.

## Coding Standards

### PHP

- Follow **PSR-12** coding standards.
- Use strictly **English** for all class names, variables, methods, and comments.
- **Javadoc Comments**: All classes and methods must be documented using Javadoc style `/** ... */`.
- **No Inline Comments**: Avoid loose `//` comments unless absolutely necessary for complex logic explanation.
- **No Emojis**: Do not use emojis in code, commits, or documentation.

### Database

- Table names must be plural and English (e.g., `vacations`, `users`).
- Column names must be snake_case and English.

## Pull Request Process

1.  Ensure all tests pass.
2.  Update the `README.md` if you strictly change how the application is used or configured.
3.  Update the `CHANGELOG.md` with details of changes.
4.  The PR must be reviewed by at least one other developer, preferably an Admin.

## Reporting Bugs

Please open an issue in the repository with the following details:

- Steps to reproduce.
- Expected behavior.
- Actual behavior.
- Screenshots (if applicable).
