<p align="center">
    <img src=".github/.media/logo.png" width="120" height="120" alt="Toohga app logo">
</p>

<h1 align="center">Toohga</h1>
<p align="center">The smart URL shortener</p>

<br>

<p align="center">
    <a href="https://github.com/jarne/Toohga/blob/master/package.json">
        <img src="https://img.shields.io/github/package-json/v/jarne/Toohga.svg" alt="Package version">
    </a>
    <a href="https://circleci.com/gh/jarne/Toohga">
        <img src="https://circleci.com/gh/jarne/Toohga.svg?style=svg&circle-token=185df0a06d48a602df7d3db656e82186c89d4868" alt="Build status">
    </a>
    <a href="https://github.com/jarne/Toohga/blob/master/LICENSE">
        <img src="https://img.shields.io/github/license/jarne/Toohga.svg" alt="License">
    </a>
</p>

##

[• Description](#-description)  
[• Usage](#-usage)  
[• Contribution](#-contribution)  
[• License](#%EF%B8%8F-license)

## 📙 Description

Toohga is your smart private URL shortener with very short URL's.

### Screenshots

<img src=".github/.media/screenshot_web.png" alt="Screenshot of Toogha web app">

<img src=".github/.media/screenshot_admin.png" alt="Screenshot of Toogha admin interface">

## 🖥 Usage

### Setup & requirements

The application needs a modern version of PHP, a MySQL database and a Redis server.

The following environment variables need to be set:

| Env variable     | Description                              |
| ---------------- | ---------------------------------------- |
| `MYSQL_HOST`     | Hostname of MySQL server                 |
| `MYSQL_USER`     | Database user                            |
| `MYSQL_PASSWORD` | Password of the database user            |
| `MYSQL_DATABASE` | Name of the MySQL database               |
| `REDIS_HOST`     | Hostname of the Redis server             |
| `ADMIN_KEY`      | Secure random secret for the admin panel |

Additionally, the following optional environment variables can be set:

| Env variable        | Description                                                                                  |
| ------------------- | -------------------------------------------------------------------------------------------- |
| `AUTH_REQUIRED`     | Requires a user authentication PIN when creating URL's (set to true/false, default to false) |
| `DELETE_AFTER_DAYS` | Delete URL's after x days (default is 14 days)                                               |
| `CONTACT_EMAIL`     | Display a contact e-mail address on the front page                                           |
| `PRIVACY_URL`       | Display a link to an external privacy page on the front page                                 |
| `ANALYTICS_SCRIPT`  | Embed HTML code for an analytics script                                                      |

## 🙋‍ Contribution

Contributions are always very welcome! It's completely equal if you're a beginner or a more experienced developer.

Thanks for your interest 🎉👍!

## 👨‍⚖️ License

[MIT](https://github.com/jarne/Toohga/blob/master/LICENSE)
