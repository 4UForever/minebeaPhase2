define({ "api": [
  {
    "success": {
      "fields": {
        "Success 200": [
          {
            "group": "Success 200",
            "optional": false,
            "field": "varname1",
            "description": "<p>No type.</p>"
          },
          {
            "group": "Success 200",
            "type": "String",
            "optional": false,
            "field": "varname2",
            "description": "<p>With type.</p>"
          }
        ]
      }
    },
    "type": "",
    "url": "",
    "version": "0.0.0",
    "filename": "./doc/main.js",
    "group": "C__xampp_htdocs_Git_Minebea_V2_public_assets_doc_main_js",
    "groupTitle": "C__xampp_htdocs_Git_Minebea_V2_public_assets_doc_main_js",
    "name": ""
  },
  {
    "type": "get",
    "url": "/process/break-list",
    "title": "Get break list",
    "version": "0.1.0",
    "description": "<p>Get process break list</p>",
    "name": "GetBreak",
    "group": "Process",
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "qr_code",
            "description": "<p>Users unique code</p>"
          }
        ]
      }
    },
    "success": {
      "fields": {
        "Success 200": [
          {
            "group": "Success 200",
            "type": "Array",
            "optional": false,
            "field": "breaks",
            "description": "<p>list of break</p>"
          }
        ]
      },
      "examples": [
        {
          "title": "Example-Success-Response:",
          "content": "{\n    \"meta_data\": {\n        \"request_params\": {\n            \"qr_code\": \"kai02\"\n        },\n        \"success\": \"Your request has been successfully received.\",\n        \"errors\": null,\n        \"next_page\": null,\n        \"previous_page\": null\n    },\n    \"data\": {\n        \"breaks\": [\n            {\n                \"id\": 1,\n                \"code\": \"001\",\n                \"reason\": \"กินข้าว\",\n                \"flag\": 0,\n                \"created_at\": \"2016-01-18 00:00:00\",\n                \"updated_at\": \"2016-01-19 14:34:34\"\n            },\n            {\n                \"id\": 5,\n                \"code\": \"002\",\n                \"reason\": \"ไปห้องน้ำ\",\n                \"flag\": 0,\n                \"created_at\": \"2016-02-10 15:51:48\",\n                \"updated_at\": \"2016-02-10 15:51:48\"\n            }\n        ]\n    }\n}",
          "type": "json"
        }
      ]
    },
    "filename": "./example.js",
    "groupTitle": "Process",
    "sampleRequest": [
      {
        "url": "http://dev-minebea.devsenses.net/api/process/break-list"
      }
    ]
  },
  {
    "type": "get",
    "url": "/process/ng-list",
    "title": "Get NG list",
    "version": "0.1.0",
    "description": "<p>Get NG list</p>",
    "name": "GetNG",
    "group": "Process",
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "qr_code",
            "description": "<p>Users unique code</p>"
          }
        ]
      }
    },
    "success": {
      "fields": {
        "Success 200": [
          {
            "group": "Success 200",
            "type": "Array",
            "optional": false,
            "field": "ngList",
            "description": "<p>list of NG</p>"
          }
        ]
      },
      "examples": [
        {
          "title": "Example-Success-Response:",
          "content": "{\n    \"meta_data\": {\n        \"request_params\": {\n            \"qr_code\": \"kai02\"\n        },\n        \"success\": \"Your request has been successfully received.\",\n        \"errors\": null,\n        \"next_page\": null,\n        \"previous_page\": null\n    },\n    \"data\": {\n        \"process_id\": 6,\n        \"ngList\": [\n            {\n                \"id\": 3,\n                \"process_id\": 6,\n                \"title\": \"test ng-detail\",\n                \"created_at\": \"2016-01-18 08:38:38\",\n                \"updated_at\": \"2016-03-24 17:41:21\"\n            },\n            {\n                \"id\": 5,\n                \"process_id\": 6,\n                \"title\": \"test1\",\n                \"created_at\": \"2016-02-10 15:24:38\",\n                \"updated_at\": \"2016-03-24 17:41:03\"\n            }\n        ]\n    }\n}",
          "type": "json"
        }
      ]
    },
    "filename": "./example.js",
    "groupTitle": "Process",
    "sampleRequest": [
      {
        "url": "http://dev-minebea.devsenses.net/api/process/ng-list"
      }
    ]
  },
  {
    "type": "get",
    "url": "/process/get-shift-code",
    "title": "Get shift code",
    "version": "0.1.0",
    "description": "<p>Request list of shift code</p>",
    "name": "GetShiftCode",
    "group": "Process",
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "qr_code",
            "description": "<p>Users unique code</p>"
          }
        ]
      }
    },
    "success": {
      "fields": {
        "Success 200": [
          {
            "group": "Success 200",
            "type": "Number",
            "optional": false,
            "field": "id",
            "description": "<p>ID of shift code</p>"
          },
          {
            "group": "Success 200",
            "type": "String",
            "optional": false,
            "field": "label",
            "description": "<p>Label of shift code</p>"
          },
          {
            "group": "Success 200",
            "type": "String",
            "optional": false,
            "field": "time_string",
            "description": "<p>String of shift code</p>"
          }
        ]
      },
      "examples": [
        {
          "title": "Example-Success-Response:",
          "content": "{\n    \"meta_data\": {\n        \"request_params\": {\n            \"qr_code\": \"kai02\"\n        },\n        \"success\": \"Your request has successfully received.\",\n        \"errors\": null,\n        \"next_page\": null,\n        \"previous_page\": null\n    },\n    \"data\": [\n        {\n            \"id\": 1,\n            \"label\": \"A\",\n            \"time_string\": \"7:00-15:00\"\n        },\n        {\n            \"id\": 2,\n            \"label\": \"B\",\n            \"time_string\": \"15:00-23:00\"\n        },\n        {\n            \"id\": 3,\n            \"label\": \"C\",\n            \"time_string\": \"23:00-7:00\"\n        },\n        {\n            \"id\": 4,\n            \"label\": \"D\",\n            \"time_string\": \"8:00-17:00\"\n        }\n    ]\n}",
          "type": "json"
        }
      ]
    },
    "filename": "./example.js",
    "groupTitle": "Process",
    "sampleRequest": [
      {
        "url": "http://dev-minebea.devsenses.net/api/process/get-shift-code"
      }
    ]
  },
  {
    "type": "post",
    "url": "/process/process-finish",
    "title": "Finish process",
    "version": "0.1.0",
    "description": "<p>Finish process, keep finish data when process finish</p>",
    "name": "PostFinish",
    "group": "Process",
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "qr_code",
            "description": "<p>Users unique code</p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "start_time",
            "description": "<p>Start time (Format:YYYY-mm-dd HH:ii:ss)</p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "end_time",
            "description": "<p>End time (Format:YYYY-mm-dd HH:ii:ss)</p>"
          },
          {
            "group": "Parameter",
            "type": "Number",
            "optional": false,
            "field": "ok_qty",
            "description": "<p>OK Quantity</p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "last_serial_no",
            "description": "<p>Last serial number</p>"
          },
          {
            "group": "Parameter",
            "type": "Number",
            "optional": false,
            "field": "setup",
            "description": "<p>Setup value</p>"
          },
          {
            "group": "Parameter",
            "type": "Number",
            "optional": false,
            "field": "dt",
            "description": "<p>D/T value</p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "remark",
            "description": "<p>Remark if some of NG2 more than NG1 (optional)</p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "ngs",
            "description": "<p>List of NG (JSON string format)</p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "breaks",
            "description": "<p>List of break (JSON string format)</p>"
          }
        ]
      },
      "examples": [
        {
          "title": "ngs-Example:",
          "content": "[{\"ng_id\":\"1\",\"ng1\":\"5\",\"ng2\":\"4\"},{\"ng_id\":\"2\",\"ng1\":\"10\",\"ng2\":\"5\"}]",
          "type": "json"
        },
        {
          "title": "breaks-Example:",
          "content": "[{\"break_id\":\"1\",\"break_flag\":\"test break flag 1\",\"start_break\":\"2017-09-08 10:10:00\",\"end_break\":\"2017-09-08 10:20:00\"},{\"break_id\":\"5\",\"break_flag\":\"test break flag 5\",\"start_break\":\"2017-09-08 11:10:00\",\"end_break\":\"2017-09-08 11:20:00\"}]",
          "type": "json"
        }
      ]
    },
    "success": {
      "fields": {
        "Success 200": [
          {
            "group": "Success 200",
            "type": "Array",
            "optional": false,
            "field": "process_log",
            "description": "<p>Array of process_log</p>"
          }
        ]
      },
      "examples": [
        {
          "title": "Example-Success-Response:",
          "content": "{\n    \"meta_data\": {\n        \"request_params\": {\n            \"qr_code\": \"kai02\",\n            \"ok_qty\": 10,\n            \"last_serial_no\": 1000,\n            \"setup\": 2,\n            \"dt\": 4,\n            \"start_time\": \"2017-09-11 10:00:00\",\n            \"end_time\": \"2017-09-11 12:00:00\",\n            \"ngs\": \"[{\\\"ng_id\\\":\\\"1\\\",\\\"ng1\\\":\\\"5\\\",\\\"ng2\\\":\\\"4\\\"},{\\\"ng_id\\\":\\\"2\\\",\\\"ng1\\\":\\\"10\\\",\\\"ng2\\\":\\\"5\\\"}]\",\n            \"breaks\": \"[{\\\"break_id\\\":\\\"1\\\",\\\"break_flag\\\":\\\"test break flag 1\\\",\\\"start_break\\\":\\\"2017-09-08 10:10:00\\\",\\\"end_break\\\":\\\"2017-09-08 10:20:00\\\"},{\\\"break_id\\\":\\\"5\\\",\\\"break_flag\\\":\\\"test break flag 5\\\",\\\"start_break\\\":\\\"2017-09-08 11:10:00\\\",\\\"end_break\\\":\\\"2017-09-08 11:20:00\\\"}]\"\n        },\n        \"success\": \"Your request has been successfully received.\",\n        \"errors\": null,\n        \"next_page\": null,\n        \"previous_page\": null\n    },\n    \"data\": {\n        \"process_log\": {\n            \"id\": 22,\n            \"user_id\": 217,\n            \"full_name\": \"kai Test02\",\n            \"user_email\": \"kai.s@excelbangkok.com\",\n            \"line_id\": 6,\n            \"line_title\": \"15VRX Line 6\",\n            \"product_id\": 4,\n            \"product_title\": \"15VRX1003C18S\",\n            \"process_id\": 9,\n            \"process_number\": \"6-1-020-110\",\n            \"process_title\": \"Laser marking to Assembly insulator 2\",\n            \"working_date\": \"2017-09-11\",\n            \"shift_id\": 1,\n            \"shift_label\": \"A\",\n            \"shift_time\": \"7:00-15:00\",\n            \"wip_id\": 2,\n            \"wip_sort\": 1,\n            \"lot_id\": 2,\n            \"lot_number\": \"A2\",\n            \"line_leader\": 1,\n            \"line_leader_name\": \"I am admin\",\n            \"first_serial_no\": \"100\",\n            \"last_serial_no\": \"1000\",\n            \"start_time\": \"2017-09-11 10:00:00\",\n            \"end_time\": \"2017-09-11 12:00:00\",\n            \"on_break\": null,\n            \"ng1_qty\": null,\n            \"ok_qty\": \"10\",\n            \"ng_qty\": \"9\",\n            \"total_break\": \"40\",\n            \"total_minute\": 120,\n            \"setup\": \"2\",\n            \"dt\": \"4\",\n            \"created_at\": \"2017-09-11 15:36:17\",\n            \"updated_at\": \"2017-09-11 17:09:28\",\n            \"remark\": null\n        }\n    }\n}",
          "type": "json"
        }
      ]
    },
    "filename": "./example.js",
    "groupTitle": "Process",
    "sampleRequest": [
      {
        "url": "http://dev-minebea.devsenses.net/api/process/process-finish"
      }
    ]
  },
  {
    "type": "get",
    "url": "/user/login",
    "title": "Login",
    "version": "0.1.0",
    "description": "<p>Login with QR code</p>",
    "name": "PostLogin",
    "group": "Process",
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "qr_code",
            "description": "<p>Users unique code</p>"
          }
        ]
      }
    },
    "success": {
      "fields": {
        "Success 200": [
          {
            "group": "Success 200",
            "type": "Array",
            "optional": false,
            "field": "data",
            "description": "<p>User's profile</p>"
          },
          {
            "group": "Success 200",
            "type": "Array",
            "optional": false,
            "field": "data.models",
            "description": "<p>User's model</p>"
          },
          {
            "group": "Success 200",
            "type": "Array",
            "optional": false,
            "field": "shifts",
            "description": "<p>Shift (for all users)</p>"
          }
        ]
      },
      "examples": [
        {
          "title": "Example-Success-Response:",
          "content": "{\n    \"meta_data\": {\n        \"request_params\": {\n            \"qr_code\": \"kai02\",\n            \"working_date\": \"2017-09-05\",\n            \"shift_id\": 1\n        },\n        \"success\": \"You are successfully logged-in\",\n        \"errors\": null,\n        \"next_page\": null,\n        \"previous_page\": null\n    },\n    \"data\": {\n        \"id\": 217,\n        \"email\": \"kai.s@excelbangkok.com\",\n        \"qr_code\": \"kai02\",\n        \"last_login\": \"2017-09-06 09:51:14\",\n        \"last_logout\": \"0000-00-00 00:00:00\",\n        \"first_name\": \"kai\",\n        \"last_name\": \"sawai\",\n        \"leader\": 0,\n        \"on_process\": 19,\n        \"working_process\": 9,\n        \"created_at\": \"2017-03-07 16:43:43\",\n        \"updated_at\": \"2017-09-06 09:51:14\",\n        \"groups\": [\n            {\n                \"id\": 2,\n                \"name\": \"Production (Staff)\",\n                \"created_at\": \"2015-06-05 06:15:05\",\n                \"updated_at\": \"2015-06-08 03:25:01\"\n            }\n        ],\n        \"permissions\": {\n            \"work_on_model_processes\": 1,\n            \"view_documents\": 1\n        },\n        \"models\": [\n            {\n                \"id\": 4,\n                \"title\": \"15VRX1003C18S\",\n                \"created_at\": \"2015-06-08 03:26:38\",\n                \"updated_at\": \"2015-06-08 03:26:38\",\n                \"lines\": [\n                    {\n                        \"id\": 3,\n                        \"title\": \"15VRX Line 3\",\n                        \"created_at\": \"2015-06-05 06:15:06\",\n                        \"updated_at\": \"2015-06-08 03:34:17\",\n                        \"processes\": [\n                            {\n                                \"id\": 6,\n                                \"number\": \"3-1-020-110\",\n                                \"title\": \"Laser marking to Assembly insulator 1\",\n                                \"created_at\": \"2015-06-08 03:32:04\",\n                                \"updated_at\": \"2015-06-08 03:32:04\"\n                            }\n                        ]\n                    },\n                    {\n                        \"id\": 4,\n                        \"title\": \"15VRX Line 4\",\n                        \"created_at\": \"2015-06-08 03:35:34\",\n                        \"updated_at\": \"2015-06-08 03:35:34\",\n                        \"processes\": [\n                            {\n                                \"id\": 7,\n                                \"number\": \"4-1-020-110\",\n                                \"title\": \"Laser marking to Assembly insulator 2\",\n                                \"created_at\": \"2015-06-08 03:35:34\",\n                                \"updated_at\": \"2015-06-08 04:07:36\"\n                            }\n                        ]\n                    },\n                    {\n                        \"id\": 6,\n                        \"title\": \"15VRX Line 6\",\n                        \"created_at\": \"2015-06-08 03:37:51\",\n                        \"updated_at\": \"2015-06-08 03:37:51\",\n                        \"processes\": [\n                            {\n                                \"id\": 9,\n                                \"number\": \"6-1-020-110\",\n                                \"title\": \"Laser marking to Assembly insulator 2\",\n                                \"created_at\": \"2015-06-08 03:37:51\",\n                                \"updated_at\": \"2015-06-08 03:44:51\"\n                            }\n                        ]\n                    }\n                ]\n            },\n            {\n                \"id\": 6,\n                \"title\": \"15VRX1003C40S\",\n                \"created_at\": \"2015-06-08 03:27:28\",\n                \"updated_at\": \"2015-06-08 03:27:28\",\n                \"lines\": [\n                    {\n                        \"id\": 2,\n                        \"title\": \"15VRX Line 2\",\n                        \"created_at\": \"2015-06-05 06:15:06\",\n                        \"updated_at\": \"2015-06-08 03:27:28\",\n                        \"processes\": [\n                            {\n                                \"id\": 5,\n                                \"number\": \"2-1-020-020\",\n                                \"title\": \"Laser marking\",\n                                \"created_at\": \"2015-06-05 06:15:08\",\n                                \"updated_at\": \"2015-06-08 03:34:00\"\n                            }\n                        ]\n                    }\n                ]\n            },\n            {\n                \"id\": 7,\n                \"title\": \"15VRX1003C55S\",\n                \"created_at\": \"2015-06-08 03:32:04\",\n                \"updated_at\": \"2015-06-08 03:32:04\",\n                \"lines\": [\n                    {\n                        \"id\": 3,\n                        \"title\": \"15VRX Line 3\",\n                        \"created_at\": \"2015-06-05 06:15:06\",\n                        \"updated_at\": \"2015-06-08 03:34:17\",\n                        \"processes\": [\n                            {\n                                \"id\": 6,\n                                \"number\": \"3-1-020-110\",\n                                \"title\": \"Laser marking to Assembly insulator 1\",\n                                \"created_at\": \"2015-06-08 03:32:04\",\n                                \"updated_at\": \"2015-06-08 03:32:04\"\n                            }\n                        ]\n                    },\n                    {\n                        \"id\": 4,\n                        \"title\": \"15VRX Line 4\",\n                        \"created_at\": \"2015-06-08 03:35:34\",\n                        \"updated_at\": \"2015-06-08 03:35:34\",\n                        \"processes\": [\n                            {\n                                \"id\": 7,\n                                \"number\": \"4-1-020-110\",\n                                \"title\": \"Laser marking to Assembly insulator 2\",\n                                \"created_at\": \"2015-06-08 03:35:34\",\n                                \"updated_at\": \"2015-06-08 04:07:36\"\n                            }\n                        ]\n                    },\n                    {\n                        \"id\": 6,\n                        \"title\": \"15VRX Line 6\",\n                        \"created_at\": \"2015-06-08 03:37:51\",\n                        \"updated_at\": \"2015-06-08 03:37:51\",\n                        \"processes\": [\n                            {\n                                \"id\": 9,\n                                \"number\": \"6-1-020-110\",\n                                \"title\": \"Laser marking to Assembly insulator 2\",\n                                \"created_at\": \"2015-06-08 03:37:51\",\n                                \"updated_at\": \"2015-06-08 03:44:51\"\n                            }\n                        ]\n                    }\n                ]\n            }\n        ],\n        \"shifts\": [\n            {\n                \"id\": 1,\n                \"label\": \"A\",\n                \"time_string\": \"7:00-15:00\"\n            },\n            {\n                \"id\": 2,\n                \"label\": \"B\",\n                \"time_string\": \"15:00-23:00\"\n            },\n            {\n                \"id\": 3,\n                \"label\": \"C\",\n                \"time_string\": \"23:00-7:00\"\n            },\n            {\n                \"id\": 4,\n                \"label\": \"D\",\n                \"time_string\": \"8:00-17:00\"\n            },\n            {\n                \"id\": 5,\n                \"label\": \"M\",\n                \"time_string\": \"7.00-19.00\"\n            },\n            {\n                \"id\": 6,\n                \"label\": \"N\",\n                \"time_string\": \"19.00-7.00\"\n            },\n            {\n                \"id\": 7,\n                \"label\": \"Ao\",\n                \"time_string\": \"7.00-19.00\"\n            },\n            {\n                \"id\": 8,\n                \"label\": \"Co\",\n                \"time_string\": \"19.00-7.00\"\n            }\n        ]\n    }\n}",
          "type": "json"
        }
      ]
    },
    "filename": "./example.js",
    "groupTitle": "Process",
    "sampleRequest": [
      {
        "url": "http://dev-minebea.devsenses.net/api/user/login"
      }
    ]
  },
  {
    "type": "post",
    "url": "/process/model-data",
    "title": "Model data",
    "version": "0.1.0",
    "description": "<p>Create process_log ID to start working</p>",
    "name": "SetModelData",
    "group": "Process",
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "qr_code",
            "description": "<p>Users unique code</p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "working_date",
            "description": "<p>Working date (Format:YYYY-mm-dd HH:ii:ss)</p>"
          },
          {
            "group": "Parameter",
            "type": "Number",
            "optional": false,
            "field": "shift_id",
            "description": "<p>Working shift ID</p>"
          },
          {
            "group": "Parameter",
            "type": "Number",
            "optional": false,
            "field": "line_id",
            "description": "<p>Working line ID</p>"
          },
          {
            "group": "Parameter",
            "type": "Number",
            "optional": false,
            "field": "model_id",
            "description": "<p>Working model ID</p>"
          },
          {
            "group": "Parameter",
            "type": "Number",
            "optional": false,
            "field": "process_id",
            "description": "<p>Working process ID</p>"
          }
        ]
      }
    },
    "success": {
      "fields": {
        "Success 200": [
          {
            "group": "Success 200",
            "type": "Array",
            "optional": false,
            "field": "process_log",
            "description": "<p>Array of process_log</p>"
          }
        ]
      },
      "examples": [
        {
          "title": "Example-Success-Response:",
          "content": "{\n    \"meta_data\": {\n        \"request_params\": {\n            \"qr_code\": \"kai02\",\n            \"working_date\": \"2017-08-30 10:00:00\",\n            \"shift_id\": \"1\",\n            \"line_id\": \"6\",\n            \"model_id\": \"4\",\n            \"process_id\": \"9\"\n        },\n        \"success\": \"Your request has successfully received.\",\n        \"errors\": null,\n        \"next_page\": null,\n        \"previous_page\": null\n    },\n    \"data\": {\n        \"process_log\": {\n            \"user_id\": 217,\n            \"full_name\": \"kai sawai\",\n            \"user_email\": \"kai.s@excelbangkok.com\",\n            \"line_id\": 6,\n            \"line_title\": \"15VRX Line 6\",\n            \"product_id\": 4,\n            \"product_title\": \"15VRX1003C18S\",\n            \"process_id\": 9,\n            \"process_number\": \"6-1-020-110\",\n            \"process_title\": \"Laser marking to Assembly insulator 2\",\n            \"working_date\": \"2017-08-30 10:00:00\",\n            \"shift_id\": 1,\n            \"shift_label\": \"A\",\n            \"shift_time\": \"7:00-15:00\",\n            \"wip_id\": 2,\n            \"wip_sort\": 1,\n            \"updated_at\": \"2017-09-01 17:22:06\",\n            \"created_at\": \"2017-09-01 17:22:06\",\n            \"id\": 19\n        }\n    }\n}",
          "type": "json"
        }
      ]
    },
    "filename": "./example.js",
    "groupTitle": "Process",
    "sampleRequest": [
      {
        "url": "http://dev-minebea.devsenses.net/api/process/model-data"
      }
    ]
  },
  {
    "type": "get",
    "url": "/user/:id",
    "title": "Create a new User",
    "version": "0.1.0",
    "description": "<p>Request User information</p>",
    "name": "GetUser",
    "group": "Test",
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "Number",
            "optional": false,
            "field": "id",
            "description": "<p>Users unique ID.</p>"
          }
        ]
      }
    },
    "success": {
      "fields": {
        "Success 200": [
          {
            "group": "Success 200",
            "type": "String",
            "optional": false,
            "field": "firstname",
            "description": "<p>Firstname of the User.</p>"
          },
          {
            "group": "Success 200",
            "type": "String",
            "optional": false,
            "field": "lastname",
            "description": "<p>Lastname of the User.</p>"
          }
        ]
      },
      "examples": [
        {
          "title": "Example-Success-Response:",
          "content": "HTTP/1.1 200 OK\n{\n  \"firstname\": \"John\",\n  \"lastname\": \"Doe\"\n}",
          "type": "json"
        }
      ]
    },
    "error": {
      "fields": {
        "Error 4xx": [
          {
            "group": "Error 4xx",
            "optional": false,
            "field": "UserNotFound",
            "description": "<p>The id of the User was not found.</p>"
          }
        ]
      },
      "examples": [
        {
          "title": "Example-Error-Response:",
          "content": "HTTP/1.1 404 Not Found\n{\n  \"error\": \"UserNotFound\"\n}",
          "type": "json"
        }
      ]
    },
    "filename": "./example.js",
    "groupTitle": "Test",
    "sampleRequest": [
      {
        "url": "http://dev-minebea.devsenses.net/api/user/:id"
      }
    ]
  }
] });
