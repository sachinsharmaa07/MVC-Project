# Testing Credentials

## Overview
All test users have been seeded into the system with the password: **`password`**

## Admin Users
Access admin dashboard, manage requests, assign routes, view analytics

| Email | Password | Role |
|-------|----------|------|
| admin1@waste.local | password | Admin |
| admin2@waste.local | password | Admin |
| admin3@waste.local | password | Admin |

## Citizen Users
Create pickup requests, track status

| Email | Password | Role |
|-------|----------|------|
| citizen@waste.local | password | Citizen (Demo User) |
| Generated | password | 9 additional Citizens (auto-generated) |

## Driver Users
View assigned routes, mark waste collected, update status

| Email | Password | Role |
|-------|----------|------|
| driver@waste.local | password | Driver (Demo User) |
| Generated | password | 4 additional Drivers (auto-generated) |

## How to Login

1. Navigate to `http://127.0.0.1:8001`
2. Click on the role-specific login link (Admin, Citizen, or Driver)
3. Enter email and password from the table above
4. Click "Sign in"

## User Roles & Permissions

### Admin Permissions
- ✅ Manage all pickup requests
- ✅ Assign trucks and routes
- ✅ View analytics and dashboards
- ✅ Manage users
- ✅ Export reports to CSV

### Citizen Permissions
- ✅ Create pickup requests
- ✅ View own requests
- ✅ Track request status in real-time
- ✅ Upload waste photos

### Driver Permissions
- ✅ View assigned routes for today
- ✅ View route stops and details
- ✅ Mark waste as collected
- ✅ Update collection status
- ✅ View notifications

## Demo Data Included

- **100+ Pickup Requests** - Distributed across all citizens
- **10 Routes** - With 6 stops each (assigned to drivers)
- **5 Trucks** - One for each driver
- **Roles & Permissions** - RBAC system fully configured

## Testing Workflow

### For Citizens
1. Login as `citizen@waste.local`
2. Click "New Request"
3. Fill address, select waste type
4. Click on map to set location
5. Submit request
6. View request status on dashboard

### For Admins
1. Login as `admin1@waste.local`
2. View all requests on dashboard
3. Create and manage routes
4. Assign trucks and drivers
5. View analytics

### For Drivers
1. Login as `driver@waste.local`
2. View today's routes
3. View stops for each route
4. Mark waste as collected
5. Check notifications

## Database Information

- **Database**: MongoDB 7 Community Edition
- **Connection**: `mongodb://127.0.0.1:27017/waste_system`
- **Collections**:
  - `users` - All user accounts
  - `pickup_requests` - Citizen requests
  - `routes` - Driver routes with stops
  - `trucks` - Vehicle information
  - `waste_logs` - Segregation compliance logs
  - `notifications` - System notifications
  - `sessions` - File-based session storage

## Notes

- All passwords are: `password`
- Demo users (citizen@waste.local, driver@waste.local, admin1@waste.local) have consistent data for testing
- Additional users are auto-generated with random names and emails
- System uses file-based sessions (no database sessions)
- Cache uses file driver (no Redis)
- Queue uses database driver (jobs stored in MongoDB)
