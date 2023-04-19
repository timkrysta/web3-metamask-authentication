# Laravel App with MetaMask Web3 Authentication

This is a Laravel web application that uses [MetaMask](https://metamask.io/) to authenticate users through the Ethereum blockchain.

## Installation

1. Clone this repository:
`git clone https://github.com/yourusername/your-repo-name.git`
2. Install PHP dependencies:
`composer install`
3. Install front-end dependencies:
`npm install`
4. Configure your environment variables by creating a `.env` file based on the `.env.example` file included in the project:
`cp .env.example .env`
5. Generate a new Laravel application key:
`php artisan key:generate`
6. Create a symbolic link from `public/storage` to `storage/app/public`:
`php artisan storage:link`
6. Run the database migrations:
`php artisan migrate`
7. Start the development server:
`php artisan serve`

## Usage
1. Open the application in your web browser at [http://localhost:8000](http://localhost:8000).
2. Click on the "Connect with MetaMask" button to authenticate with your MetaMask wallet.
3. If you are successfully authenticated, you should be able to see your Ethereum address displayed on the page.

