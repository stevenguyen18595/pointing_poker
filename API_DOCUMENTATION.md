# Planning Poker API Documentation

## Overview

Complete Laravel backend implementation for Planning Poker with REST API endpoints that correspond to the React Query hooks.

## Base URL

All API endpoints are prefixed with `/api`

## Authentication

Currently, no authentication is implemented. Player identification is session-based for guests.

## Response Format

All API responses follow this structure:

```json
{
    "data": [
        /* response data */
    ],
    "message": "Success message (optional)",
    "meta": {
        /* metadata like pagination (optional) */
    }
}
```

## API Endpoints

### Game Statuses

| Method | Endpoint                  | Description                  |
| ------ | ------------------------- | ---------------------------- |
| GET    | `/api/game-statuses`      | Get all active game statuses |
| GET    | `/api/game-statuses/{id}` | Get specific game status     |

### Point Values

| Method | Endpoint                 | Query Params               | Description                   |
| ------ | ------------------------ | -------------------------- | ----------------------------- |
| GET    | `/api/point-values`      | `?active=true&type=number` | Get point values (filterable) |
| GET    | `/api/point-values/{id}` | -                          | Get specific point value      |

### Games

| Method | Endpoint                 | Description                              |
| ------ | ------------------------ | ---------------------------------------- |
| GET    | `/api/games`             | List all games                           |
| POST   | `/api/games`             | Create new game                          |
| GET    | `/api/games/{id}`        | Get game details with relationships      |
| PATCH  | `/api/games/{id}`        | Update game                              |
| DELETE | `/api/games/{id}`        | Delete game                              |
| POST   | `/api/games/join`        | Join game with player name and game code |
| PATCH  | `/api/games/{id}/status` | Update game status                       |

### Players

| Method | Endpoint                                          | Description                            |
| ------ | ------------------------------------------------- | -------------------------------------- |
| GET    | `/api/games/{gameId}/players`                     | Get all players in game                |
| GET    | `/api/games/{gameId}/players/{playerId}`          | Get specific player                    |
| PATCH  | `/api/games/{gameId}/players/{playerId}`          | Update player (name, moderator status) |
| DELETE | `/api/games/{gameId}/players/{playerId}`          | Remove player from game                |
| POST   | `/api/games/{gameId}/players/{playerId}/activity` | Update player activity timestamp       |

### Stories

| Method | Endpoint                              | Description                                   |
| ------ | ------------------------------------- | --------------------------------------------- |
| GET    | `/api/games/{gameId}/stories`         | Get all stories for game                      |
| POST   | `/api/games/{gameId}/stories`         | Create new story                              |
| GET    | `/api/stories/{storyId}`              | Get story details                             |
| PATCH  | `/api/stories/{storyId}`              | Update story                                  |
| DELETE | `/api/stories/{storyId}`              | Delete story                                  |
| POST   | `/api/stories/{storyId}/set-current`  | Set story as currently being voted on         |
| POST   | `/api/stories/{storyId}/complete`     | Mark story as completed with estimated points |
| POST   | `/api/stories/{storyId}/start-voting` | Start voting session (clears existing votes)  |

### Votes

| Method | Endpoint                                  | Description                  |
| ------ | ----------------------------------------- | ---------------------------- |
| GET    | `/api/stories/{storyId}/votes`            | Get all votes for story      |
| POST   | `/api/stories/{storyId}/votes`            | Submit or update vote        |
| GET    | `/api/stories/{storyId}/votes/{playerId}` | Get specific player's vote   |
| DELETE | `/api/stories/{storyId}/votes/{playerId}` | Delete player's vote         |
| PATCH  | `/api/votes/{voteId}`                     | Update specific vote         |
| POST   | `/api/stories/{storyId}/reveal`           | Reveal votes with statistics |
| DELETE | `/api/stories/{storyId}/votes`            | Reset all votes for story    |

## Request Examples

### Create Game

```bash
POST /api/games
Content-Type: application/json

{
  "name": "Sprint 23 Planning",
  "description": "Planning poker for sprint 23 user stories",
  "settings": {
    "timer_minutes": 5,
    "allow_observer": true
  }
}
```

### Join Game

