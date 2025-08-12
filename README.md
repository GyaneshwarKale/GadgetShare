## 1. Install XAMPP
- Download & install XAMPP for your OS.  
- Start **Apache** and **MySQL** from the XAMPP Control Panel.

## 2. Copy project into `htdocs`
- Unzip or clone the repo.  
- Copy the `gadgetshare` folder into XAMPP's `htdocs` directory: C:\xampp\htdocs\gadgetshare

## 3. Create `uploads` folder and add images
- Inside the `gadgetshare` folder create a directory named `uploads`.  
- gadgetshare/uploads/

## 4. Import the database
- Open **phpMyAdmin** (`http://localhost/phpmyadmin`).  
- Go to **Import**, choose `gadgetshare.sql`, then click **Go**.

## 5. Configure and run the app
- Open the project in your editor (VS Code recommended).  
- Edit `db.php` if needed port: 3306.  
- Open in a browser: http://localhost/gadgetshare/index.php

DONE! your **GadgetShare** wesite is ready.
