# Cyrilgram — Realtime Chat 💬

A realtime chat built with Laravel. Supports public and private rooms, real-time message broadcasting via WebSockets, presence channels, invites, user searching, and more.

![Laravel](https://img.shields.io/badge/Laravel-12-FF2D20?style=flat&logo=laravel&logoColor=white)
![PHP](https://img.shields.io/badge/PHP-8.2+-777BB4?style=flat&logo=php&logoColor=white)
![Reverb](https://img.shields.io/badge/WebSockets-Reverb-000?style=flat)
![MySQL](https://img.shields.io/badge/MySQL-Database-4479A1?style=flat&logo=mysql&logoColor=white)

---

## 🚀 Features

### 🔐 Authentication
- Registration, login, logout via **Laravel Breeze**
- Session-based auth with CSRF protection
- Profile management (edit, delete account)

### 💬 Rooms
- Create **public** or **private** chat rooms
- Browse public rooms, join/leave anytime
- Role-based access: `owner`, `admin`, `member` (via pivot table)

### ✉️ Messages
- Send and delete messages in real-time
- Cursor pagination for efficient history loading
- Messages broadcast instantly via **Laravel Reverb**

### 👥 Invites & Discovery
- Send invites from a room to any user by email/name
- Accept or decline invites from dashboard
- Search users globally with live suggestions

### ⚡ Realtime
- New messages appear instantly without page reload
- Event: `MessageSent`

### 🔒 Authorization
All actions protected by **Laravel Policies**:
- `RoomPolicy` — controls view/update/delete based on user role
- `MessagePolicy` — only author or room admin can delete
- `InvitePolicy` — controls invite lifecycle

---

## 🛠 Tech Stack

| Layer | Technology |
|-------|-----------|
| **Backend** | PHP 8.2+, Laravel 12, MySQL |
| **Auth** | Laravel Breeze (session + cookies) |
| **Frontend** | Blade templates + Alpine.js + Tailwind CSS |
| **Realtime** | Laravel Reverb (self-hosted WebSockets) |
| **Broadcasting** | Laravel Echo + Presence Channels |
| **Queue** | Database/Sync driver + `queue:work` |

---

## 🌐 Web Routes

> All routes (except auth forms) are protected by the `auth` middleware. Authentication uses **sessions**, not tokens.

### 🔐 Auth (Breeze)
*Handlers located in `routes/auth.php`*

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/login` | Show login form |
| POST | `/login` | Authenticate user |
| GET | `/register` | Show registration form |
| POST | `/register` | Create new account |
| POST | `/logout` | Log out user |

### 👤 Profile

| Method | Endpoint | Controller | Description |
|--------|----------|------------|-------------|
| GET | `/profile` | `ProfileController@edit` | Edit profile page |
| PATCH | `/profile` | `ProfileController@update` | Update name/email |
| DELETE | `/profile` | `ProfileController@destroy` | Delete account |

### 💬 Rooms

| Method | Endpoint | Controller | Description |
|--------|----------|------------|-------------|
| GET | `/` | `view('home')` | Dashboard / home |
| GET | `/rooms` | `RoomController@index` | List user's rooms |
| POST | `/rooms` | `RoomController@store` | Create new room |
| GET | `/rooms/{room}` | `RoomController@show` | View room + chat |
| PATCH | `/rooms/{room}` | `RoomController@update` | Update room settings |
| DELETE | `/rooms/{room}` | `RoomController@destroy` | Delete room |
| GET | `/public-rooms` | `RoomController@publicRooms` | Browse public rooms |
| POST | `/rooms/{room}/join` | `RoomController@join` | Join a public room |
| POST | `/rooms/{room}/leave` | `RoomController@leave` | Leave current room |

### ✉️ Messages

| Method | Endpoint | Controller | Description |
|--------|----------|------------|-------------|
| GET | `/rooms/{room}/chat` | `MessageController@index` | Load chat interface |
| POST | `/rooms/{room}/chat` | `MessageController@store` | Send new message |
| DELETE | `/rooms/{room}/chat/{message}` | `MessageController@destroy` | Delete message |

### 👥 Users & Invites

| Method | Endpoint | Controller | Description |
|--------|----------|------------|-------------|
| GET | `/users/search?q=...` | `UserController@search` | Search users by name |
| GET | `/users/{user}` | `UserController@show` | View user profile |
| GET | `/invites` | `InviteController@index` | List incoming invites |
| POST | `/invites` | `InviteController@store` | Send invite to user |
| POST | `/invites/{invite}/accept` | `InviteController@accept` | Accept invite |
| POST | `/invites/{invite}/decline` | `InviteController@decline` | Decline invite |

---

## ⚡ Realtime: Events & Channels

Laravel Reverb broadcasts events to connected clients via WebSocket.

| Event | Channel | Trigger |
|-------|---------|---------|
| `MessageSent` | `rooms.{roomId}` | New message posted |

### Presence Channel
Track active users in a room:

---

## Database Schema

users           — id, name, email, password, timestamps
rooms           — id, name, type (public/private), timestamps
room_user       — id, room_id, user_id, role (owner/admin/member)
messages        — id, room_id, user_id, content, timestamps
invites         — id, room_id, from_user_id, to_user_id, status (pending/accepted/declined), timestamps
💡 The room_user pivot table enables flexible role-based permissions per room.

---

## Architecture Overview

Controllers — Handle HTTP requests, validate input, return Blade views.
Policies — Centralize authorization logic (RoomPolicy, MessagePolicy, InvitePolicy).
Events & Listeners — MessageSent event triggers broadcasting via Reverb.
Blade — Server-rendered templates with lightweight JS for interactivity.
Cursor Pagination — Efficient message history loading without offset lag.
No API Resources — Responses are Blade views; JSON is used only for internal Echo events.

---

## Installation

# 1. Clone & install dependencies
git clone https://github.com/ILGcyril/cyrilgram.local
cd cyrilgram.local
composer install
npm install && npm run build

# 2. Environment setup
cp .env.example .env
php artisan key:generate

# 3. Database & broadcasting
php artisan migrate
php artisan install:broadcasting  # Sets up Reverb + Echo

# 4. Configure .env
# - DB credentials
# - Reverb: REVERB_APP_ID, REVERB_APP_KEY, REVERB_APP_SECRET
# - Mail driver (for invites/password reset)

---

## Running Locally

# Terminal 1: Laravel app
php artisan serve

# Terminal 2: WebSocket server
php artisan reverb:start

# Terminal 3: Queue worker (for invites, broadcasts)
php artisan queue:work

---

## Development Tips

Use php artisan tinker to test models/policies
Enable APP_DEBUG=true for detailed errors during dev
Tailwind: run npm run dev for hot-reload during UI work
Reverb logs: check storage/logs/laravel.log for connection issues