# Leave Management

Leave management system for NCIT users to apply for leave and admins to approve or reject applications.

## How to Run

```bash
sail up -d
sail npm install
sail artisan migrate:fresh --seed
sail npm run dev
```

## Notes

- System has two user types:
  - Admin user
  - Normal user
- Admin user is defined using `is_admin` boolean on users table