```bash
POST /api/games/join
Content-Type: application/json

{
  "game_code": "ABC123XY",
  "player_name": "John Doe"
}
```

### Create Story

```bash
POST /api/games/1/stories
Content-Type: application/json

{
  "title": "User Authentication",
  "description": "Implement user login and registration",
  "acceptance_criteria": "User can login with email/password"
}
```

### Submit Vote

```bash
POST /api/stories/1/votes
Content-Type: application/json

{
  "point_value_id": 5,
  "player_id": 2
}
```

## Response Examples

### Game with Relationships

```json
{
    "data": {
        "id": 1,
        "name": "Sprint Planning",
        "game_code": "ABC123XY",
        "status_id": 2,
        "created_by": 1,
        "settings": { "timer_minutes": 5 },
        "started_at": "2025-11-23T12:00:00.000000Z",
        "completed_at": null,
        "created_at": "2025-11-23T12:00:00.000000Z",
        "updated_at": "2025-11-23T12:00:00.000000Z",
        "is_active": true,
        "status": {
            "id": 2,
            "name": "voting",
            "label": "Voting in Progress",
            "color_class": "text-blue-600 bg-blue-100"
        },
        "players": [
            {
                "id": 1,
                "name": "John Doe",
                "is_moderator": true,
                "is_online": true,
                "last_seen_at": "2025-11-23T12:30:00.000000Z"
            }
        ],
        "current_story": {
            "id": 1,
            "title": "User Authentication",
            "is_current": true,
            "votes_count": 3
        },
        "players_count": 4,
        "stories_count": 8
    }
}
```

### Vote Statistics

```json
{
    "data": {
        "votes": [
            /* vote objects */
        ],
        "revealed": true,
        "statistics": {
            "total_votes": 4,
            "average": 5.5,
            "consensus": null,
            "distribution": {
                "5": { "count": 2, "percentage": 50.0 },
                "8": { "count": 1, "percentage": 25.0 },
                "3": { "count": 1, "percentage": 25.0 }
            }
        }
    }
}
```

## Database Models & Relationships

### Game

-   **Relationships**: belongsTo(GameStatus), belongsTo(User as creator), hasMany(Player), hasMany(Story)
-   **Attributes**: is_active (computed)

### Player

-   **Relationships**: belongsTo(Game), belongsTo(User), hasMany(Vote)
-   **Attributes**: is_online (computed based on last_seen_at)

### Story

-   **Relationships**: belongsTo(Game), hasMany(Vote)
-   **Attributes**: has_votes, all_players_voted (computed)

### Vote

-   **Relationships**: belongsTo(Story), belongsTo(Player), belongsTo(PointValue)
-   **Auto-timestamps**: voted_at set automatically

### GameStatus & PointValue

-   **Static reference data** with active/inactive flags

## Features Implemented

### ✅ Game Management

-   Create games with unique codes
-   Join games via game code
-   Update game status
-   Track game lifecycle

### ✅ Player Management

-   Session-based guest players
-   Optional user account linking
-   Activity tracking (last seen)
-   Moderator privileges

### ✅ Story Management

-   CRUD operations
-   Story ordering
-   Current story tracking
-   Completion with estimated points

### ✅ Voting System

-   Submit/update votes
-   Vote revelation with statistics
-   Reset voting sessions
-   Real-time vote tracking

### ✅ Data Integrity

-   Unique player names per game
-   One vote per player per story
-   Cascade deletes for data consistency
-   Validation for all inputs

## Performance Features

### ✅ Eager Loading

-   Relationships loaded efficiently
-   Count queries for statistics
-   Conditional loading with `whenLoaded`

### ✅ Database Optimization

-   Proper indexes on foreign keys
-   Scoped queries for filtering
-   Efficient relationship queries

### ✅ API Optimization

-   Resource transformers for consistent JSON
-   Minimal data transfer
-   Proper HTTP status codes

## Error Handling

### ✅ Validation Errors (422)

-   Input validation with clear messages
-   Business rule validation
-   Unique constraint validation

### ✅ Not Found Errors (404)

-   Resource existence checks
-   Relationship validation

### ✅ Business Logic Errors

-   Player name conflicts
-   Game state validation
-   Vote constraints

The backend is now fully functional and ready to integrate with the React Query frontend!
