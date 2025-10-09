# Testing Guide for SIMPEG System

## Overview

This document provides comprehensive testing guidelines for the SIMPEG (Sistem Informasi Kepegawaian) Laravel application. The test suite covers all major features including notifications, cuti management, perjalanan dinas, and laporan PD verification.

## Test Structure

### Test Categories

1. **Unit Tests** (`tests/Unit/`)
   - `NotificationTest.php` - Core notification system testing
   - Individual component testing

2. **Feature Tests** (`tests/Feature/`)
   - `CutiTest.php` - Complete cuti management workflow
   - `PerjalananDinasTest.php` - Perjalanan dinas management
   - `LaporanPDTest.php` - Laporan PD verification process  
   - `NotificationApiTest.php` - Notification endpoints API
   - `Auth/` - Authentication and authorization tests

### Test Traits (`tests/Traits/`)

- `HasRolesAndPermissions.php` - Helper for creating users with roles
- `HasNotifications.php` - Notification testing utilities

## Running Tests

### Basic Test Commands

```bash
# Run all tests
php artisan test

# Run specific test file
php artisan test tests/Feature/CutiTest.php

# Run tests with coverage
php artisan test --coverage

# Run tests in verbose mode
php artisan test --verbose

# Run specific test method
php artisan test --filter test_admin_can_create_cuti_with_valid_data
```

### Environment Setup for Testing

```bash
# Ensure test database is configured
php artisan config:cache --env=testing

# Run migrations for test database
php artisan migrate:fresh --env=testing

# Seed test data
php artisan db:seed --env=testing
```

## Test Coverage Areas

### 1. Notification System Tests

#### Unit Tests (`tests/Unit/NotificationTest.php`)

**✅ Notification Creation & Data:**
- CutiApproved notification structure validation
- CutiRejected notification with rejection reasons
- LaporanPDVerified notification for both approved/rejected status
- Database notification data structure
- Email notification content validation

**✅ Notification Channels:**
- Email channel configuration
- Database channel configuration
- Multiple channel delivery

**✅ Notification Content:**
- Title, message, and action URLs
- Icon and color codes
- Timestamp formatting
- User personalization

#### Feature Tests (`tests/Feature/NotificationApiTest.php`)

**✅ API Endpoints:**
- `GET /notifications/recent` - Latest 5 notifications
- `GET /notifications/unread-count` - Unread notifications count
- `POST /notifications/{id}/read` - Mark notification as read
- `POST /notifications/read-all` - Mark all as read
- `DELETE /notifications/{id}` - Delete notification

**✅ Authorization:**
- User can only access their own notifications
- Guest access restrictions
- Cross-user access prevention

**✅ Performance:**
- API response times (<200ms for recent, <100ms for count)
- Large notification set handling
- Memory efficiency

### 2. Cuti Management Tests

#### Feature Tests (`tests/Feature/CutiTest.php`)

**✅ Authentication & Authorization:**
- Role-based access control
- Route protection for different user types
- Permission validation

**✅ CRUD Operations:**
- Create cuti with validation
- Update existing cuti requests
- Delete cuti records
- Data integrity checks

**✅ Approval Workflow:**
- Pimpinan approval process
- Rejection with reasons
- Status transitions
- Notification triggering

**✅ Sisa Cuti System:**
- Automatic deduction for cuti tahunan
- No deduction for non-tahunan cuti
- Multi-year allocation handling
- Balance validation

**✅ Validation Rules:**
- Date validation (start < end, future dates)
- Required field validation
- File upload validation (if applicable)
- Business logic validation

### 3. Perjalanan Dinas Tests

#### Feature Tests (`tests/Feature/PerjalananDinasTest.php`)

**✅ Management Features:**
- Create perjalanan dinas assignments
- Multiple pegawai assignment
- Document upload (SPT)
- CRUD operations

**✅ Pegawai Notifications:**
- Assignment notification delivery
- Multiple recipient handling
- Notification content accuracy

**✅ Pegawai Interface:**
- View assigned trips
- Only own assignments visible
- Status tracking
- Trip timeline

**✅ Search Features:**
- Pegawai search API
- Minimum input validation
- Result limiting (50 records)
- Performance validation

**✅ Date & Status:**
- Trip status calculation (upcoming, ongoing, completed)
- Date validation requirements
- Timeline准确性

### 4. Laporan PD Verification Tests

#### Feature Tests (`tests/Feature/LaporanPDTest.php`)

**✅ Regular Management:**
- File upload handling
- CRUD operations
- Unique constraint enforcement
- File validation (type, size)

**✅ Verification Workflow:**
- Admin Keuangan verification dashboard
- Approve/reject functionality
- Status updates
- Reason tracking

**✅ Notification System:**
- Pimpinan notification on verification
- Status-specific notifications
- Multiple pimpinan handling

