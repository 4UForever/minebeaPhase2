/**
@api {get} /user/:id Create a new User
@apiVersion 0.1.0
@apiDescription Request User information
@apiName GetUser
@apiGroup Test
@apiParam {Number} id Users unique ID.
@apiSuccess {String} firstname Firstname of the User.
@apiSuccess {String} lastname  Lastname of the User.
@apiSuccessExample Example-Success-Response:
    HTTP/1.1 200 OK
    {
      "firstname": "John",
      "lastname": "Doe"
    }
@apiError UserNotFound The id of the User was not found.
@apiErrorExample Example-Error-Response:
    HTTP/1.1 404 Not Found
    {
      "error": "UserNotFound"
    }
*/
/**
@api {get} /process/get-shift-code Get shift code
@apiVersion 0.1.0
@apiDescription Request list of shift code
@apiName GetShiftCode
@apiGroup Process
@apiParam {String} qr_code Users unique code
@apiSuccess {Number} id ID of shift code
@apiSuccess {String} label Label of shift code
@apiSuccess {String} time_string String of shift code
@apiSuccessExample Example-Success-Response:
{
    "meta_data": {
        "request_params": {
            "qr_code": "kai02"
        },
        "success": "Your request has successfully received.",
        "errors": null,
        "next_page": null,
        "previous_page": null
    },
    "data": [
        {
            "id": 1,
            "label": "A",
            "time_string": "7:00-15:00"
        },
        {
            "id": 2,
            "label": "B",
            "time_string": "15:00-23:00"
        },
        {
            "id": 3,
            "label": "C",
            "time_string": "23:00-7:00"
        },
        {
            "id": 4,
            "label": "D",
            "time_string": "8:00-17:00"
        }
    ]
}
*/
/**
@api {post} /process/model-data Model data
@apiVersion 0.1.0
@apiDescription Create process_log ID to start working
@apiName SetModelData
@apiGroup Process
@apiParam {String} qr_code Users unique code
@apiParam {Number} shift_id Working shift ID
@apiParam {Number} line_id Working line ID
@apiParam {Number} model_id Working model ID
@apiParam {Number} process_id Working process ID
@apiSuccess {Number} process_log_id ID of process_log
@apiSuccessExample Example-Success-Response:
{
    "meta_data": {
        "request_params": {
            "qr_code": "kai02",
            "shift_id": "2",
            "line_id": "3",
            "model_id": "4",
            "process_id": "6"
        },
        "success": "Your request has been successfully submitted.",
        "errors": null,
        "next_page": null,
        "previous_page": null
    },
    "data": {
        "process_log_id": 18
    }
}
*/
/**
@api {get} /process/break-list Get break list
@apiVersion 0.1.0
@apiDescription Get process break list
@apiName GetBreak
@apiGroup Process
@apiParam {String} qr_code Users unique code
@apiSuccess {Array} breaks list of break
@apiSuccessExample Example-Success-Response:
{
    "meta_data": {
        "request_params": {
            "qr_code": "kai02"
        },
        "success": "Your request has been successfully received.",
        "errors": null,
        "next_page": null,
        "previous_page": null
    },
    "data": {
        "breaks": [
            {
                "id": 1,
                "code": "001",
                "reason": "กินข้าว",
                "flag": 0,
                "created_at": "2016-01-18 00:00:00",
                "updated_at": "2016-01-19 14:34:34"
            },
            {
                "id": 5,
                "code": "002",
                "reason": "ไปห้องน้ำ",
                "flag": 0,
                "created_at": "2016-02-10 15:51:48",
                "updated_at": "2016-02-10 15:51:48"
            }
        ]
    }
}
*/