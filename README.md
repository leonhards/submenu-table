# SubMenu Table

![WordPress](https://img.shields.io/badge/WordPress-Plugin%20Ready-blue?logo=wordpress)
![GPLv2](https://img.shields.io/badge/License-GPL%20v2%2B-blue.svg)

A **WordPress plugin** to add a custom submenu and a CRUD table for vehicle registration with integrated pricing using js DataTables.

Designed as:

- 🧩 A starter project for extending vehicle registration management in WordPress.
- 🔍 A learning tool for building interactive CRUD interfaces with dynamic data.
- ⚙️ A modular solution for integrating pricing and registration functionalities.

## 🚀 Core Functionality

### A. Vehicle Registration Management

- **CRUD Operations**
  - Full CRUD functionality for vehicle records.
  - Manage comprehensive vehicle details:
    - Vehicle type
    - Registration number
    - Owner information
    - Pricing details

- **Pricing Integration**
  - Dynamic pricing calculations for vehicle registrations.
  - Customizable pricing rules based on vehicle attributes.

- **DataTables Integration**
  - Leverage js DataTables for an interactive experience:
    - Search, sorting, filtering, and pagination.
    - Responsive table layouts for large datasets.

### B. Infrastructure & Security

- **Database**
  - Utilizes WordPress-compatible MySQL databases.
  - Optimized table structures for efficient data management.

- **Deployment**
  - Seamless integration with standard WordPress environments.
  - Easily deployable on existing WordPress installations.

## 📋 Prerequisites

- **WordPress** (latest version recommended)
- **PHP 7.4+**
- **MySQL 5.7+** or higher

## 🛠️ Installation & Setup

### 1. Clone the Repository

```bash
mkdir wordpress-plugin && cd wordpress-plugin
git clone https://github.com/leonhards/submenu-table.git
```

### 2. Install the Plugin

Move the plugin to your WordPress plugins directory (replace with your actual path):

```bash
mv submenu-table ./wp-content/plugins/
```

### 3. Activate the Plugin

- Log in to your WordPress admin dashboard (e.g., http://localhost/wp-admin).
- Navigate to Plugins → Installed Plugins.
- Activate SubMenu Table.

## 🖥️ Usage

- A new Vehicle Registration submenu will appear in the WordPress dashboard.
- Use the CRUD interface to add, edit, or delete vehicle records.
- Manage pricing details seamlessly with integrated js DataTables functionalities.

## 🌐 Contributions

This project is open to the community! Feel free to:
- ⭐ Star the repository if you find it useful.
- 🐛 Report issues or suggest features via GitHub Issues.
- 🛠️ Submit pull requests for enhancements or bug fixes.

## 📜 License

This project is licensed under **GPL-2.0-or-later** - see the [GNU General Public License v2.0](https://www.gnu.org/licenses/gpl-2.0.html) for full details.

---

Happy Coding! 🚀
