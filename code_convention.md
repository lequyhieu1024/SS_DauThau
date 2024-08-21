# Code Convention for RESTful API Back End in Laravel

## Response Format

### Success Response (200)
```json
{
    "result": true,
    "message": "Success message",
    "data": {
        // your data here
    }
}
```

### Error Response (400)
```json
{
    "result": false,
    "message": "Error message",
    "errors": {
        "field_name_01": [
            "Error message 1",
            "Error message 2"
        ],
        "field_name_02": [
            "Error message 1",
            "Error message 2"
        ]
    }
}
```
