define({ "api": [
  {
    "type": "post",
    "url": "/activity/process/end",
    "title": "End a process",
    "name": "End_a_process",
    "group": "Activity",
    "version": "0.1.0",
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "qr_code",
            "description": "<p>A QR code retrieve by a scanner.</p> "
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "comment",
            "description": "<p>A comment for an end process activity.</p> "
          },
          {
            "group": "Parameter",
            "type": "Number",
            "optional": false,
            "field": "line_id",
            "description": "<p>A production line a process is on.</p> "
          },
          {
            "group": "Parameter",
            "type": "Number",
            "optional": false,
            "field": "process_id",
            "description": "<p>A process of an activity.</p> "
          },
          {
            "group": "Parameter",
            "type": "Number",
            "optional": false,
            "field": "product_id",
            "description": "<p>A product of an activity.</p> "
          }
        ]
      }
    },
    "filename": "/var/www/api_docs/projects/minebea/example.js",
    "groupTitle": "Activity",
    "sampleRequest": [
      {
        "url": "http://staging-minebea.devsenses.net/api/activity/process/end"
      }
    ],
    "success": {
      "fields": {
        "Success 200": [
          {
            "group": "Success 200",
            "type": "Number",
            "optional": false,
            "field": "id",
            "description": "<p>An activity identifier</p> "
          },
          {
            "group": "Success 200",
            "type": "String",
            "optional": false,
            "field": "type",
            "description": "<p>A type of an activity</p> "
          },
          {
            "group": "Success 200",
            "type": "String",
            "optional": false,
            "field": "comment",
            "description": "<p>A comment for an end process activity (Start process activity does not have a comment)</p> "
          },
          {
            "group": "Success 200",
            "type": "String",
            "optional": false,
            "field": "created_at",
            "description": "<p>An activity creation&#39;s timestamp</p> "
          },
          {
            "group": "Success 200",
            "type": "String",
            "optional": false,
            "field": "updated_at",
            "description": "<p>An activity modification&#39;s timestamp</p> "
          },
          {
            "group": "Success 200",
            "type": "object",
            "optional": false,
            "field": "user",
            "description": "<p>A user who perform this activity</p> "
          },
          {
            "group": "Success 200",
            "type": "Number",
            "optional": false,
            "field": "user.id",
            "description": "<p>A user identifier</p> "
          },
          {
            "group": "Success 200",
            "type": "String",
            "optional": false,
            "field": "user.first_name",
            "description": "<p>A first name of a user</p> "
          },
          {
            "group": "Success 200",
            "type": "String",
            "optional": false,
            "field": "user.last_name",
            "description": "<p>A last name of a user</p> "
          },
          {
            "group": "Success 200",
            "type": "String",
            "optional": false,
            "field": "user.last_login",
            "description": "<p>A user login&#39;s timestamp</p> "
          },
          {
            "group": "Success 200",
            "type": "String",
            "optional": false,
            "field": "user.last_logout",
            "description": "<p>A user logout&#39;s timestamp</p> "
          },
          {
            "group": "Success 200",
            "type": "String",
            "optional": false,
            "field": "user.persist_code",
            "description": "<p>Does not have its used</p> "
          },
          {
            "group": "Success 200",
            "type": "String",
            "optional": false,
            "field": "user.created_at",
            "description": "<p>A user creation&#39;s timestamp</p> "
          },
          {
            "group": "Success 200",
            "type": "String",
            "optional": false,
            "field": "user.updated_at",
            "description": "<p>A user modification&#39;s timestamp</p> "
          },
          {
            "group": "Success 200",
            "type": "object",
            "optional": false,
            "field": "line",
            "description": "<p>A production line that an activity is on</p> "
          },
          {
            "group": "Success 200",
            "type": "Number",
            "optional": false,
            "field": "line.id",
            "description": "<p>A production line identifier</p> "
          },
          {
            "group": "Success 200",
            "type": "String",
            "optional": false,
            "field": "line.title",
            "description": "<p>A production line title</p> "
          },
          {
            "group": "Success 200",
            "type": "String",
            "optional": false,
            "field": "line.created_at",
            "description": "<p>A production line creation&#39;s timestamp</p> "
          },
          {
            "group": "Success 200",
            "type": "String",
            "optional": false,
            "field": "line.updated_at",
            "description": "<p>A production line modification&#39;s timestamp</p> "
          },
          {
            "group": "Success 200",
            "type": "object",
            "optional": false,
            "field": "process",
            "description": "<p>A process of an activity</p> "
          },
          {
            "group": "Success 200",
            "type": "Number",
            "optional": false,
            "field": "process.id",
            "description": "<p>A process identifier</p> "
          },
          {
            "group": "Success 200",
            "type": "String",
            "optional": false,
            "field": "process.title",
            "description": "<p>A process title</p> "
          },
          {
            "group": "Success 200",
            "type": "String",
            "optional": false,
            "field": "process.created_at",
            "description": "<p>A process creation&#39;s timestamp</p> "
          },
          {
            "group": "Success 200",
            "type": "String",
            "optional": false,
            "field": "process.updated_at",
            "description": "<p>A process modification&#39;s timestamp</p> "
          },
          {
            "group": "Success 200",
            "type": "object",
            "optional": false,
            "field": "product",
            "description": "<p>A product of an activity</p> "
          },
          {
            "group": "Success 200",
            "type": "Number",
            "optional": false,
            "field": "product.id",
            "description": "<p>A product identifier</p> "
          },
          {
            "group": "Success 200",
            "type": "String",
            "optional": false,
            "field": "product.title",
            "description": "<p>A product title</p> "
          },
          {
            "group": "Success 200",
            "type": "String",
            "optional": false,
            "field": "product.created_at",
            "description": "<p>A product creation&#39;s timestamp</p> "
          },
          {
            "group": "Success 200",
            "type": "String",
            "optional": false,
            "field": "product.updated_at",
            "description": "<p>A product modification&#39;s timestamp</p> "
          }
        ]
      },
      "examples": [
        {
          "title": "Success-Response:",
          "content": "HTTP/1.1 200 OK {\n  meta_data: {\n    request_params: {\n      qr_code: 'CAACEdEose0cBAHZAKguA0RWU9PGXjnyPqM8TAzMIVc28QaXsbV6k5fZAiTVmZCZBTIbJfWJpVRKukPGMOHbePn75QRSZAvaXS2k5qRpvzKyots4rV9V5fWcfIc8mT1o2sE8ftcG983XavclpXjupywiaCBOoagfFYiEWPmNRj8oyqdGax4sKdLjlPjhM2T9uZB96fJIZAyTKt8NTKhZBcQakEFSofHt3pVkZD'\n      line_id: 1, \n      product_id: 1, \n      process_id: 1, \n    },\n    errors: NULL,\n    success: 'Process Process 1 is sucessfully ended on production line Line 1 by I am engineer',\n    next_page: NULL,\n    last_page: NULL\n  },\n  data: {\n    id: 7,  \n    type: 'Start',                       \n    comment: 'Eeny, meeny, miny, moe',                       \n    created_at: '2000-01-01 01:00:00',  \n    updated_at: '2000-01-01 01:00:00',\n    user: {                            \n      id: 7,  \n      email: 'james.b@devsenses.com',  \n      first_name: 'James',  \n      last_name: 'Bond',  \n      last_login: '2015-01-01 08:00:00',  \n      last_logout: '2015-01-01 17:00:00', \n      persist_code: \"$2y$10$rwiLfqmPAp.0GepEUudRqONxxubl/Z.ATR24fR6WPQMm1mZcudq66\", \n      created_at: '2000-01-01 01:00:00',  \n      updated_at: '2000-01-01 01:00:00',   \n    }   \n    line: { \n      id: \"1\",\n      title: \"Line 1\",\n      created_at: \"2015-02-05 07:35:15\",\n      updated_at: \"2015-02-05 07:35:15\",                            \n    }  \n    process: { \n      id: \"1\",\n      title: \"Process 1\",\n      created_at: \"2015-02-05 07:35:15\",\n      updated_at: \"2015-02-05 07:35:15\",                            \n    } \n    product: { \n      id: \"1\",\n      title: \"Product 1\",\n      created_at: \"2015-02-05 07:35:15\",\n      updated_at: \"2015-02-05 07:35:15\",                            \n    } \n  }\n}",
          "type": "json"
        }
      ]
    },
    "error": {
      "examples": [
        {
          "title": "Error-Response:",
          "content": "HTTP/1.1 400 Bad Request {\n  meta_data: {\n    request_params: {\n      qr_code: fDAdfaFSFAfadfAE\n    },\n    errors: [\n      qr_code: 'Invalid QR code'\n    ],\n    success: NULL,\n    next_page: NULL,\n    last_page: NULL\n  },\n  data: NULL\n}",
          "type": "json"
        }
      ]
    }
  },
  {
    "type": "post",
    "url": "/activity/process/start",
    "title": "Start a process",
    "name": "Start_a_process",
    "group": "Activity",
    "version": "0.1.0",
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "qr_code",
            "description": "<p>A QR code retrieve by a scanner.</p> "
          },
          {
            "group": "Parameter",
            "type": "Number",
            "optional": false,
            "field": "line_id",
            "description": "<p>A production line a process is on.</p> "
          },
          {
            "group": "Parameter",
            "type": "Number",
            "optional": false,
            "field": "process_id",
            "description": "<p>A process of an activity.</p> "
          },
          {
            "group": "Parameter",
            "type": "Number",
            "optional": false,
            "field": "product_id",
            "description": "<p>A product of an activity.</p> "
          }
        ]
      }
    },
    "filename": "/var/www/api_docs/projects/minebea/example.js",
    "groupTitle": "Activity",
    "sampleRequest": [
      {
        "url": "http://staging-minebea.devsenses.net/api/activity/process/start"
      }
    ],
    "success": {
      "fields": {
        "Success 200": [
          {
            "group": "Success 200",
            "type": "Number",
            "optional": false,
            "field": "id",
            "description": "<p>An activity identifier</p> "
          },
          {
            "group": "Success 200",
            "type": "String",
            "optional": false,
            "field": "type",
            "description": "<p>A type of an activity</p> "
          },
          {
            "group": "Success 200",
            "type": "String",
            "optional": false,
            "field": "comment",
            "description": "<p>A comment for an end process activity (Start process activity does not have a comment)</p> "
          },
          {
            "group": "Success 200",
            "type": "String",
            "optional": false,
            "field": "created_at",
            "description": "<p>An activity creation&#39;s timestamp</p> "
          },
          {
            "group": "Success 200",
            "type": "String",
            "optional": false,
            "field": "updated_at",
            "description": "<p>An activity modification&#39;s timestamp</p> "
          },
          {
            "group": "Success 200",
            "type": "object",
            "optional": false,
            "field": "user",
            "description": "<p>A user who perform this activity</p> "
          },
          {
            "group": "Success 200",
            "type": "Number",
            "optional": false,
            "field": "user.id",
            "description": "<p>A user identifier</p> "
          },
          {
            "group": "Success 200",
            "type": "String",
            "optional": false,
            "field": "user.first_name",
            "description": "<p>A first name of a user</p> "
          },
          {
            "group": "Success 200",
            "type": "String",
            "optional": false,
            "field": "user.last_name",
            "description": "<p>A last name of a user</p> "
          },
          {
            "group": "Success 200",
            "type": "String",
            "optional": false,
            "field": "user.last_login",
            "description": "<p>A user login&#39;s timestamp</p> "
          },
          {
            "group": "Success 200",
            "type": "String",
            "optional": false,
            "field": "user.last_logout",
            "description": "<p>A user logout&#39;s timestamp</p> "
          },
          {
            "group": "Success 200",
            "type": "String",
            "optional": false,
            "field": "user.persist_code",
            "description": "<p>Does not have its used</p> "
          },
          {
            "group": "Success 200",
            "type": "String",
            "optional": false,
            "field": "user.created_at",
            "description": "<p>A user creation&#39;s timestamp</p> "
          },
          {
            "group": "Success 200",
            "type": "String",
            "optional": false,
            "field": "user.updated_at",
            "description": "<p>A user modification&#39;s timestamp</p> "
          },
          {
            "group": "Success 200",
            "type": "object",
            "optional": false,
            "field": "line",
            "description": "<p>A production line that an activity is on</p> "
          },
          {
            "group": "Success 200",
            "type": "Number",
            "optional": false,
            "field": "line.id",
            "description": "<p>A production line identifier</p> "
          },
          {
            "group": "Success 200",
            "type": "String",
            "optional": false,
            "field": "line.title",
            "description": "<p>A production line title</p> "
          },
          {
            "group": "Success 200",
            "type": "String",
            "optional": false,
            "field": "line.created_at",
            "description": "<p>A production line creation&#39;s timestamp</p> "
          },
          {
            "group": "Success 200",
            "type": "String",
            "optional": false,
            "field": "line.updated_at",
            "description": "<p>A production line modification&#39;s timestamp</p> "
          },
          {
            "group": "Success 200",
            "type": "object",
            "optional": false,
            "field": "process",
            "description": "<p>A process of an activity</p> "
          },
          {
            "group": "Success 200",
            "type": "Number",
            "optional": false,
            "field": "process.id",
            "description": "<p>A process identifier</p> "
          },
          {
            "group": "Success 200",
            "type": "String",
            "optional": false,
            "field": "process.title",
            "description": "<p>A process title</p> "
          },
          {
            "group": "Success 200",
            "type": "String",
            "optional": false,
            "field": "process.created_at",
            "description": "<p>A process creation&#39;s timestamp</p> "
          },
          {
            "group": "Success 200",
            "type": "String",
            "optional": false,
            "field": "process.updated_at",
            "description": "<p>A process modification&#39;s timestamp</p> "
          },
          {
            "group": "Success 200",
            "type": "object",
            "optional": false,
            "field": "product",
            "description": "<p>A product of an activity</p> "
          },
          {
            "group": "Success 200",
            "type": "Number",
            "optional": false,
            "field": "product.id",
            "description": "<p>A product identifier</p> "
          },
          {
            "group": "Success 200",
            "type": "String",
            "optional": false,
            "field": "product.title",
            "description": "<p>A product title</p> "
          },
          {
            "group": "Success 200",
            "type": "String",
            "optional": false,
            "field": "product.created_at",
            "description": "<p>A product creation&#39;s timestamp</p> "
          },
          {
            "group": "Success 200",
            "type": "String",
            "optional": false,
            "field": "product.updated_at",
            "description": "<p>A product modification&#39;s timestamp</p> "
          }
        ]
      },
      "examples": [
        {
          "title": "Success-Response:",
          "content": "HTTP/1.1 200 OK {\n  meta_data: {\n    request_params: {\n      qr_code: 'CAACEdEose0cBAHZAKguA0RWU9PGXjnyPqM8TAzMIVc28QaXsbV6k5fZAiTVmZCZBTIbJfWJpVRKukPGMOHbePn75QRSZAvaXS2k5qRpvzKyots4rV9V5fWcfIc8mT1o2sE8ftcG983XavclpXjupywiaCBOoagfFYiEWPmNRj8oyqdGax4sKdLjlPjhM2T9uZB96fJIZAyTKt8NTKhZBcQakEFSofHt3pVkZD'\n      line_id: 1, \n      product_id: 1, \n      process_id: 1, \n    },\n    errors: NULL,\n    success: 'Process Process 1 is sucessfully started on production line Line 1 by I am engineer',\n    next_page: NULL,\n    last_page: NULL\n  },\n  data: {\n    id: 7,  \n    type: 'Start',                       \n    comment: NULL,                       \n    created_at: '2000-01-01 01:00:00',  \n    updated_at: '2000-01-01 01:00:00',\n    user: {                            \n      id: 7,  \n      email: 'james.b@devsenses.com',  \n      first_name: 'James',  \n      last_name: 'Bond',  \n      last_login: '2015-01-01 08:00:00',  \n      last_logout: '2015-01-01 17:00:00', \n      persist_code: \"$2y$10$rwiLfqmPAp.0GepEUudRqONxxubl/Z.ATR24fR6WPQMm1mZcudq66\", \n      created_at: '2000-01-01 01:00:00',  \n      updated_at: '2000-01-01 01:00:00',   \n    }   \n    line: { \n      id: \"1\",\n      title: \"Line 1\",\n      created_at: \"2015-02-05 07:35:15\",\n      updated_at: \"2015-02-05 07:35:15\",                            \n    }  \n    process: { \n      id: \"1\",\n      title: \"Process 1\",\n      created_at: \"2015-02-05 07:35:15\",\n      updated_at: \"2015-02-05 07:35:15\",                            \n    } \n    product: { \n      id: \"1\",\n      title: \"Product 1\",\n      created_at: \"2015-02-05 07:35:15\",\n      updated_at: \"2015-02-05 07:35:15\",                            \n    } \n  }\n}",
          "type": "json"
        }
      ]
    },
    "error": {
      "examples": [
        {
          "title": "Error-Response:",
          "content": "HTTP/1.1 400 Bad Request {\n  meta_data: {\n    request_params: {\n      qr_code: fDAdfaFSFAfadfAE\n    },\n    errors: [\n      qr_code: 'Invalid QR code'\n    ],\n    success: NULL,\n    next_page: NULL,\n    last_page: NULL\n  },\n  data: NULL\n}",
          "type": "json"
        }
      ]
    }
  },
  {
    "type": "get",
    "url": "/document/{document_id}/download",
    "title": "Download a document",
    "name": "Download_a_document",
    "group": "Document",
    "version": "0.1.0",
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "qr_code",
            "description": "<p>A QR code retrieve by a scanner.</p> "
          }
        ]
      }
    },
    "filename": "/var/www/api_docs/projects/minebea/example.js",
    "groupTitle": "Document",
    "sampleRequest": [
      {
        "url": "http://staging-minebea.devsenses.net/api/document/{document_id}/download"
      }
    ],
    "error": {
      "examples": [
        {
          "title": "Error-Response:",
          "content": "HTTP/1.1 400 Bad Request {\n  meta_data: {\n    request_params: {\n      qr_code: fDAdfaFSFAfadfAE\n    },\n    errors: [\n      qr_code: 'Invalid QR code'\n    ],\n    success: NULL,\n    next_page: NULL,\n    last_page: NULL\n  },\n  data: NULL\n}",
          "type": "json"
        }
      ]
    }
  },
  {
    "type": "get",
    "url": "/document-category",
    "title": "Get a list of document category and its documents",
    "name": "Get_a_list_of_document_category_and_its_documents",
    "group": "Document_category",
    "version": "0.1.0",
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "qr_code",
            "description": "<p>A QR code retrieve by a scanner.</p> "
          },
          {
            "group": "Parameter",
            "type": "Number",
            "optional": false,
            "field": "line_id",
            "description": "<p>A line identifier.</p> "
          },
          {
            "group": "Parameter",
            "type": "Number",
            "optional": false,
            "field": "product_id",
            "description": "<p>A model identifier.</p> "
          },
          {
            "group": "Parameter",
            "type": "Number",
            "optional": false,
            "field": "process_id",
            "description": "<p>A process identifier.</p> "
          }
        ]
      }
    },
    "filename": "/var/www/api_docs/projects/minebea/example.js",
    "groupTitle": "Document_category",
    "sampleRequest": [
      {
        "url": "http://staging-minebea.devsenses.net/api/document-category"
      }
    ],
    "success": {
      "fields": {
        "Success 200": [
          {
            "group": "Success 200",
            "type": "Number",
            "optional": false,
            "field": "id",
            "description": "<p>A document category identifier</p> "
          },
          {
            "group": "Success 200",
            "type": "String",
            "optional": false,
            "field": "title",
            "description": "<p>A document category title</p> "
          },
          {
            "group": "Success 200",
            "type": "String",
            "optional": false,
            "field": "created_at",
            "description": "<p>A document creation&#39;s timestamp</p> "
          },
          {
            "group": "Success 200",
            "type": "String",
            "optional": false,
            "field": "updated_at",
            "description": "<p>A document modification&#39;s timestamp</p> "
          },
          {
            "group": "Success 200",
            "type": "object",
            "optional": false,
            "field": "document",
            "description": "<p>A list of documents which belong to a document category</p> "
          },
          {
            "group": "Success 200",
            "type": "String",
            "optional": false,
            "field": "document.title",
            "description": "<p>A document title</p> "
          },
          {
            "group": "Success 200",
            "type": "String",
            "optional": false,
            "field": "document.created_at",
            "description": "<p>A document creation&#39;s timestamp</p> "
          },
          {
            "group": "Success 200",
            "type": "String",
            "optional": false,
            "field": "document.updated_at",
            "description": "<p>A document modification&#39;s timestamp</p> "
          },
          {
            "group": "Success 200",
            "type": "String",
            "optional": false,
            "field": "document.download_url",
            "description": "<p>A url for download a document</p> "
          }
        ]
      },
      "examples": [
        {
          "title": "Success-Response:",
          "content": "HTTP/1.1 200 OK {\n  meta_data: {\n    request_params: {\n      qr_code: 'CAACEdEose0cBAHZAKguA0RWU9PGXjnyPqM8TAzMIVc28QaXsbV6k5fZAiTVmZCZBTIbJfWJpVRKukPGMOHbePn75QRSZAvaXS2k5qRpvzKyots4rV9V5fWcfIc8mT1o2sE8ftcG983XavclpXjupywiaCBOoagfFYiEWPmNRj8oyqdGax4sKdLjlPjhM2T9uZB96fJIZAyTKt8NTKhZBcQakEFSofHt3pVkZD'\n      line_id: 1, \n      process_id: 1, \n      product_id: 1, \n    },\n    errors: NULL,\n    success: 'Successfully retrieve a list of documents for process id 1, model id 1',\n    next_page: NULL,\n    last_page: NULL\n  },\n  data: [\n    0: {\n      id: 1\n      title: \"PI\"\n      created_at: \"2015-02-05 12:44:48\"\n      updated_at: \"2015-02-06 10:21:28\"  \n      documents: [\n        0: {\n          id: 1,\n          title: \"PI-1\",  \n          created_at: \"2015-02-05 12:44:48\"\n          updated_at: \"2015-02-06 10:21:28\" \n          download_url: \"http://staging-minebea.devsenses.net/api/document/1/download\" \n        }\n        1: {\n          id: 2,\n          title: \"PI-2\",  \n          created_at: \"2015-02-05 12:44:48\"\n          updated_at: \"2015-02-06 10:21:28\" \n          download_url: \"http://staging-minebea.devsenses.net/api/document/2/download\" \n        }\n      ]                                                                         \n    }\n    1: {                                                                       \n      id: 2\n      title: \"RE\"\n      created_at: \"2015-02-05 12:44:48\"\n      updated_at: \"2015-02-06 10:21:28\"  \n      documents: [\n        0: {\n          id: 3,\n          title: \"RE-1\",  \n          created_at: \"2015-02-05 12:44:48\"\n          updated_at: \"2015-02-06 10:21:28\" \n          download_url: \"http://staging-minebea.devsenses.net/api/document/3/download\" \n        }\n        1: {\n          id: 4,\n          title: \"RE-2\",  \n          created_at: \"2015-02-05 12:44:48\"\n          updated_at: \"2015-02-06 10:21:28\" \n          download_url: \"http://staging-minebea.devsenses.net/api/document/4/download\" \n        }\n      ] \n    }\n    2: {                                                                                                                                       \n      id: 3\n      title: \"PI-PR\"\n      created_at: \"2015-02-05 12:44:48\"\n      updated_at: \"2015-02-06 10:21:28\"  \n      documents: [\n        0: {\n          id: 5,\n          title: \"PI-PR-1\",  \n          created_at: \"2015-02-05 12:44:48\"\n          updated_at: \"2015-02-06 10:21:28\" \n          download_url: \"http://staging-minebea.devsenses.net/api/document/5/download\" \n        }\n        1: {\n          id: 6,\n          title: \"PI-PR-2\",  \n          created_at: \"2015-02-05 12:44:48\"\n          updated_at: \"2015-02-06 10:21:28\" \n          download_url: \"http://staging-minebea.devsenses.net/api/document/6/download\" \n        }\n      ] \n    }\n    3: {                                                                                                                                                                                                  \n      id: 4\n      title: \"PI-SET\"\n      created_at: \"2015-02-05 12:44:48\"\n      updated_at: \"2015-02-06 10:21:28\"  \n      documents: [\n        0: {\n          id: 7,\n          title: \"PI-SET-1\",  \n          created_at: \"2015-02-05 12:44:48\"\n          updated_at: \"2015-02-06 10:21:28\" \n          download_url: \"http://staging-minebea.devsenses.net/api/document/7/download\" \n        }\n        1: {\n          id: 8,\n          title: \"PI-SET-2\",  \n          created_at: \"2015-02-05 12:44:48\"\n          updated_at: \"2015-02-06 10:21:28\" \n          download_url: \"http://staging-minebea.devsenses.net/api/document/8/download\" \n        }\n      ] \n    }\n  ]\n}",
          "type": "json"
        }
      ]
    },
    "error": {
      "examples": [
        {
          "title": "Error-Response:",
          "content": "HTTP/1.1 400 Bad Request {\n  meta_data: {\n    request_params: {\n      qr_code: fDAdfaFSFAfadfAE\n    },\n    errors: [\n      qr_code: 'Invalid QR code'\n    ],\n    success: NULL,\n    next_page: NULL,\n    last_page: NULL\n  },\n  data: NULL\n}",
          "type": "json"
        }
      ]
    }
  },
  {
    "type": "post",
    "url": "/process/check-status",
    "title": "Check process status",
    "name": "Return_process_status_and_availibity_for_a_current_logged_in_user",
    "group": "Process",
    "version": "0.1.0",
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "qr_code",
            "description": "<p>A QR code retrieve by a scanner.</p> "
          },
          {
            "group": "Parameter",
            "type": "Number",
            "optional": false,
            "field": "line_id",
            "description": "<p>A id of a production line a process is on.</p> "
          },
          {
            "group": "Parameter",
            "type": "Number",
            "optional": false,
            "field": "product_id",
            "description": "<p>A model id of an activity.</p> "
          },
          {
            "group": "Parameter",
            "type": "Number",
            "optional": false,
            "field": "process_id",
            "description": "<p>A process id of an activity.</p> "
          }
        ]
      }
    },
    "filename": "/var/www/api_docs/projects/minebea/example.js",
    "groupTitle": "Process",
    "sampleRequest": [
      {
        "url": "http://staging-minebea.devsenses.net/api/process/check-status"
      }
    ],
    "success": {
      "fields": {
        "Success 200": [
          {
            "group": "Success 200",
            "type": "Number",
            "optional": false,
            "field": "id",
            "description": "<p>A process identifier</p> "
          },
          {
            "group": "Success 200",
            "type": "String",
            "optional": false,
            "field": "title",
            "description": "<p>A process title</p> "
          },
          {
            "group": "Success 200",
            "type": "String",
            "optional": false,
            "field": "created_at",
            "description": "<p>A process creation&#39;s timestamp</p> "
          },
          {
            "group": "Success 200",
            "type": "String",
            "optional": false,
            "field": "updated_at",
            "description": "<p>A process modification&#39;s timestamp</p> "
          },
          {
            "group": "Success 200",
            "type": "Number",
            "optional": false,
            "field": "status",
            "description": "<p>Indicate whether a process is start or stop. 0 = Stop, 1 = Start</p> "
          },
          {
            "group": "Success 200",
            "type": "Boolean",
            "optional": false,
            "field": "available",
            "description": "<p>Indicate whether a process can be operated by a current logged-in user or not.</p> "
          }
        ]
      },
      "examples": [
        {
          "title": "Success-Response:",
          "content": "HTTP/1.1 200 OK {\n  meta_data: {\n    request_params: {\n      qr_code: 'CAACEdEose0cBAHZAKguA0RWU9PGXjnyPqM8TAzMIVc28QaXsbV6k5fZAiTVmZCZBTIbJfWJpVRKukPGMOHbePn75QRSZAvaXS2k5qRpvzKyots4rV9V5fWcfIc8mT1o2sE8ftcG983XavclpXjupywiaCBOoagfFYiEWPmNRj8oyqdGax4sKdLjlPjhM2T9uZB96fJIZAyTKt8NTKhZBcQakEFSofHt3pVkZD'\n      line_id: 1, \n      product_id: 1, \n      process_id: 1, \n    },\n    errors: NULL,\n    success: 'Successfully retrieve the status of process Process 1',\n    next_page: NULL,\n    last_page: NULL\n  },\n  data: {\n    id: \"1\",\n    title: \"Process 1\",\n    created_at: \"2015-02-05 07:35:15\",\n    updated_at: \"2015-02-05 07:35:15\",                            \n    status: 0,                            \n    available: true,\n  }\n}",
          "type": "json"
        }
      ]
    },
    "error": {
      "examples": [
        {
          "title": "Error-Response:",
          "content": "HTTP/1.1 400 Bad Request {\n  meta_data: {\n    request_params: {\n      qr_code: fDAdfaFSFAfadfAE\n    },\n    errors: [\n      qr_code: 'Invalid QR code'\n    ],\n    success: NULL,\n    next_page: NULL,\n    last_page: NULL\n  },\n  data: NULL\n}",
          "type": "json"
        }
      ]
    }
  },
  {
    "type": "post",
    "url": "/user/login",
    "title": "User login",
    "name": "User_login",
    "group": "User",
    "version": "0.1.0",
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "qr_code",
            "description": "<p>A QR code retrieve by a scanner.</p> "
          }
        ]
      }
    },
    "filename": "/var/www/api_docs/projects/minebea/example.js",
    "groupTitle": "User",
    "sampleRequest": [
      {
        "url": "http://staging-minebea.devsenses.net/api/user/login"
      }
    ],
    "success": {
      "fields": {
        "Success 200": [
          {
            "group": "Success 200",
            "type": "Number",
            "optional": false,
            "field": "id",
            "description": "<p>A user identifier</p> "
          },
          {
            "group": "Success 200",
            "type": "String",
            "optional": false,
            "field": "first_name",
            "description": "<p>A first name of a user</p> "
          },
          {
            "group": "Success 200",
            "type": "String",
            "optional": false,
            "field": "last_name",
            "description": "<p>A last name of a user</p> "
          },
          {
            "group": "Success 200",
            "type": "String",
            "optional": false,
            "field": "last_login",
            "description": "<p>A user login&#39;s timestamp</p> "
          },
          {
            "group": "Success 200",
            "type": "String",
            "optional": false,
            "field": "last_logout",
            "description": "<p>A user logout&#39;s timestamp</p> "
          },
          {
            "group": "Success 200",
            "type": "String",
            "optional": false,
            "field": "persist_code",
            "description": "<p>Does not have its used</p> "
          },
          {
            "group": "Success 200",
            "type": "String",
            "optional": false,
            "field": "created_at",
            "description": "<p>A user creation&#39;s timestamp</p> "
          },
          {
            "group": "Success 200",
            "type": "String",
            "optional": false,
            "field": "updated_at",
            "description": "<p>A user modification&#39;s timestamp</p> "
          },
          {
            "group": "Success 200",
            "type": "object[]",
            "optional": false,
            "field": "groups",
            "description": "<p>A list of user roles</p> "
          },
          {
            "group": "Success 200",
            "type": "Number",
            "optional": false,
            "field": "groups.id",
            "description": "<p>A role identifier</p> "
          },
          {
            "group": "Success 200",
            "type": "String",
            "optional": false,
            "field": "groups.title",
            "description": "<p>A role title</p> "
          },
          {
            "group": "Success 200",
            "type": "String",
            "optional": false,
            "field": "groups.created_at",
            "description": "<p>A role creation&#39;s timestamp</p> "
          },
          {
            "group": "Success 200",
            "type": "String",
            "optional": false,
            "field": "groups.updated_at",
            "description": "<p>A role modification&#39;s timestamp</p> "
          },
          {
            "group": "Success 200",
            "type": "object[]",
            "optional": false,
            "field": "models",
            "description": "<p>A list of models which a user is allowed to work on</p> "
          },
          {
            "group": "Success 200",
            "type": "Number",
            "optional": false,
            "field": "models.id",
            "description": "<p>A model identifier</p> "
          },
          {
            "group": "Success 200",
            "type": "String",
            "optional": false,
            "field": "models.title",
            "description": "<p>A model title</p> "
          },
          {
            "group": "Success 200",
            "type": "String",
            "optional": false,
            "field": "models.created_at",
            "description": "<p>A model creation&#39;s timestamp</p> "
          },
          {
            "group": "Success 200",
            "type": "String",
            "optional": false,
            "field": "models.updated_at",
            "description": "<p>A model modification&#39;s timestamp</p> "
          },
          {
            "group": "Success 200",
            "type": "object[]",
            "optional": false,
            "field": "models.lines",
            "description": "<p>A list of production lines of a model</p> "
          },
          {
            "group": "Success 200",
            "type": "Number",
            "optional": false,
            "field": "models.lines.id",
            "description": "<p>A production lines identifier</p> "
          },
          {
            "group": "Success 200",
            "type": "String",
            "optional": false,
            "field": "models.lines.title",
            "description": "<p>A production lines title</p> "
          },
          {
            "group": "Success 200",
            "type": "String",
            "optional": false,
            "field": "models.lines.created_at",
            "description": "<p>A production lines creation&#39;s timestamp</p> "
          },
          {
            "group": "Success 200",
            "type": "String",
            "optional": false,
            "field": "models.lines.updated_at",
            "description": "<p>A production lines modification&#39;s timestamp</p> "
          },
          {
            "group": "Success 200",
            "type": "object[]",
            "optional": false,
            "field": "models.lines.processes",
            "description": "<p>A list of processes of a production lines</p> "
          },
          {
            "group": "Success 200",
            "type": "Number",
            "optional": false,
            "field": "models.lines.processes.id",
            "description": "<p>A process identifier</p> "
          },
          {
            "group": "Success 200",
            "type": "String",
            "optional": false,
            "field": "models.lines.processes.title",
            "description": "<p>A process title</p> "
          },
          {
            "group": "Success 200",
            "type": "String",
            "optional": false,
            "field": "models.lines.processes.created_at",
            "description": "<p>A process creation&#39;s timestamp</p> "
          },
          {
            "group": "Success 200",
            "type": "String",
            "optional": false,
            "field": "models.lines.processes.updated_at",
            "description": "<p>A process modification&#39;s timestamp</p> "
          }
        ]
      },
      "examples": [
        {
          "title": "Success-Response:",
          "content": "HTTP/1.1 200 OK {\n  meta_data: {\n    request_params: {\n      qr_code: 'CAACEdEose0cBAHZAKguA0RWU9PGXjnyPqM8TAzMIVc28QaXsbV6k5fZAiTVmZCZBTIbJfWJpVRKukPGMOHbePn75QRSZAvaXS2k5qRpvzKyots4rV9V5fWcfIc8mT1o2sE8ftcG983XavclpXjupywiaCBOoagfFYiEWPmNRj8oyqdGax4sKdLjlPjhM2T9uZB96fJIZAyTKt8NTKhZBcQakEFSofHt3pVkZD'\n    },\n    errors: NULL,\n    success: 'You are successfully logged-in',\n    next_page: NULL,\n    last_page: NULL\n  },\n  data: {\n    id: 7,  \n    email: 'james.b@devsenses.com',  \n    first_name: 'James',  \n    last_name: 'Bond',  \n    last_login: '2015-01-01 08:00:00',  \n    last_logout: '2015-01-01 17:00:00', \n    persist_code: \"$2y$10$rwiLfqmPAp.0GepEUudRqONxxubl/Z.ATR24fR6WPQMm1mZcudq66\", \n    created_at: '2000-01-01 01:00:00',  \n    updated_at: '2000-01-01 01:00:00',  \n    groups: [\n      0: {\n        id: 1,\n        title: 'Product engineer', \n        created_at: '2000-01-01 01:00:00',  \n        updated_at: '2000-01-01 01:00:00'\n      },\n      1: {\n        id: 2,\n        title: 'Secret agent',\n        created_at: '2000-01-01 01:00:00',  \n        updated_at: '2000-01-01 01:00:00'\n      }\n    ] \n    models: [     \n      0: {\n        id: 1,\n        title: 'ASTON MARTIN - V8 VANTAGE', \n        created_at: '2000-01-01 01:00:00',  \n        updated_at: '2000-01-01 01:00:00',\n        lines: [\n          0: {\n            id: 1,\n            title: 'Line no. 1',   \n            created_at: '2000-01-01 01:00:00',  \n            updated_at: '2000-01-01 01:00:00'\n            processes: [\n              0: {                          \n                id: 1,\n                title: 'Process no. 1',   \n                created_at: '2000-01-01 01:00:00',  \n                updated_at: '2000-01-01 01:00:00'\n              }\n              1: {                          \n                id: 2,\n                title: 'Process no. 2',   \n                created_at: '2000-01-01 01:00:00',  \n                updated_at: '2000-01-01 01:00:00'\n              }\n            ]\n          }\n        ], \n      },\n      1: {\n        id: 2,\n        title: 'Jetpack',\n        created_at: '2000-01-01 01:00:00',  \n        updated_at: '2000-01-01 01:00:00',\n        lines: [\n          0: {\n            id: 1,\n            title: 'Line no. 1',   \n            created_at: '2000-01-01 01:00:00',  \n            updated_at: '2000-01-01 01:00:00'\n            processes: [\n              0: {                          \n                id: 1,\n                title: 'Process no. 1',   \n                created_at: '2000-01-01 01:00:00',  \n                updated_at: '2000-01-01 01:00:00'\n              }\n              1: {                          \n               id: 2,\n               title: 'Process no. 2',   \n                created_at: '2000-01-01 01:00:00',  \n                updated_at: '2000-01-01 01:00:00'\n              }\n            ]\n          }\n        ],\n      }\n    ] \n  }\n}",
          "type": "json"
        }
      ]
    },
    "error": {
      "examples": [
        {
          "title": "Error-Response:",
          "content": "HTTP/1.1 400 Bad Request {\n  meta_data: {\n    request_params: {\n      qr_code: fDAdfaFSFAfadfAE\n    },\n    errors: [\n      qr_code: 'Invalid QR code'\n    ],\n    success: NULL,\n    next_page: NULL,\n    last_page: NULL\n  },\n  data: NULL\n}",
          "type": "json"
        }
      ]
    }
  }
] });