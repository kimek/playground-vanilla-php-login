# Vanilla PHP Login System

A simple login system built with vanilla PHP, supporting user registration, login, and file uploads.

## Requirements

- **PHP**: 7.0 or above
- **MySQL**: 5.7 or above
- **PHP Modules**:
    - php-mysql
    - php-pdo

## Installation

1. **Clone the repository**:
    ```sh
    git clone https://github.com/kimek/playground-vanilla-php-login.git
    cd playground-vanilla-php-login
    ```

2. **Set up the database**:
    - Create a new MySQL database, tables, user:
      ```sh
      mysql -u your_username -p your_database_name < migration/migration.sql
      ```

3. **Configure the application**:
    - If you would like to use custom DB credentials, update `config.php`.


4. **Ensure required PHP modules are installed**:
    - You can check for installed modules using the following command:
      ```sh
      php -m
      ```
    - To install missing modules, use:
      ```sh
      sudo apt-get install php-mysql php-pdo
      ```

## Usage

1. **Start your local development server**:
    ```sh
    php -S localhost:8000
    ```

2. **Access the application**:
    - Open your web browser and navigate to [http://localhost:8000](http://localhost:8000)

## Features

- User Registration
- User Login
- File Upload

## Folder Structure

- `index.php` - Main entry point for the application.
- `api.php` - Vanilla api script.
- `config/config.php` - Configuration file for database.
- `assets/` - Contains stylesheets and JavaScript files.
- `migration/migration.sql` - Contains the SQL schema for setting up the database.
- `src/controllers/userSystem.php` - Login and registration handling script.
- `src/inc/file_handling.php` - File upload handling script.
- `src/inc/json_helper.php` - Json helper for api.
- `src/inc/db_connection.php` - DB handling script.
- `src/view/` - Basic page content.
- `uploads/` - File upload folder.

## Contributing

If you wish to contribute to this project, please follow these steps:

1. Fork the repository.
2. Create a new branch (`git checkout -b feature/your-feature-name`).
3. Commit your changes (`git commit -m 'Add some feature'`).
4. Push to the branch (`git push origin feature/your-feature-name`).
5. Open a Pull Request.

## License

This project is licensed under the MIT License. See the [LICENSE](LICENSE) file for more information.

## Contact

For any questions or inquiries, please create contribution issue ticket.

