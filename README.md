# Link-Shortener
### Setup phpmyadmin
IMPORTANT: INSTALL THE XAMPP APPLICATION FIRST ON `https://www.apachefriends.org/`

1. Go to `localhost/phpmyadmin` or `127.0.0.1/phpmyadmin`
2. Create a new database named `url_short`
3. Select the SQL menu
4. Paste the following code into SQL
```bash
CREATE TABLE urls (
  id INT AUTO_INCREMENT PRIMARY KEY,
  long_url TEXT NOT NULL,
  short_code VARCHAR(10) NOT NULL UNIQUE,
  expires_at TIMESTAMP NULL DEFAULT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

ALTER TABLE urls ADD COLUMN expires_at TIMESTAMP NULL DEFAULT NULL;
```

