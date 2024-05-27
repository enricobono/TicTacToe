# TicTacToe

This repo contains a demo TicTacToe application.

Stack:

- Symfony 7 as API backend server
- PHP 8.3

A full game is performed in the `tests/EndToEnd/FullGameTest.php` test.


## Installation

- Copy `.env.example` to `.env`
- Run `docker compose build --no-cache` to build fresh images
- Run `docker compose up --pull always -d --wait` to set up and start a fresh Symfony project

You can now interact with the API using your preferred client (CURL, Postman, ...) pointing to `https://localhost`.


## Everyday use

- Start the application using `make start`
- Stop the application using `make stop`
- Test the application using `make test`


## APIs


### Create a game

**Request**: in order to start a game, perform a POST request to:

    http://localhost/games


**Response**: the response will include the `id` of the new game as well as all the data needed by the client:

```json
{
    "data": {
        "id": "018fbab3-e450-73d6-a8f3-60aaed82fbad",
        "cells": [
            [
                null,
                null,
                null
            ],
            [
                null,
                null,
                null
            ],
            [
                null,
                null,
                null
            ]
        ],
        "isWon": false,
        "winner": null,
        "lastPlayer": null
    }
}
```
where:
- `cells`: represents the full board status (`null` if the cell is still empty, `1` or `2` if the cell has already been played)
- `isWon`: is a boolean showing if one of the two players won the game
- `winner`: is `null` if nobody won the game yet, or an integer indicating the winner
- `lastPlayer`: is `null` if no move has been played, or an integer indicating the player who played the last move


### Get info about a game

**Request**: just perform a GET request to:

    http://localhost/games/{id}


**Response**: the API will return the same response documented above.


### Play a move

**Request**: just perform a PATCH request to:

    http://localhost/games/{id}


**Response**: the JSON request body will need to include the following fields:

```json
{
    "row": <int>,
    "col": <int>,
    "player": <int>
}
```
where:
- `row` is an integer between 0 and 2
- `col` is an integer between 0 and 2
- `player` is an integer between 1 and 2


**Response**: the API will return the same response as above, with the updated data:

```json
{
    "data": {
        "id": "018fbab3-e450-73d6-a8f3-60aaed82fbad",
        "cells": [
            [
                1,
                2,
                null
            ],
            [
                1,
                null,
                2
            ],
            [
                1,
                null,
                null
            ]
        ],
        "isWon": true,
        "winner": 1,
        "lastPlayer": 1
    }
}
```

### Errors

Any error will be reported with the following structure:

```json
{
    "error": "This cell is already taken."
}
```
