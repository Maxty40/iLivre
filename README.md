# 📚 iLivre (Modern Library Management System)

### ⌨️ Developed By:
1. Dhiauddin Arfa
2. Ulwan Luthfi
3. Klaudia Weda
4. Ahmad Afifi
5. Freeze Ad Kaban
</br>

> “Livre” comes from French and means “book.” Empowering readers through seamless library management.

iLivre is a modern Library Management Information System (LIMS) designed to streamline book circulation, member management, and library administration. Built on the Laravel framework with an elegant interface, iLivre separates the Admin and User workspaces for a more focused experience.

---

## ✨ Key Features

- **🛡️ Admin Dashboard (Filament PHP):** An exclusive control panel for library staff to manage Book CRUD, Member Cards, Loans, and Returns.
- **👤 User Portal:** An interactive *landing page* and *dashboard* for library members to browse the book catalog.
- **⚙️ Advanced Database Architecture:** Implements advanced database specifications including *Stored Procedures*, *Triggers*, and *Views* for data integrity.
- **🎨 Modern UI/UX:** Built using Tailwind CSS for a responsive and visually appealing interface.

---

## 🛠️ Tech Stack

- **Framework:** Laravel 13
- **Admin Panel:** Filament 5
- **Frontend:** Tailwind CSS & Laravel Blade (Breeze)
- **Database:** MySQL
- **Version Control:** Git & GitHub

## ⚙️ How to Setup the Project
1. **Clone the project**
```bash
git clone https://github.com/Maxty40/iLivre.git
```
2. **Install the composer (Installing the Laravel & Spatie)**
```bash
composer install
```
3. **Install NPM (For TailwindCSS)**
```bash
npm install
```
4. **Copy Environment**
```bash
cp .env.example .env
```
5. **Generate Key**
```bash
php artisan generate:key
```
6. **Config the database on ```.env```, then run:**
```bash
php artisan migrate --seed
```
7. **Generate Symlink**
```bash
php artisan storage:link
```
8. **Finally, run the project**
```bash
php artisan serve
npm run dev
```
9. **Enjoy!**