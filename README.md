# LifeOS

A Personal Operating System built with Laravel, Blade, Alpine.js, and Tailwind CSS.

LifeOS helps you manage life, goals, projects, tasks, habits, focus, and execution using GTD, Time Blocking, SMART Goals, and the 80/80/80 Rule.

## Features

- **Daily Planner** — command center for your entire day
- **80/80/80 System** — P1/P2/P3 priorities with daily success scoring
- **GTD Inbox** — quick capture with global modal
- **Goals & Projects** — SMART goals with Kanban boards
- **Time Blocking** — start deadline rule with missed/reschedule
- **Calendar** — day/week/month views with Jalali (Persian) dates
- **Habits, Journal, Notes** — tracking and reflection
- **Focus Mode** — distraction-free execution
- **Reviews & Statistics** — weekly/monthly reviews with charts

## Requirements

- PHP 8.2+
- MySQL 8+
- Node.js 18+
- Composer

## Setup

```bash
composer install
cp .env.example .env
php artisan key:generate

# Configure MySQL in .env, then:
php artisan migrate --seed
npm install
npm run build
php artisan serve
```

## Demo Account

After seeding:

- **Email:** demo@lifeos.app
- **Password:** password

## Tech Stack

- Laravel 12
- Blade + Alpine.js
- Tailwind CSS 4
- MySQL
- morilog/jalali (Persian calendar display)

## Testing

```bash
php artisan test
```
