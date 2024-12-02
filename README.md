Eloquent-Maxio-Driver: PHP Eloquent Driver for MinIO The eloquent-maxio-driver is designed to enable PHP Eloquent applications to access MinIO as a database using Eloquent modeling.

Note: This driver is currently under development and is not ready for release.

Installation and Usage in a PHP Laravel Application To install and use the driver in your Laravel application, follow these steps:

Clone the source code from the Git repository: git clone https://github.com/BingWen923/eloquent-maxio-driver.git

Configure Composer to recognize the local repository. Replace ../eloquent-maxio-driver with the correct relative path to the driver directory: composer config repositories.eloquent-maxio-driver path ../eloquent-maxio-driver

Require the driver in your Laravel application: composer require exaba/eloquent-maxio-driver

Additional Configuration Steps for Your Application After installing the eloquent-maxio-driver, you need to configure your Laravel application to use it. Follow these steps: a) Update the .env File b) Update the database.php Configuration
