# 🎁 Arta Reward Wallet System

[![License: MIT](https://img.shields.io/badge/License-MIT-yellow.svg)](https://opensource.org/licenses/MIT)
[![PHP Version](https://img.shields.io/badge/PHP-8.0%2B-blue.svg)](https://www.php.net/)
[![WordPress](https://img.shields.io/badge/WordPress-6.5%2B-blue.svg)](https://wordpress.org/)
[![WooCommerce](https://img.shields.io/badge/WooCommerce-Required-orange.svg)](https://woocommerce.com/)

A sophisticated WooCommerce plugin that implements a comprehensive wallet-based reward system. This plugin automatically grants users incentives for profile completion, successful referrals, and qualifying purchases. Wallet credits and points are applied automatically based on configurable rules defined by the site administrator, ensuring controlled, one-time rewards and seamless integration with the WooCommerce account system.

## 📋 Table of Contents

- [Features](#-features)
- [Architecture & Design Patterns](#-architecture--design-patterns)
- [Project Structure](#-project-structure)
- [Requirements](#-requirements)
- [Installation](#-installation)
- [Usage](#-usage)
- [Configuration](#-configuration)
- [Development](#-development)
- [Contributing](#-contributing)
- [License](#-license)
- [Author](#-author)

## ✨ Features

### Core Functionality

- **🎯 Profile Completion Rewards**: Automatically credit user wallets when they complete their profile with all required fields
- **🎁 Registration Bonus**: Configurable one-time registration bonus for new users
- **📊 Admin Dashboard**: Comprehensive dashboard with user statistics and system overview
- **⚙️ Flexible Settings**: Customizable account fields, bonus amounts, and reward rules
- **📱 SMS Integration**: Built-in SMS notification system with logging capabilities
- **📥 Bulk User Import**: Excel-based bulk user import functionality
- **📝 SMS Logs**: Complete SMS transaction logging and monitoring
- **🔔 User Notifications**: Real-time notifications for wallet credits and rewards
- **👤 Profile Management**: Enhanced WooCommerce account page with custom fields and validation

### Technical Features

- **🏗️ Modern Architecture**: Built with dependency injection, service providers, and clean architecture principles
- **🔒 Type Safety**: Full PHP 8.0+ type hints and return types
- **📦 PSR-4 Autoloading**: Standardized namespace and autoloading structure
- **🎨 View System**: Clean separation of concerns with dedicated view layer
- **🔌 Extensible**: Easy to extend with custom services and providers

## 🏗️ Architecture & Design Patterns

This plugin follows modern software architecture principles and implements several well-known design patterns:

### Design Patterns Implemented

#### 1. **Dependency Injection (DI) Container Pattern**
- **Location**: `src/Core/Container.php`
- **Purpose**: Manages class dependencies and resolves them automatically using reflection
- **Features**:
  - Automatic dependency resolution
  - Singleton support
  - Interface binding
  - Closure-based resolution
  - Parameter injection

#### 2. **Service Provider Pattern**
- **Location**: `src/Core/ServiceProvider.php`, `src/Provider/`
- **Purpose**: Modular service registration and bootstrapping
- **Implementation**:
  - `AbstractServiceProvider`: Base class for all service providers
  - `AdminServiceProvider`: Registers admin-related services
  - `WooCommerceServiceProvider`: Registers WooCommerce integration services
- **Benefits**: Separation of concerns, lazy loading, organized service management

#### 3. **Service Registry Pattern**
- **Location**: `src/Core/ServiceRegistry.php`
- **Purpose**: Centralized management of service providers
- **Features**:
  - Provider registration
  - Boot sequence management
  - Dependency tracking

#### 4. **Singleton Pattern**
- **Location**: `src/Contract/Abstract/AbstractSingleton.php`
- **Purpose**: Ensures single instance of critical classes
- **Implementation**: Used in `App` and `Container` classes
- **Features**: Thread-safe initialization, cloning prevention

#### 5. **Abstract Factory Pattern**
- **Location**: `src/Contract/Abstract/`
- **Purpose**: Provides base implementations for services and providers
- **Classes**:
  - `AbstractService`: Base for all service classes
  - `AbstractServiceProvider`: Base for all service providers
  - `AbstractSingleton`: Base for singleton implementations

#### 6. **Facade Pattern**
- **Location**: `src/Core/Application.php`
- **Purpose**: Provides simplified interface to complex subsystem
- **Features**: Static helper methods for common operations

#### 7. **Template Method Pattern**
- **Location**: `src/Contract/Abstract/AbstractServiceProvider.php`
- **Purpose**: Defines skeleton of algorithm in base class
- **Implementation**: `registerServices()` and `bootServices()` methods

### Architecture Layers

```
┌─────────────────────────────────────────────────────────┐
│                    Application Layer                     │
│  (App.php - Entry Point & Bootstrap)                    │
└─────────────────────────────────────────────────────────┘
                          │
                          ▼
┌─────────────────────────────────────────────────────────┐
│                      Core Layer                          │
│  ┌──────────────┐  ┌──────────────┐  ┌──────────────┐  │
│  │ Application  │  │  Container   │  │ServiceRegistry│  │
│  └──────────────┘  └──────────────┘  └──────────────┘  │
└─────────────────────────────────────────────────────────┘
                          │
                          ▼
┌─────────────────────────────────────────────────────────┐
│                   Service Provider Layer                 │
│  ┌──────────────┐  ┌──────────────────────┐            │
│  │AdminProvider │  │WooCommerceProvider   │            │
│  └──────────────┘  └──────────────────────┘            │
└─────────────────────────────────────────────────────────┘
                          │
                          ▼
┌─────────────────────────────────────────────────────────┐
│                     Service Layer                        │
│  ┌──────────┐  ┌──────────┐  ┌──────────┐  ┌────────┐ │
│  │MainMenu  │  │SettingMenu│  │ImportUsers│  │Account │ │
│  │          │  │          │  │          │  │Details │ │
│  └──────────┘  └──────────┘  └──────────┘  └────────┘ │
└─────────────────────────────────────────────────────────┘
                          │
                          ▼
┌─────────────────────────────────────────────────────────┐
│                    Helper Layer                         │
│  ┌──────────┐  ┌──────────┐                            │
│  │   Sms    │  │  ...     │                            │
│  └──────────┘  └──────────┘                            │
└─────────────────────────────────────────────────────────┘
```

### Key Architectural Principles

1. **Separation of Concerns**: Clear boundaries between layers
2. **Dependency Inversion**: High-level modules depend on abstractions
3. **Single Responsibility**: Each class has one clear purpose
4. **Open/Closed Principle**: Open for extension, closed for modification
5. **Interface Segregation**: Focused, specific interfaces
6. **Don't Repeat Yourself (DRY)**: Reusable abstractions and utilities

## 📁 Project Structure

```
arta-reward-wallet-system/
│
├── src/                          # Source code directory
│   ├── App/                      # Application entry point
│   │   └── App.php              # Main application bootstrap
│   │
│   ├── Core/                     # Core framework components
│   │   ├── Application.php      # Application facade and manager
│   │   ├── Container.php        # Dependency injection container
│   │   ├── ServiceProvider.php  # Base service provider
│   │   └── ServiceRegistry.php  # Service provider registry
│   │
│   ├── Contract/                 # Contracts and interfaces
│   │   ├── Abstract/            # Abstract base classes
│   │   │   ├── AbstractService.php
│   │   │   ├── AbstractServiceProvider.php
│   │   │   └── AbstractSingleton.php
│   │   └── Interface/           # Interface definitions
│   │       ├── ApplicationInterface.php
│   │       ├── ContainerInterface.php
│   │       ├── ServiceInterface.php
│   │       ├── ServiceProviderInterface.php
│   │       └── ServiceRegistryInterface.php
│   │
│   ├── Provider/                 # Service providers
│   │   ├── AdminServiceProvider.php
│   │   └── WooCommerceServiceProvider.php
│   │
│   ├── Service/                  # Business logic services
│   │   ├── Admin/               # Admin panel services
│   │   │   ├── MainMenu.php
│   │   │   ├── SettingMenu.php
│   │   │   ├── ImportUsers.php
│   │   │   └── SmsLogs.php
│   │   └── WooCommerce/         # WooCommerce integration
│   │       └── AccountDetails.php
│   │
│   └── Helper/                   # Helper classes
│       └── Sms.php              # SMS helper
│
├── Views/                        # View templates
│   ├── dashboard.php
│   ├── settings.php
│   ├── import-users.php
│   ├── sms-logs.php
│   └── profile-popup.php
│
├── assets/                       # Static assets
│   └── example.xlsx             # Example import file
│
├── vendor/                       # Composer dependencies
│   └── composer/
│
├── arta-reward-wallet-system.php # Main plugin file
├── composer.json                 # Composer configuration
├── LICENSE                       # License file
└── README.md                     # This file
```

### Namespace Structure

```
ArtaRewardWalletSystem\
├── App\                          # Application bootstrap
├── Core\                         # Core framework
├── Contract\                     # Contracts and interfaces
│   ├── Abstract\                # Abstract classes
│   └── Interface\               # Interfaces
├── Provider\                     # Service providers
├── Service\                      # Business logic
│   ├── Admin\                   # Admin services
│   └── WooCommerce\             # WooCommerce services
└── Helper\                       # Helper utilities
```

## 📋 Requirements

### Server Requirements

- **PHP**: 8.0 or higher
- **WordPress**: 6.5 or higher
- **WooCommerce**: Latest version (required)
- **WooWallet**: Plugin must be installed and activated (required)

### PHP Extensions

- `reflection` (for dependency injection)
- `json` (for data handling)
- `mbstring` (for string operations)

## 🚀 Installation

### Method 1: Manual Installation

1. **Download the plugin**
   ```bash
   git clone https://github.com/your-username/arta-reward-wallet-system.git
   ```

2. **Navigate to WordPress plugins directory**
   ```bash
   cd wp-content/plugins/
   ```

3. **Install Composer dependencies**
   ```bash
   cd arta-reward-wallet-system
   composer install
   ```

4. **Activate the plugin**
   - Go to WordPress Admin → Plugins
   - Find "Arta Reward Wallet System"
   - Click "Activate"

### Method 2: WordPress Admin

1. Upload the plugin folder to `/wp-content/plugins/`
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Ensure WooCommerce and WooWallet are installed and activated

## 💻 Usage

### Initial Setup

1. **Navigate to Settings**
   - Go to WordPress Admin → "امتیازات و شارژ کیف پول" (Reward Wallet System)
   - Click on "Settings" submenu

2. **Configure Account Fields**
   - Enable/disable account fields
   - Set field requirements
   - Customize field labels

3. **Set Bonus Amounts**
   - Configure profile completion bonus
   - Set registration bonus (optional)
   - Define reward rules

4. **SMS Configuration** (Optional)
   - Enter SMS API key
   - Configure SMS settings
   - Test SMS functionality

### Admin Features

#### Dashboard
- View user statistics
- Monitor system status
- Quick access to all features

#### User Import
- Import users from Excel files
- Bulk user creation
- Automatic wallet credit assignment

#### SMS Logs
- View all SMS transactions
- Monitor SMS delivery status
- Debug SMS issues

### User Features

#### Profile Completion
- Users are prompted to complete their profile
- Incomplete profile popup on account pages
- Automatic wallet credit upon completion

#### Account Management
- Enhanced account edit page
- Custom field validation
- Real-time notifications

## ⚙️ Configuration

### Settings Options

The plugin stores configuration in WordPress options:

- `arta_account_fields`: Account field configuration
- `arta_completion_bonus_amount`: Profile completion bonus amount
- `arta_enable_registration_bonus`: Enable/disable registration bonus
- `arta_registration_bonus_amount`: Registration bonus amount
- `arta_sms_api_key`: SMS API key
- `arta_sms_parent_number`: SMS parent number
- `arta_sms_logs`: SMS transaction logs

### Hooks and Filters

The plugin provides various WordPress hooks for extensibility:

```php
// Action hooks
do_action('arta_wallet_credit_added', $user_id, $amount);
do_action('arta_profile_completed', $user_id);

// Filter hooks
apply_filters('arta_bonus_amount', $amount, $user_id);
apply_filters('arta_account_fields', $fields);
```

## 🔧 Development

### Setting Up Development Environment

1. **Clone the repository**
   ```bash
   git clone https://github.com/your-username/arta-reward-wallet-system.git
   cd arta-reward-wallet-system
   ```

2. **Install dependencies**
   ```bash
   composer install
   ```

3. **Development Guidelines**
   - Follow PSR-12 coding standards
   - Use type hints for all methods
   - Write descriptive docblocks
   - Follow the existing architecture patterns

### Code Style

- **PSR-4**: Autoloading standard
- **PSR-12**: Coding style guide
- **Type Hints**: Use PHP 8.0+ type declarations
- **Docblocks**: PHPDoc comments for all classes and methods

### Adding New Services

1. Create service class extending `AbstractService`
2. Register in appropriate `ServiceProvider`
3. Implement `boot()` method for WordPress hooks

Example:
```php
namespace ArtaRewardWalletSystem\Service\Admin;

use ArtaRewardWalletSystem\Contract\Abstract\AbstractService;

class MyNewService extends AbstractService
{
    public function boot(): void
    {
        add_action('init', [$this, 'myMethod']);
    }
    
    public function myMethod(): void
    {
        // Your code here
    }
}
```

### Adding New Service Providers

1. Create provider class extending `AbstractServiceProvider`
2. Implement `registerServices()` and `bootServices()` methods
3. Register in `App.php`

## 🤝 Contributing

Contributions are welcome! Please follow these guidelines:

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/amazing-feature`)
3. Commit your changes (`git commit -m 'Add some amazing feature'`)
4. Push to the branch (`git push origin feature/amazing-feature`)
5. Open a Pull Request

### Contribution Guidelines

- Follow PSR-12 coding standards
- Write meaningful commit messages
- Add tests for new features
- Update documentation as needed
- Ensure backward compatibility

## 📄 License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

## 👤 Author

**Amir Safari**

- Email: amir.safari.dev@gmail.com
- Website: [https://artacode.net](https://artacode.net)
- GitHub: [@amirsafari](https://github.com/amirsafari)

---

## 🙏 Acknowledgments

- Built for WooCommerce ecosystem
- Inspired by modern PHP frameworks
- Uses WordPress best practices

---

**⭐ If you find this project helpful, please consider giving it a star!**