**✅ Pegawai Features:**
- Report creation for own assignments
- View submitted reports
- Status tracking

**✅ Statistics & Analytics:**
- Verification statistics API
- Daily counts (approved, rejected, pending)  
- Dashboard data accuracy

### 5. API Tests

#### Feature Tests (`tests/Feature/NotificationApiTest.php`)

**✅ Endpoints Coverage:**
- All notification endpoints tested
- Response format validation
- Error handling

**✅ Integration Flows:**
- Complete workflows from trigger to display
- Cross-system integration
- End-to-end validation

**✅ Performance & Security:**
- Response time benchmarks
- Authorization enforcement
- Data privacy preservation

## Test Data Setup

### User Creation Examples

```php
// Create admin kepegawaian
$admin = $this->createAdminKepegawaian();

// Create pimpinan
$pimpinan = $this->createPimpinan(); 

// Create admin keuangan
$adminKeuangan = $this->createAdminKeuangan();

// Create pegawai with complete data
[$user, $pegawai] = $this->setupCompletePegawai();

// Login as specific role
$this->actingAsAdminKepegawai();
```

### Factory Usage

```php
// Create cuti with specific state
$cuti = Cuti::factory()->approved()->create();

// Create perjalanan dinas with date range
$trip = PerjalananDinas::factory()->upcoming()->create();

// Create laporan PD with verification status
$report = LaporanPD::factory()->unverified()->create();
```

## Continuous Integration

### GitHub Actions (Recommended)

```yaml
name: Tests

on: [push, pull_request]

jobs:
  test:
    runs-on: ubuntu-latest
    
    steps:
    - uses: actions/checkout@v2
    
    - name: Setup PHP
      uses: shivammathur/setup-php@v2
      with:
        php-version: '8.1'
        extensions: mbstring, xdebug
        
    - name: Copy .env
      run: cp .env.example .env
      
    - name: Install Dependencies
      run: composer install -q --no-ansi --no-interaction --no-scripts
      
    - name: Generate Key
      run: php artisan key:generate
      
    - name: Execute Tests
      run: php vendor/bin/phpunit --coverage-clover=coverage.xml
      
    - name: Upload Coverage
      uses: codecov/codecov-action@v1
```

### Local Development Testing

```bash
# Watch mode for continuous testing
php artisan test --watch

# Parallel testing
php artisan test --parallel

# Generate coverage report
php artisan test --coverage --coverage-html=coverage
```

## Best Practices

### Test Writing Guidelines

1. **Descriptive Test Names**: Use `test_` prefix with clear descriptions
2. **AAA Pattern**: Arrange-Act-Assert structure
3. **Test Independence**: Each test should be self-contained
4. **Clean Up**: Use `RefreshDatabase` trait for clean state
5. **Realistic Data**: Use factories for realistic test data

### Performance Testing

```php
// Performance benchmark example
$startTime = microtime(true);
$response = $this->get(route('api.endpoint'));
$executionTime = (microtime(true) - $startTime) * 1000;

$response->assertOk();
$this->assertLessThan(200, $executionTime); // Less than 200ms
```

### Database Testing

```php
// Use RefreshDatabase trait for clean state
use RefreshDatabase;

// Test database state
$this->assertDatabaseHas('cuti', ['status' => 'Disetujui']);
$this->assertDatabaseMissing('pegawai', ['id' => 999]);
```

## Troubleshooting

### Common Issues

1. **Test Database Issues:**
   ```bash
   php artisan migrate:fresh --env=testing
   ```

2. **Permission Issues:**
   ```bash
   php artisan config:clear
   php artisan cache:clear
   ```

3. **Memory Issues:**
   ```bash
   php -d memory_limit=512M artisan test
   ```

4. **Queue Testing:**
   ```php
   Queue::fake();
   Notification::fake();
   ```

### Debug Mode

```bash
# Run with debug information
php artisan test --debug

# Stop on first failure
php artisan test --stop-on-failure

# Run specific test with detailed output
php artisan test --filter test_name --verbose
```

## Coverage Report

Run this command to generate comprehensive coverage report:

```bash
php artisan test --coverage --coverage-text --coverage-html
```

Target coverage areas:
- ✅ Controllers: 95%+
- ✅ Models: 90%+  
- ✅ Notifications: 100%
- ✅ API Endpoints: 95%+
- ✅ Business Logic: 90%+

## Release Checklist

Before releasing new features:

1. **Run All Tests:** `php artisan test`
2. **Check Coverage:** Minimum 85% coverage
3. **Performance Tests:** Ensure API responses <200ms
4. **Integration Tests:** Test complete user workflows
5. **Security Tests:** Verify access controls
6. **Database Tests:** Check data integrity

---

**This testing suite ensures the reliability, security, and performance of the SIMPEG system across all major features and workflows.**
