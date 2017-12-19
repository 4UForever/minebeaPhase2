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
@apiParam {String} working_date Working date (Format:YYYY-mm-dd HH:ii:ss)
@apiParam {Number} shift_id Working shift ID
@apiParam {Number} line_id Working line ID
@apiParam {Number} model_id Working model ID
@apiParam {Number} process_id Working process ID
@apiParam {Number} process_log_from Process log from (In case continue working process. If not, add empty value)
@apiSuccess {Array} process_log Array of process_log
@apiSuccessExample Example-Success-Response:
{
    "meta_data": {
        "request_params": {
            "qr_code": "kai02",
            "working_date": "2017-08-30 10:00:00",
            "shift_id": "1",
            "line_id": "6",
            "model_id": "4",
            "process_id": "9"
        },
        "success": "Your request has successfully received.",
        "errors": null,
        "next_page": null,
        "previous_page": null
    },
    "data": {
        "process_log": {
            "user_id": 217,
            "full_name": "kai sawai",
            "user_email": "kai.s@excelbangkok.com",
            "line_id": 6,
            "line_title": "15VRX Line 6",
            "product_id": 4,
            "product_title": "15VRX1003C18S",
            "process_id": 9,
            "process_number": "6-1-020-110",
            "process_title": "Laser marking to Assembly insulator 2",
            "working_date": "2017-08-30 10:00:00",
            "shift_id": 1,
            "shift_label": "A",
            "shift_time": "7:00-15:00",
            "wip_id": 2,
            "wip_sort": 1,
            "updated_at": "2017-09-01 17:22:06",
            "created_at": "2017-09-01 17:22:06",
            "id": 19
        }
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
/**
@api {get} /process/ng-list Get NG list
@apiVersion 0.1.0
@apiDescription Get NG list
@apiName GetNG
@apiGroup Process
@apiParam {String} qr_code Users unique code
@apiSuccess {Array} ngList list of NG
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
        "process_id": 6,
        "ngList": [
            {
                "id": 3,
                "process_id": 6,
                "title": "test ng-detail",
                "created_at": "2016-01-18 08:38:38",
                "updated_at": "2016-03-24 17:41:21"
            },
            {
                "id": 5,
                "process_id": 6,
                "title": "test1",
                "created_at": "2016-02-10 15:24:38",
                "updated_at": "2016-03-24 17:41:03"
            }
        ]
    }
}
*/

/**
@api {get} /user/login Login
@apiVersion 0.1.0
@apiDescription Login with QR code
@apiName PostLogin
@apiGroup Process
@apiParam {String} qr_code Users unique code
@apiSuccess {Array} data User's profile
@apiSuccess {Array} data.models User's model
@apiSuccess {Array} shifts Shift (for all users)
@apiSuccessExample Example-Success-Response:
{
    "meta_data": {
        "request_params": {
            "qr_code": "kai02",
            "working_date": "2017-09-05",
            "shift_id": 1
        },
        "success": "You are successfully logged-in",
        "errors": null,
        "next_page": null,
        "previous_page": null
    },
    "data": {
        "id": 217,
        "email": "kai.s@excelbangkok.com",
        "qr_code": "kai02",
        "last_login": "2017-09-06 09:51:14",
        "last_logout": "0000-00-00 00:00:00",
        "first_name": "kai",
        "last_name": "sawai",
        "leader": 0,
        "on_process": 19,
        "working_process": 9,
        "created_at": "2017-03-07 16:43:43",
        "updated_at": "2017-09-06 09:51:14",
        "groups": [
            {
                "id": 2,
                "name": "Production (Staff)",
                "created_at": "2015-06-05 06:15:05",
                "updated_at": "2015-06-08 03:25:01"
            }
        ],
        "permissions": {
            "work_on_model_processes": 1,
            "view_documents": 1
        },
        "models": [
            {
                "id": 4,
                "title": "15VRX1003C18S",
                "created_at": "2015-06-08 03:26:38",
                "updated_at": "2015-06-08 03:26:38",
                "lines": [
                    {
                        "id": 3,
                        "title": "15VRX Line 3",
                        "created_at": "2015-06-05 06:15:06",
                        "updated_at": "2015-06-08 03:34:17",
                        "processes": [
                            {
                                "id": 6,
                                "number": "3-1-020-110",
                                "title": "Laser marking to Assembly insulator 1",
                                "created_at": "2015-06-08 03:32:04",
                                "updated_at": "2015-06-08 03:32:04"
                            }
                        ]
                    },
                    {
                        "id": 4,
                        "title": "15VRX Line 4",
                        "created_at": "2015-06-08 03:35:34",
                        "updated_at": "2015-06-08 03:35:34",
                        "processes": [
                            {
                                "id": 7,
                                "number": "4-1-020-110",
                                "title": "Laser marking to Assembly insulator 2",
                                "created_at": "2015-06-08 03:35:34",
                                "updated_at": "2015-06-08 04:07:36"
                            }
                        ]
                    },
                    {
                        "id": 6,
                        "title": "15VRX Line 6",
                        "created_at": "2015-06-08 03:37:51",
                        "updated_at": "2015-06-08 03:37:51",
                        "processes": [
                            {
                                "id": 9,
                                "number": "6-1-020-110",
                                "title": "Laser marking to Assembly insulator 2",
                                "created_at": "2015-06-08 03:37:51",
                                "updated_at": "2015-06-08 03:44:51"
                            }
                        ]
                    }
                ]
            },
            {
                "id": 6,
                "title": "15VRX1003C40S",
                "created_at": "2015-06-08 03:27:28",
                "updated_at": "2015-06-08 03:27:28",
                "lines": [
                    {
                        "id": 2,
                        "title": "15VRX Line 2",
                        "created_at": "2015-06-05 06:15:06",
                        "updated_at": "2015-06-08 03:27:28",
                        "processes": [
                            {
                                "id": 5,
                                "number": "2-1-020-020",
                                "title": "Laser marking",
                                "created_at": "2015-06-05 06:15:08",
                                "updated_at": "2015-06-08 03:34:00"
                            }
                        ]
                    }
                ]
            },
            {
                "id": 7,
                "title": "15VRX1003C55S",
                "created_at": "2015-06-08 03:32:04",
                "updated_at": "2015-06-08 03:32:04",
                "lines": [
                    {
                        "id": 3,
                        "title": "15VRX Line 3",
                        "created_at": "2015-06-05 06:15:06",
                        "updated_at": "2015-06-08 03:34:17",
                        "processes": [
                            {
                                "id": 6,
                                "number": "3-1-020-110",
                                "title": "Laser marking to Assembly insulator 1",
                                "created_at": "2015-06-08 03:32:04",
                                "updated_at": "2015-06-08 03:32:04"
                            }
                        ]
                    },
                    {
                        "id": 4,
                        "title": "15VRX Line 4",
                        "created_at": "2015-06-08 03:35:34",
                        "updated_at": "2015-06-08 03:35:34",
                        "processes": [
                            {
                                "id": 7,
                                "number": "4-1-020-110",
                                "title": "Laser marking to Assembly insulator 2",
                                "created_at": "2015-06-08 03:35:34",
                                "updated_at": "2015-06-08 04:07:36"
                            }
                        ]
                    },
                    {
                        "id": 6,
                        "title": "15VRX Line 6",
                        "created_at": "2015-06-08 03:37:51",
                        "updated_at": "2015-06-08 03:37:51",
                        "processes": [
                            {
                                "id": 9,
                                "number": "6-1-020-110",
                                "title": "Laser marking to Assembly insulator 2",
                                "created_at": "2015-06-08 03:37:51",
                                "updated_at": "2015-06-08 03:44:51"
                            }
                        ]
                    }
                ]
            }
        ],
        "shifts": [
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
            },
            {
                "id": 5,
                "label": "M",
                "time_string": "7.00-19.00"
            },
            {
                "id": 6,
                "label": "N",
                "time_string": "19.00-7.00"
            },
            {
                "id": 7,
                "label": "Ao",
                "time_string": "7.00-19.00"
            },
            {
                "id": 8,
                "label": "Co",
                "time_string": "19.00-7.00"
            }
        ]
    }
}
*/

/**
@api {post} /process/process-finish Finish process
@apiVersion 0.1.0
@apiDescription Finish process, keep finish data when process finish
@apiName PostFinish
@apiGroup Process
@apiParam {String} qr_code Users unique code
@apiParam {String} start_time Start time (Format:YYYY-mm-dd HH:ii:ss)
@apiParam {String} end_time End time (Format:YYYY-mm-dd HH:ii:ss)
@apiParam {Number} ok_qty OK Quantity
@apiParam {String} last_serial_no Last serial number
@apiParam {Number} setup Setup value
@apiParam {Number} dt D/T value
@apiParam {String} remark Remark if some of NG2 more than NG1 (optional)
@apiParam {Number} wip_qty WIP quantity if working in last lot of this shift (optional)
@apiParam {Boolean} is_continue Can be continue to another process-log (optional)
@apiParam {String} ngs List of NG (JSON string format)
@apiParam {String} breaks List of break (JSON string format)
@apiParamExample {json} ngs-Example:
[{"ng_id":"1","ng_serial":"x00101","ng1":true,"ng2":false},{"ng_id":"2","ng_serial":"x00102","ng1":true,"ng2":true}]
@apiParamExample {json} breaks-Example:
[{"break_id":"1","break_flag":"test break flag 1","start_break":"2017-09-08 10:10:00","end_break":"2017-09-08 10:20:00"},{"break_id":"5","break_flag":"test break flag 5","start_break":"2017-09-08 11:10:00","end_break":"2017-09-08 11:20:00"}]
@apiSuccess {Array} process_log Array of process_log
@apiSuccessExample Example-Success-Response:
{
    "meta_data": {
        "request_params": {
            "qr_code": "kai02",
            "ok_qty": 10,
            "last_serial_no": 1000,
            "setup": 2,
            "dt": 4,
            "start_time": "2017-09-11 10:00:00",
            "end_time": "2017-09-11 12:00:00",
            "ngs": "[{\"ng_id\":\"1\",\"ng1\":\"5\",\"ng2\":\"4\"},{\"ng_id\":\"2\",\"ng1\":\"10\",\"ng2\":\"5\"}]",
            "breaks": "[{\"break_id\":\"1\",\"break_flag\":\"test break flag 1\",\"start_break\":\"2017-09-08 10:10:00\",\"end_break\":\"2017-09-08 10:20:00\"},{\"break_id\":\"5\",\"break_flag\":\"test break flag 5\",\"start_break\":\"2017-09-08 11:10:00\",\"end_break\":\"2017-09-08 11:20:00\"}]"
        },
        "success": "Your request has been successfully received.",
        "errors": null,
        "next_page": null,
        "previous_page": null
    },
    "data": {
        "process_log": {
            "id": 22,
            "user_id": 217,
            "full_name": "kai Test02",
            "user_email": "kai.s@excelbangkok.com",
            "line_id": 6,
            "line_title": "15VRX Line 6",
            "product_id": 4,
            "product_title": "15VRX1003C18S",
            "process_id": 9,
            "process_number": "6-1-020-110",
            "process_title": "Laser marking to Assembly insulator 2",
            "working_date": "2017-09-11",
            "shift_id": 1,
            "shift_label": "A",
            "shift_time": "7:00-15:00",
            "wip_id": 2,
            "wip_sort": 1,
            "lot_id": 2,
            "lot_number": "A2",
            "line_leader": 1,
            "line_leader_name": "I am admin",
            "first_serial_no": "100",
            "last_serial_no": "1000",
            "start_time": "2017-09-11 10:00:00",
            "end_time": "2017-09-11 12:00:00",
            "on_break": null,
            "ng1_qty": null,
            "ok_qty": "10",
            "ng_qty": "9",
            "total_break": "40",
            "total_minute": 120,
            "setup": "2",
            "dt": "4",
            "created_at": "2017-09-11 15:36:17",
            "updated_at": "2017-09-11 17:09:28",
            "remark": null
        }
    }
}
*/

/**
@api {post} /process/check-input-lot Post check input lot
@apiVersion 0.1.0
@apiDescription Post check input lot
@apiName PostCheckInput
@apiGroup Process
@apiParam {String} qr_code Users unique code
@apiParam {String} parts List of input parts (JSON string format)
@apiParam {String} wip_lots List of input wip lots (JSON string format)
@apiSuccess {Array} process_log Array of process_log
@apiSuccess {Array} lots Array of lot data available
@apiSuccess {Array} lots.lot_data Array of lot data
@apiSuccess {Array} lots.lot_data.first_serial_no First serial number
@apiSuccess {Array} lots.lot_data.last_serial_no Last serial number
@apiSuccessExample Example-Success-Response:
{
    "meta_data": {
        "request_params": {
            "qr_code": "kai02",
            "wip_lots": "[]",
            "parts": "[{\"number\":\"4011000525\",\"iqc_lots\":[]},{\"number\":\"4011000524\",\"iqc_lots\":[]}]"
        },
        "success": "Your request has been successfully received.",
        "errors": null,
        "next_page": null,
        "previous_page": null
    },
    "data": {
        "process_log": {
            "id": 26,
            "user_id": 217,
            "user_email": "kai.s@excelbangkok.com",
            "line_id": 3,
            "line_title": "15VRX Line 3",
            "model_id": 4,
            "model_title": "15VRX1003C18S",
            "process_id": 45,
            "process_number": "3-1-210-220",
            "process_title": "Apply varnish to Dryheat",
            "wip_id": 1,
            "wip_sort": 2
        },
        "lots": {
            "input_lot_number": false,
            "lot_data": [
                {
                    "id": 3,
                    "wip_id": 1,
                    "wip_title": "test",
                    "number": "test1",
                    "quantity": null,
                    "created_at": "2017-09-13 09:54:55",
                    "updated_at": "2017-09-13 09:54:55",
                    "first_serial_no": "1",
                    "last_serial_no": "3"
                }
            ]
        }
    }
}
*/

/**
@api {get} /process/get-continue-process Get continue process
@apiVersion 0.1.0
@apiDescription Get can continue process
@apiName GetContinue
@apiGroup Process
@apiParam {String} qr_code Users unique code
@apiSuccess {Array} process_log Array of process_log
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
            "process_log_from": 28,
            "line_id": 6,
            "line_title": "15VRX Line 6",
            "product_id": 4,
            "product_title": "15VRX1003C18S",
            "process_id": 10,
            "process_number": "6-1-120-165",
            "process_title": "Assembly insulator 1 to Output from conveyer",
            "wip_id": 2,
            "wip_sort": 2,
            "lot_id": 2,
            "lot_number": "A2",
            "line_leader": 6
        }
    ]
}
*/
