# Leave Management

Leave management system for NCIT users to apply for leave and admins to approve or reject applications. 

# Stack

Laravel
Vue
Inertia

## How to Run

```bash
sail up -d
sail npm install
sail artisan migrate:fresh --seed
sail npm run dev
```

## User Credentials

**Admin User**
- Email: admin@ncit.com
- Password: password

**Normal User**
- Email: user@ncit.com
- Password: password

## Notes

- System has two user types:
  - Admin user
  - Normal user
- Admin user is defined using `is_admin` boolean on users table

## Database Tables

- **leave_balance** - Manages user leave balances
- **leaves** - Stores leave applications and approval status