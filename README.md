# 🌍 UnityExchange
[![PHP](https://img.shields.io/badge/PHP-777BB4?style=flat&logo=php&logoColor=white)](https://www.php.net/)
[![MySQL](https://img.shields.io/badge/MySQL-4479A1?style=flat&logo=mysql&logoColor=white)](https://www.mysql.com/)
[![JavaScript](https://img.shields.io/badge/JavaScript-F7DF1E?style=flat&logo=javascript&logoColor=black)](https://developer.mozilla.org/en-US/docs/Web/JavaScript)
[![CSS3](https://img.shields.io/badge/CSS3-1572B6?style=flat&logo=css3&logoColor=white)](https://developer.mozilla.org/en-US/docs/Web/CSS)
[![CI/CD](https://img.shields.io/badge/Deploy-GitHub_Actions-2088FF?style=flat&logo=github-actions&logoColor=white)](https://github.com/features/actions)
[![License: MIT](https://img.shields.io/badge/License-MIT-yellow.svg)](https://opensource.org/licenses/MIT)

UnityExchange is a custom-built, C-2-C (Consumer-to-Consumer) e-commerce marketplace designed to empower the South African informal economy. It provides a secure, fast, and highly accessible digital platform for local entrepreneurs and community members to trade goods and services seamlessly.

## 🚀 The Mission
The informal economy is a massive driver of entrepreneurship, but it often lacks digital tools that prioritize safety, seamless logistics, and financial inclusion. UnityExchange bridges this gap by offering an intuitive platform that works flawlessly across all devices, ensuring local trading is accessible to everyone.

## ✨ Core Features
* **Mobile-First Responsive UI:** Engineered from the ground up using pure CSS (no bloated frameworks) to ensure pixel-perfect rendering across standard desktop monitors, tablets, and mobile devices.
* **Custom Marketplace & Cart:** Features dynamic product grids, category filtering, and a seamless shopping cart experience.
* **Secure Authentication:** User registration, secure login, and dedicated seller profiles.
* **Admin Command Center:** A comprehensive backend dashboard for inventory control, user management, and transaction monitoring.
* **Automated Cache-Busting:** Integrated PHP-driven cache invalidation to ensure users instantly receive UI updates without manual browser refreshes.

## 🛠️ Tech Stack
* **Frontend:** HTML5, Vanilla JavaScript, Custom CSS3 (Flexbox/Grid architecture)
* **Backend:** PHP
* **Database:** MySQL
* **CI/CD:** GitHub Actions (Automated FTP deployment pipeline)

## 🏗️ Architecture & Deployment
This project utilizes a custom continuous integration and deployment (CI/CD) pipeline via GitHub Actions. Pushes to the main branch automatically sync and deploy via FTP to the live hosting environment, ensuring rapid and reliable shipping of new features. 

## 🔜 Upcoming Features
* **Advanced User Metrics:** Enhanced dashboard analytics for top sellers.

## 💻 Local Setup
To run this project locally, you will need a local server environment like XAMPP, MAMP, or Laragon.

1. Clone the repository:
   ```bash
   git clone https://github.com/crunchwilkinson/UnityExchange.git
   ```

2. Move the project folder into your local server's htdocs (XAMPP) or www directory.

3. Import the database.sql file (if provided) into your local MySQL interface (e.g., phpMyAdmin).

4. Rename .env.example to .env and configure your local database credentials and app URLs.

5. Open your browser and navigate to http://localhost/UnityExchange.

Developed by Tristan Wilkinson - Empowering local trade through technology.
