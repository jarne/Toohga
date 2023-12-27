<p align="center">
    <img src=".github/.media/logo.png" width="120" height="120" alt="Toohga app logo">
</p>

<h1 align="center">Toohga</h1>
<p align="center">The smart URL shortener</p>

<br>

<p align="center">
    <a href="https://github.com/users/jarne/packages/container/package/toohga">
        <img src="https://img.shields.io/github/v/tag/jarne/Toohga?label=release" alt="Release version">
    </a>
    <a href="https://circleci.com/gh/jarne/Toohga">
        <img src="https://circleci.com/gh/jarne/Toohga.svg?style=shield" alt="Build status">
    </a>
    <a href="https://github.com/jarne/Toohga/blob/master/LICENSE">
        <img src="https://img.shields.io/github/license/jarne/Toohga.svg" alt="License">
    </a>
</p>

##

[Description](#-description) | [Usage](#-usage) | [Contribution](#-contribution) | [License](#%EF%B8%8F-license)

## 📙 Description

Toohga is your smart private URL shortener with very short URL's.
It uses a custom algorithm to generate short codes that stay as short as possible, using combinations of numbers and letters.
The application can be operated both open to the public or secured by PIN codes required to create new URL's.
An admin panel provides features to perform administrative tasks, such as managing users and listing or deleting URL's.

### Screenshots

<img src=".github/.media/screenshot_web.png" alt="Screenshot of Toogha web app">

<img src=".github/.media/screenshot_admin.png" alt="Screenshot of Toogha admin interface">

## 🖥 Usage

### Setup & requirements

The application needs a modern version of PHP, a MySQL database and a Redis server.

The following environment variables need to be set:

| Env variable     | Description                                                  |
| ---------------- | ------------------------------------------------------------ |
| `MYSQL_HOST`     | Hostname of MySQL server                                     |
| `MYSQL_USER`     | Database user                                                |
| `MYSQL_PASSWORD` | Password of the database user                                |
| `MYSQL_DATABASE` | Name of the MySQL database                                   |
| `REDIS_HOST`     | Hostname of the Redis server                                 |
| `ADMIN_KEY`      | Secure secret for accessing the admin panel                  |
| `JWT_SECRET`     | Randomly generated secret for encrypting admin access tokens |

Additionally, the following _optional_ environment variables can be set:

| Env variable             | Description                                                                                     |
| ------------------------ | ----------------------------------------------------------------------------------------------- |
| `TGA_AUTH_REQUIRED`      | Requires a user authentication PIN when creating URL's (set to true/false, default to false)    |
| `DELETE_AFTER_DAYS`      | Delete URL's after x days (default is 14 days)                                                  |
| `TGA_CONTACT_EMAIL`      | Display a contact e-mail address on the front page                                              |
| `TGA_THEME`              | Visual color theme of the application (possible values are: `pink`, `orange`, `blue` (default)) |
| `TGA_PRIVACY_URL`        | Display a link to an external privacy page on the front page                                    |
| `TGA_ANALYTICS_SCRIPT`   | Embed HTML code for an analytics script                                                         |
| `TGA_API_ENDPOINT`       | Specify general API endpoint for front-end to use                                               |
| `TGA_ADMIN_API_ENDPOINT` | Specify admin API endpoint for front-end                                                        |

> All environment variables beginning with `TGA_` are exposed to the front-end and therefore are publically accessible. They should not contain sensitive information!

### Docker image

The recommended way to deploy Toohga is using its [Docker](./Dockerfile) image.

The image can be pulled from the
[GitHub Packages registry](https://github.com/users/jarne/packages/container/package/toohga)
using: `docker pull ghcr.io/jarne/toohga:latest`.

## 🙋‍ Contribution

Contributions are always very welcome! It's completely equal if you're a beginner or a more experienced developer.

Thanks for your interest 🎉👍!

## 👨‍⚖️ License

[MIT](https://github.com/jarne/Toohga/blob/master/LICENSE)
